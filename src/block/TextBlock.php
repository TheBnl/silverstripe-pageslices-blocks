<?php

namespace Broarm\PageSlices\Block;

/**
 * Class TextBlock
 *
 * @author Bram de Leeuw
 */
class TextBlock extends Block
{
    private static $table_name = 'PageSlicesTextBlock';

    private static $db = [
        'Content' => 'HTMLText'
    ];

    private static $block_image = 'bramdeleeuw/silverstripe-pageslices-blocks:client/images/TextBlock.png';
}