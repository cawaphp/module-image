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

use Cawa\App\AbstractApp;
use Cawa\App\HttpFactory;
use Cawa\Controller\AbstractController;
use Cawa\Core\DI;
use Cawa\Events\DispatcherFactory;
use Cawa\Events\TimerEvent;
use Intervention\Image\ImageManager;

class Controller extends AbstractController
{
    use HttpFactory;
    use DispatcherFactory;

    /**
     * @param string $file
     * @param string $extension
     * @param int $width
     * @param int $height
     * @param string $effect
     *
     * @return string
     */
    public function resize(
        string $file,
        string $extension,
        int $width = null,
        int $height = null,
        string $effect = null
    ) : string {
        $options = class_exists('Imagick') ? ['driver' => 'imagick'] : [];
        $manager = new ImageManager($options);

        $path = $_SERVER['DOCUMENT_ROOT'] . $file . '.' . $extension;
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

        self::dispatcher()->emit($timerEvent);

        if (!$height) {
            $height = round($width * $img->height() / $img->width());
        }

        if (!$width) {
            $width = round($height * $img->width() / $img->height());
        }

        $timerEvent = new TimerEvent('image.resize');

        $interlace = DI::config()->getIfExists('image/interlace');
        $interlace = is_null($interlace) ? true : $interlace;

        $sharpen = DI::config()->getIfExists('image/sharpen');
        $sharpen = is_null($sharpen) ? 5 : $sharpen;

        $quality = DI::config()->getIfExists('image/quality');

        $timerEvent->addData([
            'width' => $width,
            'heigth' => $height,
        ]);

        $encoded = $img->fit($width, $height);

        self::dispatcher()->emit($timerEvent);

        if ($interlace) {
            $timerEvent = new TimerEvent('image.effect', ['type' => 'interlace']);
            $encoded->interlace();
            self::dispatcher()->emit($timerEvent);
        }

        if ($sharpen) {
            $timerEvent = new TimerEvent('image.effect', ['type' => 'sharpen']);
            $encoded->sharpen($sharpen);
            self::dispatcher()->emit($timerEvent);
        }

        if ($effect) {
            /* @var \Cawa\ImageModule\Module $module */
            $module = AbstractApp::instance()->getModule('Cawa\\ImageModule\\Module');

            foreach (explode('-', $effect) as $currentEffect) {
                $timerEvent = new TimerEvent('image.effect', ['type' => $currentEffect]);
                $callable = $module->getEffect($currentEffect);
                $encoded = $callable($encoded);
                self::dispatcher()->emit($timerEvent);
            }
        }

        $encoded = $encoded->encode($extension, $quality);

        self::response()->addHeader('Content-Type', $encoded->mime());
        self::response()->addHeader('Content-Length', (string) strlen($encoded->getEncoded()));

        return $encoded->getEncoded();
    }
}
