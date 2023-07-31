<?php

namespace WPC\Viewer;

use ElementorPro\Modules\GlobalWidget\Documents\Widget;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Viewer_Loader
{


	private static $_instance = null;

	public static function instance() {

		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
	return self::$_instance;

	}

 
private function include_widgets_files() {

	require_once(__DIR__ . '/viewer/viewer.php');

}

public function register_widgets($widgets_manager) {
	if(is_user_logged_in()) {
	$this->include_widgets_files();

	$widgets_manager->register(new Helper\Helper());
	}
} 



public function __construct() {

	add_action('elementor/widgets/register',[$this,'register_widgets'],99);

}

} //class end

//Instantiate Plugin Class

Viewer_Loader::instance();


