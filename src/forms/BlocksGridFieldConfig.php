<?php

namespace Broarm\PageSlices;

use Broarm\PageSlices\Block\Block;
use Heyday\GridFieldVersionedOrderableRows\GridFieldVersionedOrderableRows;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Versioned\VersionedGridFieldState\VersionedGridFieldState;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;


/**
 * Class BlocksGridFieldConfig
 *
 * @package Broarm\PageSlices
 */
class BlocksGridFieldConfig extends GridFieldConfig_RecordEditor
{
    /**
     * BlocksGridFieldConfig constructor.
     *
     * @param array  $availableClasses
     * @param int    $itemsPerPage
     * @param string $sortField
     */
    public function __construct($availableClasses = array(), $itemsPerPage = null, $sortField = 'Sort')
    {
        parent::__construct($itemsPerPage = null);
        $this->removeComponentsByType(new GridFieldAddNewButton());
        $this->addComponent(new GridFieldVersionedOrderableRows($sortField));
        $this->addComponent($multiClassComponent = new GridFieldAddNewMultiClass('buttons-before-left'));
        $multiClassComponent->setClasses($availableClasses);
    }
}
