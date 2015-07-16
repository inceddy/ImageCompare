<?php

/*
 * This file is part of ImageCompare.
 *
 * (c) 2015 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */


/**
 * Autoloads ImageCompare classes.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class ImageCompareAutoloader
{
    /**
     * Registers ImageCompareAutoloader as an SPL autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     *
     * @return void
     * 
     */

    public static function register($prepend = false)
    {
        if (PHP_VERSION_ID < 50300) {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        } else {
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        }
    }
    /**
     * Handles autoloading of classes.
     *
     * @param string $class the class name
     * 
     * @return void
     *  
     */
    
    public static function autoload($class)
    {
        if (is_file($file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR , $class).'.php')) {
            require $file;
        }
    }
}

ImageCompareAutoloader::register();