<?php

/**
 *
 * @package   Bic Contest Plugin
 * @author    Arqaa
 * @copyright 2024 - Arqaa
 *
 * @wordpress-plugin
 * Plugin Name:       Bic Contest Plugin
 * Description:       With Bic Contest you can easily organize a photo contest on your website.
 * Version:           1.0.0
 * Author:            Arqaa
 * Text Domain:       bic-contest
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Define plugin constants
 */
const BIC_VERSION = '1.0.3';
define('BIC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BIC_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once BIC_PLUGIN_PATH."includes/bic-init.php";