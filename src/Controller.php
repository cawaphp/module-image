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
use Cawa\Controller\AbstractController;
use Cawa\Core\DI;
use Cawa\Date\DateTime;
use Cawa\Events\DispatcherFactory;
use Cawa\Events\TimerEvent;
use Intervention\Image\Image;
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
    private function parseFilters(string $filters = null) : array
    {
        if (is_null($filters)) {
            return [];
        }

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
                    $arg = (int) $arg;
                } elseif (is_numeric($arg)) {
                    $arg = (float) $arg;
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
     * @var ImageManager
     */
    private $manager;

    /**
     * @return ImageManager
     */
    private function getManager() : ImageManager
    {
        if (!$this->manager) {
            $options = class_exists('Imagick') ? ['driver' => 'imagick'] : [];
            $this->manager = new ImageManager($options);
        }

        return $this->manager;
    }

    /**
     * @param string $file
     * @param string $extension
     * @param string $filters
     * @param string $extensionFrom
     *
     * @return string
     */
    public function resize(
        string $file,
        string $extension,
        string $filters,
        string $extensionFrom = null
    ) : string {
        $path = $_SERVER['DOCUMENT_ROOT'] . $file . '.' . ($extensionFrom ? $extensionFrom: $extension);
        $timerEvent = new TimerEvent('image.make', ['path' => $path]);

        if (!file_exists($path)) {
            self::response()->setStatus(404);
            $img = $this->getManager()->make(dirname(__DIR__) . '/assets/404.png');
        } else {
            $img = $this->getManager()->make($path);
        }

        return $this->handleResize($img, $timerEvent, $filters, $extension);
    }

    /**
     * @param string $stream
     * @param string $extension
     * @param string $filters
     *
     * @return string
     */
    public function resizeFromStream(string $stream, string $extension, string $filters = null) : string
    {
        $timerEvent = new TimerEvent('image.make', ['stream' => true]);

        $img = $this->getManager()->make($stream);

        return $this->handleResize($img, $timerEvent, $filters, $extension);
    }

    /**
     * @param Image $img
     * @param TimerEvent $timerEvent
     * @param string $filters
     * @param string $extension
     *
     * @return string
     */
    private function handleResize(Image $img, TimerEvent $timerEvent, string $filters = null, string $extension) : string
    {
        $timerEvent->addData([
            'width' => $img->width(),
            'heigth' => $img->height(),
            'size' => $img->filesize(),
        ]);

        self::emit($timerEvent);

        $quality = DI::config()->getIfExists('image/quality');

        $filters = $this->parseFilters($filters);
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

        $this->addExpires();

        return $encoded->getEncoded();
    }

    /**
     *
     */
    private function addExpires()
    {
        if ($interval = DI::config()->getIfExists('image/expires')) {
            $date = (new DateTime());
            $expiration = $date->add(new \DateInterval($interval));

            self::response()->addHeader('expires', $expiration->format('D, d M Y H:i:s') . ' GMT');
            self::response()->addHeader('pragma', 'public');
            self::response()->addHeader('cache-control', 'max-age=' . ($expiration->getTimestamp() - $date->getTimestamp()) . ', public');
        }
    }

}
