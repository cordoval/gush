<?php

/**
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush;

/**
 * Creates the default directory structure to run Gush
 *
 * @author Daniel Gomes <me@danielcsgomes.com>
 */
class Factory
{
    /**
     * @throws \RuntimeException
     * @return Config
     */
    public static function createHomeConfig()
    {
        // determine home and cache dirs
        $home = getenv('GUSH_HOME');
        $cacheDir = getenv('GUSH_CACHE_DIR');
        if (!$home) {
            if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
                if (!getenv('APPDATA')) {
                    throw new \RuntimeException(
                        'The APPDATA or GUSH_HOME environment variable must be set for Gush to run correctly'
                    );
                }
                $home = strtr(getenv('APPDATA'), '\\', '/').'/Gush';
            } else {
                if (!getenv('HOME')) {
                    throw new \RuntimeException(
                        'The HOME or GUSH_HOME environment variable must be set for Gush to run correctly'
                    );
                }
                $home = rtrim(getenv('HOME'), '/').'/.gush';
            }
        }
        if (!$cacheDir) {
            if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
                if ($cacheDir = getenv('LOCALAPPDATA')) {
                    $cacheDir .= '/Gush';
                } else {
                    $cacheDir = $home.'/cache';
                }
                $cacheDir = strtr($cacheDir, '\\', '/');
            } else {
                $cacheDir = $home.'/cache';
            }
        }

        // Protect directory against web access. Since HOME could be
        // the www-data's user home and be web-accessible it is a
        // potential security risk
        foreach ([$home, $cacheDir] as $dir) {
            if (!file_exists($dir.'/.htaccess')) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0744, true);
                }
                @file_put_contents($dir.'/.htaccess', 'Deny from all');
            }
        }

        $config = new Config();

        // add dirs to the config
        $config->merge(['home' => $home, 'cache-dir' => $cacheDir]);

        return $config;
    }

    /**
     * @throws \RuntimeException
     */
    public function createProjectConfig()
    {

    }
}
