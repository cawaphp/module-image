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

use Cawa\Net\Uri;
use Cawa\Renderer\AssetTrait;

/**
 * @mixin AssetTrait
 */
trait ImageTrait
{
    /**
     * @param string $path
     * @param string $effects
     * @param string $extensionTo
     *
     * @return Uri
     */
    public function image(
        string $path,
        string $effects = null,
        string $extensionTo = null
    ) : Uri {
        list($path) = $this->getAssetData($path);

        $infos = pathinfo($path->getPath());
        $finalPath = $infos['dirname'] . '/' . $infos['filename'];

        if (sizeof($effects)) {
            $finalPath .= '.imm:' . $effects;
        }

        $finalPath .= '.' . $infos['extension'];

        if ($extensionTo) {
            $finalPath .= '.' . $extensionTo;
        }

        return $path->setPath($finalPath);
    }
}
