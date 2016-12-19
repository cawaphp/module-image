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

class Gamma implements FilterInterface
{
    /**
     * @var float
     */
    private $correction;

    /**
     * @param float $correction
     */
    public function __construct(float $correction)
    {
        $this->correction = $correction;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        return $image
           ->gamma($this->correction);
    }
}
