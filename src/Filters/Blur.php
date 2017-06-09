<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\ImageModule\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Blur implements FilterInterface
{
    /**
     * @var int
     */
    private $blur;

    /**
     * @param int $blur
     */
    public function __construct(int $blur)
    {
        $this->blur = $blur;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        return $image
           ->colorize(3, 3, 3)
           ->contrast(-30)
           ->blur($this->blur);
    }
}
