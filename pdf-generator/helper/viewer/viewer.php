<?php 

namespace WPC\Viewer\Helper;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class Helper extends Widget_Base {


public function get_name(){
	return "printer_viewer";
}

public function get_title() {
 	return "PDF Printer Viewer";
}

public function get_icon() {
	return "eicon-image-box";
}

public function get_categories()
{
	return ['general'];
}

protected function _register_controls()
{

	$this->start_controls_section(
	'section_content',
	[
		'label'=>'Viewer',
	]);




	$this->end_controls_section();
	
	
}

protected function render() {

	$settings = $this->get_settings_for_display();
	$this->add_inline_editing_attributes('label_heading','basic');
	$this->add_render_attribute(
		'label_heading',
		[
			'class' => ['print__label-heading'],
		]

	);

	?>
<form class="border" action="">
<div class="row"><p><b>You have selected:</b></p></div>
<div class="row-li">

<?php 

global $woocommerce;
$items = $woocommerce->cart->get_cart(); ?>
<ol>
<?php foreach($items as $item => $values) : ;
	$_product =  wc_get_product( $values['data']->get_id()); 
	echo "<li>".$_product->get_title().'</li>'; ?>
<?php endforeach;




?>
</ol>

</div>
</form>

<style>
@media(min-width:855px) {
.border {
 border: 1px solid #fff;
 padding: 20px;
}

.row-li {
display: inline;
}

.row-li li{
float:left;
margin-right: 35px;
}

.row {
display:flex;
flex-direction: row;
justify-content: space-between;
}
.col {
flex-direction: column;	
margin: 0 15px 0 15px;
}

.col-2 {
flex-direction: column;	
margin: auto 15px auto 15px;

}
}
@media(max-width: 854px) {
.border {
 border: 1px solid #fff;
 padding: 5px;
}

.row {
display:flex;
flex-direction: column;
justify-content: space-between;
}
.col {
flex-direction: row;	
margin: 15px auto 15px auto;
}

.col-2 {
flex-direction: row;	
margin: 15px auto 15px auto;

}
}
</style>


<?php }


}

 