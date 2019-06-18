<?php

namespace Broarm\PageSlices;

use Broarm\PageSlices\Block\Block;
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
class BlocksGridFieldConfig extends GridFieldConfig
{

    /**
     * BlocksGridFieldConfig constructor.
     *
     * @param array  $availableClasses
     * @param int    $itemsPerPage
     * @param string $sortField
     */
    public function __construct($availableClasses = array(), $itemsPerPage = 999, $sortField = 'Sort')
    {
        parent::__construct();
        $this->addComponent(new GridFieldTitleHeader());
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new VersionedGridFieldState());
        $this->addComponent(new GridFieldOrderableRows($sortField));
        $this->addComponent(new GridFieldDetailForm());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent($multiClassComponent = new GridFieldAddNewMultiClass());
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));

        $multiClassComponent->setClasses(Block::getAvailableBlocks());
        $pagination->setThrowExceptionOnBadDataType(false);
    }
}
