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
     * @param callable[] $effect
     */
    public function __construct(array $effect = [])
    {
        $this->effect = $effect;
    }

    /**
     * @var callable[]
     */
    private $effect = [];

    /**
     * @param string $key
     *
     * @return callable
     */
    public function getEffect(string $key) : callable
    {
        return $this->effect[$key];
    }

    /**
     * @return bool
     */
    public function init() : bool
    {
        $effect = null;
        if ($this->effect) {
            $effect = '{{O:e<effect>' .
                '(' . implode('|', array_keys($this->effect)) . ')' .
                '(?:-(' . implode('|', array_keys($this->effect)) . '))*' .
                '}}';
        }

        self::router()->addRoutes([
            Route::create()
                ->setName('imageResize')
                ->setMatch('{{C:<file>.*}}\.' .
                    '{{O:w<width>[0-9]+}}' .
                    '{{O:h<height>[0-9]+}}' .
                    $effect .
                    '\.{{C:<extension>(jpg|jpeg|png|gif|tif|tiff|ico|bmp)}}')
                ->setController('Cawa\\ImageModule\\Controller::resize'),
        ]);

        return true;
    }
}
