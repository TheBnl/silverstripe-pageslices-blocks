<?php

namespace Broarm\PageSlices;

use Broarm\PageSlices\Block\Block;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\ManyManyList;

/**
 * Class BlockSlice
 *
 * @author Bram de Leeuw
 * @package Broarm\PageSlices
 *
 * @method ManyManyList BlockSliceBlock()
 */
class BlockSlice extends PageSlice
{
    private static $table_name = 'BlockSlice';

    private static $defaults = [
        'Title' => 'Block slice'
    ];

    private static $many_many = [
        'BlockSliceBlock' => Block::class
    ];

    private static $many_many_extraFields = [
        'BlockSliceBlock' => [
            'Sort' => 'Int'
        ]
    ];

    private static $slice_image = 'bramdeleeuw/silverstripe-pageslices_blocks:client/images/BlockSlice.png';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if ($this->exists()) {
            $fields->addFieldsToTab('Root.Main', [
                GridField::create('Blocks', 'Blocks', $this->BlockSliceBlock(), BlocksGridFieldConfig::create())
            ]);
        } else {
            $message = _t(__CLASS__ . 'SaveNeeded', 'You need to save before you can add blocks');
            $fields->insertBefore(
                'Title',
                LiteralField::create('Warning', "<p class='message notice'>{$message}</p>")
            );
        }

        return $fields;
    }
}
