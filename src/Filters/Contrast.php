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

class Contrast implements FilterInterface
{
    /**
     * @var int
     */
    private $contrast;

    /**
     * @param int $contrast
     */
    public function __construct(int $contrast)
    {
        $this->contrast = $contrast;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        return $image
           ->contrast($this->contrast);
    }
}
