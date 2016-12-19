<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Cawa\ImageModule\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Brightness implements FilterInterface
{
    /**
     * @var int
     */
    private $brightness;

    /**
     * @param int $brightness
     */
    public function __construct(int $brightness)
    {
        $this->brightness = $brightness;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        return $image
           ->brightness($this->brightness);
    }
}
