<?php 

namespace WPC\Index\Panel;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class Printpdfpanel extends Widget_Base {


public function get_name(){
	return "printer_pdf";
}

public function get_title() {
 	return "PDF Printer Panel";
}

public function get_icon() {
	return "eicon-header";
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
		'label'=>'Panel',
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
<form method="post" class="border" action="<?php echo admin_url('admin-post.php'); ?>">
<?php wp_nonce_field('my_plugin_action','security-code-here'); ?>
<input name="action" value="my_plugin_action" type="hidden">
<div class="row grid-row">
<div class="col">
<p class="color-white"><b>Printing options:</b></p>
  <input type="radio" name="rad" value="YES" checked="checked" data-waschecked="true">
  <label class="color-white" for="yes_cover">With covers</label><br>
</div>

<div class="col">
<label class="label-select" for="page_select">Choose products per page:</label>

<select name="page_select" id="page_select" class="select-style">
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3" selected>3</option>
</select>
</div>

<div class="col">
<a onclick="myClearAllFunction()" class="danger" value="on">Unselect All</a>
</div>

<div class="col">
<button name="print" class="print" value="on">Print PDF</button>
</div>


</div>
</form>



<style>

.label-select, .color-white {
	color: white;
}


.select-style {
	border-radius: 0px;
}

.print {
	border-radius: 0px;
	color: red;
	border: 2px solid white;
	background-color: red;
	color: white;
}

.print:hover {
	border-radius: 0px;
	color: red;
	border: 2px solid white;
	background-color: red;
	color: white;
}



.danger  {
    color: white!important;
    text-decoration: none;
	background-color: transparent;
	display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
	padding: 0.5rem 1rem;
    font-size: 1rem;
    border-radius: 0px;
	cursor: pointer;
	border: 2px solid white;
}

.danger:hover {
	background-color: transparent!important;	
	text-decoration: none;
	color: white!important;
}

@media(min-width:855px) {

.border {
 border: 1px solid #fff;
 padding: 20px;
}

.row {
display:flex;
flex-direction: row;
justify-content: space-between;
align-items: flex-end;
background-color: #2F2F2F;
}

.row div {
padding: 10px;
}

.col {
margin: 0 15px 0 15px;
}

.col-2 {
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
background-color: #2F2F2F;
}

.row div {
	padding: 10px;
}
.col {
margin: 15px auto 15px auto;
}

.col-2 {
margin: 15px auto 15px auto;
}

}
</style>


<?php }


} //class end

