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

class Modulate implements FilterInterface
{
    /**
     * @var int
     */
    private $brightness;

    /**
     * @var int
     */
    private $saturation;

    /**
     * @var int
     */
    private $hue;

    /**
     * @param int $brightness
     * @param int $saturation
     * @param int $hue
     */
    public function __construct(int $brightness = null, int $saturation = null, int $hue = null)
    {
        $this->brightness = $brightness + 100;
        $this->saturation = $saturation + 100;
        $this->hue = $hue + 100;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        return $image
           ->getCore()->modulateImage($this->brightness, $this->saturation, $this->hue);
    }
}
