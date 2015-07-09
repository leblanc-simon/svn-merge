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

/**
 * Config class.
 *
 * @version     1.0.0
 *
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
class Config
{
    /**
     * The array with all configuration values.
     *
     * @static
     */
    private static $datas = array();

    /**
     * Add an array of configuration.
     * 
     * @param array $datas An array of configuration values
     * @static
     */
    public static function add(array $datas)
    {
        self::$datas = array_merge(self::$datas, $datas);
    }

    /**
     * Set a configuration value.
     *
     * @param string $name    The name of the configuration
     * @param mixed  $value   The value of the configuration
     * @param bool   $replace True to replace configuration if it exists, false else
     *
     * @return bool True if the configuration is setted, false else
     * @static
     */
    public static function set($name, $value, $replace = true)
    {
        if ($replace === true || isset(self::$datas[$name]) === false) {
            self::$datas[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * Get a configuration value.
     *
     * @param string $name    The name of the configuration to get
     * @param mixed  $default The default value if the configuration name doesn't exist
     *
     * @return mixed The value of the configuration
     * @static
     */
    public static function get($name, $default = null)
    {
        return isset(self::$datas[$name]) === false ? $default : self::$datas[$name];
    }
}
