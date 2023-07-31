<?php

namespace WPC\Index;

use ElementorPro\Modules\GlobalWidget\Documents\Widget;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Widget_Loader
{


	private static $_instance = null;

	public static function instance() {

		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
	return self::$_instance;

	}

 
private function include_widgets_files() {

	require_once(__DIR__ . '/panel/printpdfpanel.php');

}

public function register_widgets($widgets_manager) {
	if(is_user_logged_in()) {
	$this->include_widgets_files();

	$widgets_manager->register(new Panel\Printpdfpanel());
	}
} 


public function __construct() {

	add_action('elementor/widgets/register',[$this,'register_widgets'],99);
}

} //class end

//Instantiate Plugin Class

Widget_Loader::instance();


