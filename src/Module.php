<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Cawa\ImageModule;

use Cawa\App\HttpFactory;
use Cawa\Router\Route;
use Cawa\Router\RouterFactory;

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
            Route::create()
                ->setName('imageResize')
                ->setMatch('{{C:<file>.*}}\.' .
                    '{{O:w<width>[0-9]+}}' .
                    '{{O:h<height>[0-9]+}}' .
                    '\.{{C:<extension>(jpg|jpeg|png|gif|tif|tiff|ico|bmp)}}')
                ->setController('Cawa\\ImageModule\\Controller::resize'),
        ]);

        return true;
    }
}
