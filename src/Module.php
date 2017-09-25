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

namespace Cawa\ImageModule;

use Cawa\App\HttpFactory;
use Cawa\ImageModule\Filters\Blur;
use Cawa\ImageModule\Filters\Brightness;
use Cawa\ImageModule\Filters\CenteredBlur;
use Cawa\ImageModule\Filters\Contrast;
use Cawa\ImageModule\Filters\Fit;
use Cawa\ImageModule\Filters\Gamma;
use Cawa\ImageModule\Filters\Greyscale;
use Cawa\ImageModule\Filters\Interlace;
use Cawa\ImageModule\Filters\Modulate;
use Cawa\ImageModule\Filters\Resize;
use Cawa\ImageModule\Filters\Scale;
use Cawa\ImageModule\Filters\Sharpen;
use Cawa\Router\Route;
use Cawa\Router\RouterFactory;
use Intervention\Image\Filters\FilterInterface;

class Module extends \Cawa\App\Module
{
    use HttpFactory;
    use RouterFactory;

    private $filters = [
        'blur' => Blur::class,
        'brightness' => Brightness::class,
        'centeredblur' => CenteredBlur::class,
        'contrast' => Contrast::class,
        'fit' => Fit::class,
        'gamma' => Gamma::class,
        'greyscale' => Greyscale::class,
        'interlace' => Interlace::class,
        'modulate' => Modulate::class,
        'resize' => Resize::class,
        'scale' => Scale::class,
        'sharpen' => Sharpen::class,
    ];

    /**
     * @param string $name
     * @param array $args
     *
     * @return FilterInterface
     */
    public function getFilter(string $name, array $args) : FilterInterface
    {
        $class = $this->filters[$name];

        return new $class(...$args);
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return $this| self
     */
    public function addFilter(string $name, string $class) : self
    {
        $this->filters[$name] = $class;

        return $this;
    }

    /**
     * @return bool
     */
    public function init() : bool
    {
        self::router()->addRoutes([
            (new Route())
                ->setName('imageResize')
                ->setMatch(
                    '{{C:<file>.*}}' .
                    '\.imm' .
                    '{{O:<filters>(?:\:[a-zA-Z]+(?:\[[a-z0-9-,\.]*\])?)+}}' .
                    '{{O:\.<extensionFrom>(jpg|jpeg|png|gif|tif|tiff|ico|bmp|svg|dat)}}' .
                    '\.{{C:<extension>(jpg|jpeg|png|gif|tif|tiff|ico|bmp|svg|dat)}}'
                )
                ->setController('Cawa\\ImageModule\\Controller::resize'),
        ]);

        return true;
    }
}
