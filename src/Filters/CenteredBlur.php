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

use Cawa\App\HttpFactory;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class CenteredBlur implements FilterInterface
{
    use HttpFactory;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $blur;

    /**
     * @param int $width
     * @param int $height
     * @param int $blur
     */
    public function __construct(int $width = null, int $height = null, int $blur = 30)
    {
        $this->width = $width;
        $this->height = $height;
        $this->blur = $blur;
    }

    /**
     * {@inheritdoc}
     */
    public function applyFilter(Image $image)
    {
        $background = clone $image;
        $front = clone $image;
        $frontBlur = clone $image;

        $front->filter(new Scale($this->width, $this->height));

        $twoImage = $front->width() * 2 > $this->width;

        if ($twoImage) {
            $frontBlur
                ->filter(new Scale($this->width, $this->height))
                ->filter(new Blur($this->blur))
            ;
        }

        $background
            ->filter(new Fit($this->width, $this->height))
            ->filter(new Blur($this->blur))
        ;

        $return = $background;

        if ($twoImage) {
            $return
                ->insert($frontBlur, 'top-left')
                ->insert($frontBlur, 'top-right');
        }

        return $return->insert($front, 'center');
    }
}
