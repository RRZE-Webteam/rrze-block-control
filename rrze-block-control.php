<?php

/*
 * Plugin Name: RRZE Block Control
 * Plugin URL: https://www.rrze.fau.de
 * description: A wordpress Plugin to improve the UX inside the BlockEditor, whenever wanted.
 * Author: RRZE Webteam
 * Author URI: https://www.rrze.fau.de
 * Version: 0.0.1
 * License: GPL3
 * Text domain: block-control
 * Domain Path: /languages
 */

use RRZE\BlockControl\Main;

defined('ABSPATH') || exit;

const RRZE_BLOCKCONTROL_PHP_VERSION = '8.2';
const RRZE_BLOCKCONTROL_WP_VERSION = '6.7';

// ==================================================
// Actions and Activation / Deactivation Hooks
// ==================================================
add_action('plugins_loaded', 'block_control_init');
add_action('init', 'block_control_load_textdomain');

register_activation_hook(__FILE__, 'block_control_plugin_activation');
register_deactivation_hook(__FILE__, 'block_control_plugin_deactivation');

// ==================================================
// Plugin Initialization, Activation and Deactivation
// ==================================================
/**
 * Initializes the Plugin
 *
 * Runs on plugins_loaded WP action and initializes the Plugin.
 * It might rely on an activation function that could run before this function to Setup the Options.
 * @return void
 */
function block_control_init(): void
{
    block_control_include_autoloader();

    new Main();
}

/**
 * Plugin Activation Function
 */
function block_control_plugin_activation(): void
{
    block_control_load_textdomain();
    block_control_check_system_requirements();
    block_control_include_autoloader();

    // Your required activation steps here, if there are any
    // For example defining a global option, etc.
}

/**
 * Plugin Deactivation Function
 */
function block_control_plugin_deactivation(): void
{
    block_control_include_autoloader();

    // Your required deactivation logic here.
    // For example wiping the wp transients of your plugin if needed
}

// ==================================================
// Helper functions | Autoload | System Check | Textdomain Loading
// ==================================================
/**
 * Include the Composer Autoloader
 *
 * The PSR-4 Autloader in Composer makes it possible to rely on PHP classes
 * inside our includes directory. It is configured in the Composer config file.
 * For now just be aware, that you could also replace the comoposer autoloader with a
 * PSR-4 Autloading-Code-Snippet.
 */
function block_control_include_autoloader(): void
{
    if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
        require_once(dirname(__FILE__) . '/vendor/autoload.php');
    }
}

/**
 * Load the Textdomain for localization (l10n).
 *
 * This is only required, if we host our plugin on GitHub, etc.
 * On the official WordPress Org site is it enough to just define the Textdomain
 * in the Plugin comment at the beginning of this file
 */
function block_control_load_textdomain(): void
{
    load_plugin_textdomain('block_control', FALSE, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/**
 * Checks the System Requirements and deactivates the Plugin in case of violation
 *
 * Checks the System Requirements for in L16,L17 defined WP- and PHP-Version.
 * If the check fails, the Plugin is deactivated network-wide
 * @return void
 */
function block_control_check_system_requirements(): void
{
    $error = '';
    if (version_compare(PHP_VERSION, RRZE_BLOCKCONTROL_PHP_VERSION, '<')) {
        $error = sprintf(__('Your server is running PHP version %s. Please upgrade at least to PHP version %s.', 'block_control'), PHP_VERSION, RRZE_BLOCKCONTROL_PHP_VERSION);
    }

    if (version_compare($GLOBALS['wp_version'], RRZE_BLOCKCONTROL_WP_VERSION, '<')) {
        $error = sprintf(__('Your Wordpress version is %s. Please upgrade at least to Wordpress version %s.', 'block_control'), $GLOBALS['wp_version'], RRZE_BLOCKCONTROL_WP_VERSION);
    }

    if (!empty($error)) {
        deactivate_plugins(plugin_basename(__FILE__), FALSE, TRUE);
        wp_die($error);
    }
}