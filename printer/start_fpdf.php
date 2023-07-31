<?php

namespace Loading;

if ( ! defined( 'WPINC' ) ) {
	die;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * @link              http://www.fpdf.org/
 * @since             1.85
 * @package           FPDF
 * 
 * @wordpress-plugin
 * Plugin Name:       FPDF
 * Description:       Custom adaptation of FPDF.
 * Version:           1.85
 * Author:            Olivier PLATHEY, Tomasz Kowalski
 * Author URI:        https://kowalski-consulting.pl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fpdf
 * Date:    		  2023-03-06  
 */


 define( 'ROOT', WP_PLUGIN_DIR . '/printer/');

 define( 'WOO', WP_PLUGIN_DIR . '/woocommerce/');

// include_once WOO . 'includes/wc-cart-functions.php';
 //include_once WOO . 'includes/class-wc-cart.php';







