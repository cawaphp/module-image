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

use Cawa\App\AbstractApp;
use Cawa\App\HttpFactory;
use Cawa\Controller\AbstractController;
use Cawa\Core\DI;
use Cawa\Events\DispatcherFactory;
use Cawa\Events\TimerEvent;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

class Controller extends AbstractController
{
    use HttpFactory;
    use DispatcherFactory;

    /**
     * @param string $filters
     *
     * @return array
     */
    private function parseFilters(string $filters) : array
    {
        $filters = explode(':', $filters);
        array_shift($filters);

        // global configuration filters
        $configurations = DI::config()->getIfExists('image/effects');
        if (is_array($configurations)) {
            $filters = array_merge($filters, $configurations);
        }

        $return = [];
        foreach ($filters as $filter) {

            $type = null;
            $args = [];

            if (stripos($filter, '[') !== false) {
                $type = substr($filter, 0, stripos($filter, '['));
                $args = explode(',', substr($filter, stripos($filter, '[') + 1, -1));
            } else {
                $type = $filter;
            }

            foreach ($args as &$arg) {
                if (is_numeric($arg) && (int) $arg == $arg) {
                    $arg = (int)$arg;
                }

                if ($arg === '') {
                    $arg = null;
                }
            }

            $return[] = [$type, $args];
        }

        return $return;
    }

    /**
     * @param string $file
     * @param string $extension
     * @param string $filters
     * @param string $extensionFrom
     * @return string
     */
    public function resize(
        string $file,
        string $extension,
        string $filters,
        string $extensionFrom = null
    ) : string
    {
        $filters = $this->parseFilters($filters);

        $options = class_exists('Imagick') ? ['driver' => 'imagick'] : [];
        $manager = new ImageManager($options);

        $path = $_SERVER['DOCUMENT_ROOT'] . $file . '.' . ($extensionFrom ? $extensionFrom: $extension);
        $timerEvent = new TimerEvent('image.make', ['path' => $path]);

        if (!file_exists($path)) {
            self::response()->setStatus(404);
            $img = $manager->make(dirname(__DIR__) . '/assets/404.png');
        } else {
            $img = $manager->make($path);
        }

        $timerEvent->addData([
            'width' => $img->width(),
            'heigth' => $img->height(),
            'size' => $img->filesize(),
        ]);

        self::emit($timerEvent);

        $quality = DI::config()->getIfExists('image/quality');

        foreach ($filters as $currentEffect) {
            list($type, $args) = $currentEffect;

            $timerEvent = new TimerEvent('image.effect', [
                'type' => $type,
                'args' => $args,
            ]);

            $class = 'Cawa\\ImageModule\\Filters\\' . ucfirst($type);
            $effect = new $class(...$args);

            $img->filter($effect);
            self::emit($timerEvent);
        }

        $encoded = $img->encode($extension, $quality);

        self::response()->addHeader('Content-Type', $encoded->mime());
        self::response()->addHeader('Content-Length', (string) strlen($encoded->getEncoded()));

        return $encoded->getEncoded();
    }
}
