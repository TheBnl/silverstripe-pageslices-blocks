<?php

namespace Broarm\PageSlices;

use Broarm\PageSlices\Block\Block;
use SilverStripe\ORM\ArrayList;

/**
 * class BlockSliceController
 * @package Broarm\PageSlices
 * @mixin BlockSlice
 */
class BlockSliceController extends PageSliceController
{
    private static $allowed_actions = [];

    public function init()
    {
        parent::init();
    }

    /**
     * Get the block controllers
     *
     * @return ArrayList
     * @throws \Exception
     */
    public function getBlocks()
    {
        $controllers = ArrayList::create();
        $blocks = $this->BlockSliceBlock()->Sort('Sort ASC');
        
        
        if ($blocks) {
            /** @var Block $block */
            foreach ($blocks as $block) {
                $controller = $block->getController();
                $controller->setSlice($this);
                $controller->init();
                $controllers->push($controller);
            }
            return $controllers;
        }

        return $controllers;
    }
}