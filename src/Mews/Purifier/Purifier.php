<?php namespace Mews\Purifier;

use Config;

use HTMLPurifier, HTMLPurifier_Config;

/*
 * This file is part of HTMLPurifier Bundle.
 * (c) 2012 Maxime Dizerens
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * Modified
 * Laravel 4 HTMLPurifier package
 * @copyright Copyright (c) 2013 MeWebStudio
 * @version 1.0.0
 * @author Muharrem ERİN
 * @contact me@mewebstudio.com
 * @link http://www.mewebstudio.com
 * @date 2013-03-21
 * @license http://www.gnu.org/licenses/lgpl-2.1.html GNU Lesser General Public License, version 2.1
 *
 */

class Purifier {

    /**
     * @var  HTMLPurifier  singleton instance of the HTML Purifier object
     */
    protected static $singleton;

    /**
     * @var  array  set of configuration objects
     */
    protected static $configs;

    /**
     * Returns the singleton instance of HTML Purifier. If no instance has
     * been created, a new instance will be created.
     *
     *     $purifier = Purifier::instance();
     *
     * @return  HTMLPurifier
     */
    public static function instance()
    {
        if ( ! Purifier::$singleton)
        {
            if ( ! class_exists('HTMLPurifier_Config', false))
            {
                if (Config::get('purifier::config.preload'))
                {
                    // Load the all of HTML Purifier right now.
                    // This increases performance with a slight hit to memory usage.
                    require base_path('vendor/ezyang/htmlpurifier/library/') . 'HTMLPurifier.includes.php';
                }

                // Load the HTML Purifier auto loader
                require base_path('vendor/ezyang/htmlpurifier/library/') . 'HTMLPurifier.auto.php';
            }

            // Create a new configuration object
            $config = HTMLPurifier_Config::createDefault();

            // Allow configuration to be modified
            if ( ! Config::get('purifier::config.finalize'))
            {
                $config->autoFinalize = false;
            }

            // Use the same character set as Laravel
            $config->set('Core.Encoding', Config::get('purifier::config.encoding'));

            if (is_array(Config::get('purifier::config.settings.default')))
            {
                // Load the settings
                $config->loadArray(Config::get('purifier::config.settings.default'));
            }

            // Configure additional options
            $config = Purifier::configure($config);
            Purifier::$configs['default'] = $config;

            // Create the purifier instance
            Purifier::$singleton = new HTMLPurifier($config);
        }

        return Purifier::$singleton;
    }

    /**
     * Modifies the configuration before creating a HTML Purifier instance.
     *
     * [!!] You must create an extension and overload this method to use it.
     *
     * @param   HTMLPurifier_Config  configuration object
     * @return  HTMLPurifier_Config
     */
    public static function configure(HTMLPurifier_Config $config)
    {
        return $config;
    }

    /**
     * Removes broken HTML and XSS from text using [HTMLPurifier](http://htmlpurifier.org/).
     *
     *     $text = Purifier::clean($dirty_html);
     *
     * The original content is returned with all broken HTML and XSS removed.
     *
     * @param   mixed   text to clean, or an array to clean recursively
     * @param   mixed   optional set of configuration options, as an array or a string denoting a set of options in the config file
     * @return  mixed
     */
    public static function clean($dirty, $config = null)
    {
        if (is_array($dirty))
        {
            foreach ($dirty as $key => $value)
            {
                // Recursively clean arrays
                $clean[$key] = Purifier::clean($value);
            }
        }
        else
        {
            // Load HTML Purifier
            $purifier = Purifier::instance();

            // Clean the HTML and return it
            if(is_array($config)) {
                $c = HTMLPurifier_Config::inherit(Purifier::$configs['default']);
                $c->loadArray($config);

                $clean = $purifier->purify($dirty, $c);
            } else if(is_string($config)) {
                if(isset(Purifier::$configs[$config])) {
                    $c = Purifier::$configs[$config];
                } else {
                    $c = HTMLPurifier_Config::inherit(Purifier::$configs['default']);
                    $c->loadArray(Config::get('purifier::config.settings.' . $config));
                    Purifier::$configs[$config] = $c;
                }

                $clean = $purifier->purify($dirty, $c);
            } else {
                $clean = $purifier->purify($dirty, Purifier::$configs['default']);
            }
        }

        return $clean;
    }

}
