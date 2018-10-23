<?php

namespace Broarm\PageSlices\Block;

use Broarm\PageSlices\BlockSliceController;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Flushable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Versioned\Versioned;

/**
 * Class BlockController
 *
 * @author Bram de Leeuw
 * @package Broarm\PageSlices\Block
 *
 * @mixin Block
 */
class BlockController extends Controller implements Flushable
{
    /**
     * @var Block
     */
    protected $block;

    /**
     * @var BlockSliceController
     */
    protected $slice;

    /**
     * Overwrite this setting on your subclass
     * to disable caching on a per slice basis
     *
     * @var boolean
     */
    protected $useCaching = true;

    /**
     * Turn the caching feature on/off
     *
     * @var boolean
     */
    private static $enable_cache = false;

    /**
     * @var array
     */
    private static $allowed_actions = array();

    /**
     * @param Block $block
     */
    public function __construct($block = null)
    {
        if ($block) {
            $this->block = $block;
            $this->failover = $block;
        }

        parent::__construct();
    }

    /**
     * Trigger the on after init here because we don't have a request handler on the page slice controller
     */
    public function init()
    {
        parent::init();
        $this->extend('onAfterInit');
    }

    /**
     * @param string $action
     *
     * @return string
     */
    public function Link($action = null)
    {
        $id = ($this->block) ? $this->block->ID : null;
        $segment = Controller::join_links('block', $id, $action);

        if ($page = Director::get_current_page()) {
            return $page->Link($segment);
        }

        return Controller::curr()->Link($segment);
    }

    /**
     * Get the parent Controller
     *
     * @return Controller
     */
    public function Parent()
    {
        return Controller::curr();
    }

    /**
     * @return Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @return BlockSliceController
     */
    public function getSlice()
    {
        return $this->slice;
    }

    /**
     * @param BlockSliceController $slice
     */
    public function setSlice($slice)
    {
        $this->slice = $slice;
    }

    /**
     * Check if the caching featured is turned on and enabled for this slice
     *
     * @return bool
     */
    public function useCaching()
    {
        return $this->useCaching && self::config()->get('enable_cache');
    }

    /**
     * The Cache key with basis properties
     * Extend this on your subclass for more specific properties
     *
     * @return string
     */
    public function getCacheKey()
    {
        $cacheKey = implode('_', array(
            $this->ID,
            strtotime($this->LastEdited),
            strtotime($this->Parent()->LastEdited),
            Versioned::get_reading_mode()
        ));
        $this->extend('updateCacheKey', $cacheKey);
        return $cacheKey;
    }

    /**
     * @return CacheInterface
     */
    public static function cache()
    {
        return Injector::inst()->get(CacheInterface::class . '.PageSliceBlocks');
    }

    /**
     * Flush the caches
     */
    public static function flush()
    {
        self::cache()->clear();
    }

    /**
     * Return the rendered template
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getTemplate()
    {
        if (!$this->useCaching()) {
            $result = $this->renderTemplate();
        } else {
            $cache = self::cache();
            if (!$cache->has($this->getCacheKey())) {
                $result = $this->renderTemplate();
                $cache->set($this->getCacheKey(), $result);
            } else {
                try {
                    $result = $cache->get($this->getCacheKey());
                } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
                    $result = $this->renderTemplate();
                }
            }
        }

        return $result;
    }

    public function renderTemplate()
    {
        return $this->renderWith(array_reverse(ClassInfo::ancestry($this->getClassName())));
    }
}
