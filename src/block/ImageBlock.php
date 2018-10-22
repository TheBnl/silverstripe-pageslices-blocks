<?php

namespace Broarm\PageSlices\Block;

use SilverStripe\Assets\Image;

/**
 * Class TextBlock
 *
 * @author Bram de Leeuw
 *
 * @property string Caption
 * @method Image Image()
 */
class ImageBlock extends Block
{
    private static $table_name = 'ImageBlock';

    private static $db = [
        'Caption' => 'Varchar'
    ];

    private static $has_one = [
        'Image' => Image::class
    ];

    private static $block_image = 'bramdeleeuw/silverstripe-pageslices-blocks:client/images/ImageBlock.png';
}