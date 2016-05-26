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
use Cawa\Controller\AbstractController;
use Cawa\Core\DI;
use Intervention\Image\ImageManager;

class Controller extends AbstractController
{
    use HttpFactory;

    /**
     * @param string $file
     * @param string $extension
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function resize(string $file, string $extension, int $width = null, int $height = null) : string
    {
        $options = class_exists('Imagick') ? ['driver' => 'imagick'] : [];
        $manager = new ImageManager($options);

        $path = $_SERVER['DOCUMENT_ROOT'] . $file . '.' . $extension;
        if (!file_exists($path)) {
            $this->response()->setStatus(404);
            $img = $manager->make(dirname(__DIR__) . '/assets/40<4.png');
        } else {
            $img = $manager->make($path);
        }

        if (!$height) {
            $height = round($width * $img->height() / $img->width());
        }

        if (!$width) {
            $width = round($height * $img->width() / $img->height());
        }

        $interlace = DI::config()->getIfExists('image/interlace');
        $interlace = is_null($interlace) ? true : $interlace;

        $sharpen = DI::config()->getIfExists('image/sharpen');
        $sharpen = is_null($sharpen) ? 5 : $sharpen;

        $quality = DI::config()->getIfExists('image/quality');

        $encoded = $img->fit($width, $height);

        if ($interlace) {
            $encoded->interlace();
        }

        if ($sharpen) {
            $encoded->sharpen($sharpen);
        }

        $encoded = $encoded->encode($extension, $quality);

        $this->response()->addHeader('Content-Type', $encoded->mime());
        $this->response()->addHeader('Content-Length', (string) strlen($encoded->getEncoded()));

        return $encoded->getEncoded();
    }
}
