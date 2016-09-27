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

use Cawa\Renderer\AssetTrait;

/**
 * @mixin AssetTrait
 */
trait ImageTrait
{
    /**
     * @param string $path
     * @param int $width
     * @param int $height
     * @param array $position
     * @param string[] $effect
     * @param string $extensionTo
     *
     * @return string
     */
    public function image(
        string $path,
        int $width = null,
        int $height = null,
        array $position = null,
        array $effect = null,
        string $extensionTo = null
    ) : string {
        list($path) = $this->getAssetData($path);

        $infos = pathinfo($path);
        $finalPath = $infos['dirname'] . '/' . $infos['filename'];

        if ($width || $height || $effect) {
            $finalPath .= '.';

            if ($width) {
                $finalPath .= 'w_' . $width;
            }

            if ($height) {
                $finalPath .= 'h_' . $height;
            }

            if ($position) {
                $finalPath .= 'p_' . implode('', $position);
            }

            if ($effect) {
                $finalPath .= 'e_' . implode('-', $effect);
            }
        }

        $finalPath .= '.' . $infos['extension'];

        if ($extensionTo) {
            $finalPath .= '.' . $extensionTo;
        }

        return $finalPath;
    }
}
