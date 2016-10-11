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
     * @return FilterInterface
     */
    public function getEffect(string $key) : FilterInterface
    {
        return $this->effect[$key]();
    }

    /**
     * @return bool
     */
    public function init() : bool
    {
        $effect = null;
        if ($this->effect) {
            $effect = '{{O:e_<effect>' .
                '(' . implode('|', array_keys($this->effect)) . ')' .
                '(?:-(' . implode('|', array_keys($this->effect)) . '))*' .
                '}}';
        }

        self::router()->addRoutes([
            (new Route())
                ->setName('imageResize')
                ->setMatch('{{C:<file>.*}}\.' .
                    '{{O:w_<width>[0-9]+}}' .
                    '{{O:h_<height>[0-9]+}}' .
                    '{{O:p_<position>(t|b)?(l|r)?}}' .
                    $effect .
                    '{{O:\.<extensionFrom>(jpg|jpeg|png|gif|tif|tiff|ico|bmp|svg)}}' .
                    '\.{{C:<extension>(jpg|jpeg|png|gif|tif|tiff|ico|bmp|svg)}}')
                ->setController('Cawa\\ImageModule\\Controller::resize'),
        ]);

        return true;
    }
}
