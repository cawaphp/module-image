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

class Fit implements FilterInterface
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var string
     */
    private $position;

    /**
     * @param int $width
     * @param int $height
     * @param string $position
     */
    public function __construct(int $width = null, int $height = null, string $position = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        $width = $this->width;
        $height = $this->height;

        if (!$height) {
            $height = round($width * $image->height() / $image->width());
        }

        if (!$width) {
            $width = round($height * $image->width() / $image->height());
        }

        return $image->fit($width, $height, null, $this->position);
    }
}
