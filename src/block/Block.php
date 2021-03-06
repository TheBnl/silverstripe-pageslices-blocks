<?php

namespace Broarm\PageSlices\Block;

use Exception;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

/**
 * Class Block
 *
 * @author Bram de Leeuw
 * @package Broarm\PageSlices\Block
 *
 * @property string Title
 * @method SiteTree Parent
 */
class Block extends DataObject
{
    private static $table_name = 'PageSlicesBlock';

    private static $db = [
        'Title' => 'Varchar(255)'
    ];

    private static $summary_fields = [
        'getBlockImage' => 'Type',
        'getBlockType' => 'Type Name',
        'Title' => 'Title'
    ];

    private static $searchable_fields = [
        'Title'
    ];

    private static $translate = [
        'Title'
    ];

    private static $extensions = [
        Versioned::class
    ];

    private static $block_image = 'bramdeleeuw/silverstripe-pageslices-blocks:client/images/Block.png';

    /**
     * @var BlockController
     */
    protected $controller;

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if ($this->ClassName === Block::class) {
            $fields->addFieldsToTab('Root.Main', [
                DropdownField::create('ClassName', _t(__CLASS__ . 'ClassName', 'Block type'), self::getAvailableBlocks())
                    ->setEmptyString(_t(__CLASS__ . 'ClassNameEmpty', 'Select block type'))
            ]);
        }

        return $fields;
    }

    public function validate()
    {
        $validation = parent::validate();
        if ($validation->isValid() && $this->ClassName === Block::class) {
            $validation->addError('Select a proper block type');
        }

        return $validation;
    }

    /**
     * If this block holds has_many content
     * on duplicate copy the content over
     *
     * @param Block $block
     */
    public function onAfterDuplicate(Block $block)
    {
        // Check if there are relations set
        // Loop over each set relation
        // Copy all items in the relation over to the new object
        if ($hasManyRelations = $block->data()->hasMany()) {
            foreach ($hasManyRelations as $relation => $class) {
                foreach ($block->$relation() as $object) {
                    /** @var DataObject $object */
                    $copy = $object->duplicate(true);
                    $this->$relation()->add($copy);
                }
            }
        }
    }

    /**
     * Return the translated ClassName
     *
     * @return string
     */
    public function getBlockType()
    {
        $singularName = explode('\\', $this->i18n_singular_name());
        return end($singularName);
    }

    /**
     * Return a nice css name
     *
     * @return string
     */
    public function getCSSName()
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $this->getClassName()));
    }

    /**
     * Return the path to the section image
     *
     * @return string
     */
    public function getBlockImage()
    {
        $image = ModuleResourceLoader::resourceURL(self::config()->get('block_image'));
        return LiteralField::create(
            'BlockImage',
            "<img src='$image' title='{$this->getBlockType()}' alt='{$this->getBlockType()}' width='50'>"
        );
    }

    /**
     * @throws Exception
     * @return BlockController
     */
    public function getController()
    {
        if ($this->controller) {
            return $this->controller;
        }

        $controllerClass = null;
        foreach (array_reverse(ClassInfo::ancestry($this->getClassName())) as $sliceClass) {
            $controllerClass = "{$sliceClass}_Controller";
            if (class_exists($controllerClass)) {
                break;
            }

            $controllerClass = "{$sliceClass}Controller";
            if (class_exists($controllerClass)) {
                break;
            }
        }

        if (!class_exists($controllerClass)) {
            throw new Exception("Could not find controller class for {$this->getClassName()}");
        }

        $this->controller = Injector::inst()->create($controllerClass, $this);
        return $this->controller;
    }

    /**
     * Remove the add new button from the utility list
     * Because of the multi class, add new would create a new base class that should not be used
     * (Could be replaced with an add new multi class button)
     *
     * @return mixed
     */
    public function getBetterButtonsUtils()
    {
        $fields = parent::getBetterButtonsUtils();
        $fields->removeByName('action_doNew');
        return $fields;
    }

    /**
     * Get the available block types
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getAvailableBlocks()
    {
        $availableClasses = ClassInfo::subclassesFor(Block::class);
        array_shift($availableClasses);

        return array_map(function ($class) {
            return $class::singleton()->getBlockType();
        }, array_combine($availableClasses, $availableClasses));
    }
}
