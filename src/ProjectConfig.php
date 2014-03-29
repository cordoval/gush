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
 * @author Carlos Salvatierra <cslucano@gmail.com>
 */
class ProjectConfig
{
    public static $defaultConfig = ['key' => 'value'];

    /**
     * @var array $config
     */
    private $config;

    public function __construct()
    {
        // load defaults
        $this->config = static::$defaultConfig;
    }

    /**
     * Merges new config values with the existing ones (overriding)
     *
     * @param array $config
     */
    public function merge(array $config)
    {
        // override defaults with given config
        foreach ($config as $key => $val) {
            $this->config[$key] = $val;
        }
    }

    /**
     * Returns a setting
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        switch ($key) {
            default:
                if (!isset($this->config[$key])) {
                    return null;
                }

                return $this->config[$key];
        }
    }

    public function raw()
    {
        return $this->config;
    }

    /**
     * Checks whether a setting exists
     *
     * @param  string  $key
     * @return Boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Validates if the configuration
     *
     * @return Boolean
     */
    public function isValid()
    {
        if (isset($this->config['key']['value'])) {
            return true;
        }

        return false;
    }
}
