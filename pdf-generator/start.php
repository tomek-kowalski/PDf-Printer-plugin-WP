<?php


namespace My_plugin;

use ElementorPro\Modules\GlobalWidget\Documents\Widget;

/**
 * @link              https://kowalski-consulting.pl/
 * @since             1.0.0
 * @package           PDF Print Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Print PDF Generator
 * Description:       Generating PDF files from product cataloque. It will work correctly only on a shop page.
 * Version:           1.0.0
 * Author:            Tomasz Kowalski
 * Author URI:        https://kowalski-consulting.pl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pdf-gen
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
const GEN = '1.0.0';

define( 'GEN_DIR', plugin_dir_path( __FILE__ ) );


require_once(GEN_DIR . "/index/pdfgen.php");
require_once(GEN_DIR . "/redirect/redirect.php");
require_once(GEN_DIR . "/database/database.php");
require_once(GEN_DIR . "/helper/helper.php");


