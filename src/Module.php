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

namespace Cawa\ImageModule;

use Cawa\App\HttpFactory;
use Cawa\Router\Route;
use Cawa\Router\RouterFactory;
use Intervention\Image\Filters\FilterInterface;

class Module extends \Cawa\App\Module
{
    use HttpFactory;
    use RouterFactory;

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
