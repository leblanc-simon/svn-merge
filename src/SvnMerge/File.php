<?php

/**
 * This file is part of the SvnMerge package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SvnMerge;

class File
{
    public static function convertPath($path)
    {
        if (substr($path, 0, 2) === '~/') {
            return getenv('HOME').DIRECTORY_SEPARATOR.substr($path, 2);
        }

        return $path;
    }
}
