<?php

namespace Loading;



if ( ! defined( 'WPINC' ) ) {
	die;
}

//define('FPDF_FONTPATH', '/Sora/');

define( 'PRINT_IMG', plugin_dir_path( __FILE__ ) . '/images/');



//require(ROOT . "fpdf.php");


class PDF extends FPDF
{

    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start

//Page header

function Header() {

    if($this->PageNo() >= 1)  {

        $this->Image(PRINT_IMG . 'header.png',0,0,-170);
        // Arial bold 15
        $this->SetFont('Arial','B',10);
        // Move to the right
        $this->Cell(5);
        // Title
        // Line break
        $this->Ln(20);
    } 
}//end header

// Page footer

function Footer()
{


if($this->PageNo() != 1) {


    $sikorski = "© Sikorski Sausages" . " " . Date("Y");
    $text = "Every effort is taken to ensure that the ingredients and nutritional information listed here is accurate, however,";
    $text_2 = "data may change from time to time. Please always check the package for the most current information.";
    
    if (preg_match('/Â/', $sikorski)) {
        $sikorski = urldecode(str_replace("Â", "%C2", "© Sikorski Sausages" . " " . Date("Y")));
    } else {
        $sikorski = "Sikorski Sausages" . " " . Date("Y");
    };

    $this->SetY(-20);
    $this->Image(PRINT_IMG . 'footer_line.png',-4, 280,-166);
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    $this->SetFont('Arial','',10);
    $this->Cell(0,10,$sikorski,0,0,'L');
    $this->SetX(30);
    $this->SetFont('Arial','',6);
    $this->Cell(0,8,$text,0, 0, 'C');
    
    $this->SetY(-10);
    $this->SetX(23);
    $this->Cell(0,8,$text_2,0, 0, 'C');
    $this->SetY(-15);
    $this->SetFont('Arial','',10);
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    } 
} // end footer

function ChapterTitle($num, $label)
{
    // Title
    
    $this->SetFont('Arial','',12);
    $this->setTextColor(66, 40, 14);
    $this->SetFillColor(255,255,255);
    $this->Cell(0,6," $num...................................................................................................................... $label",0,1,'L',true);
    $this->Ln(10);
    // Save ordinate
    $this->y0 = $this->GetY();
}

function SetCol($col)
{
    // Set position at a given column
    $this->col = $col;
    $x = 10+$col*65;
    $this->SetLeftMargin($x);
    $this->SetX($x);
}

function PrintChapter($num, $title, $file)
{
    // $this->AddPage(); //Add chapter
    $this->ChapterTitle($num,$title);
    //$this->ChapterBody($file);
}

} //class end
$pdf = new PDF();

/* // First page
$pdf->AddPage('P','A4');
$pdf->Image(PRINT_IMG . 'cover_1.png',0,0,-300); */


//Contents Page
/* 
if ( is_null( WC()->cart ) ) {
    wc_load_cart();
}

global $woocommerce;

$product = "";

$items = $woocommerce->cart->get_cart();


$pdf->AddPage();
$pdf->Image(PRINT_IMG . 'table-contents.png',0,40,-170);
$pdf->AliasNbPages();
$pdf->SetY(75);
$i = 0;


foreach($items as $item => $values) : ;

$taxonomy       = 'products_property';





$_product               =  wc_get_product( $values['data']->get_id()); 
$product_type           =  wp_get_post_terms( $_product->get_id(), $taxonomy, ['order' => 'ASC', 'fields' => 'names', ]);

$input                  = implode('',$product_type  );


$i<count($items); $i++;
$pdf->PrintChapter($i,$input ,'test 1');


endforeach; */

//Product Page

if ( is_null( WC()->cart ) ) {
    wc_load_cart();
}

global $woocommerce;


$product = "";

$items = $woocommerce->cart->get_cart();

foreach($items as $item => $values) : ;

    $taxonomy       = 'products_property';
	$_product       =  wc_get_product( $values['data']->get_id()); 
    $product_id     = $_product->get_id();
    $product_title  = $_product->get_title();
    $product_sku    = " - Product Number" . " " . $_product->get_sku();
    $product_desc   = $_product->get_short_description();
    $categories     = get_the_terms( $product_id, 'product_cat' );
    $product_type   = "Category:" . " " . implode(',' ,wp_get_post_terms( $_product->get_id(), $taxonomy, ['fields' => 'names']));
    $category_line  = join(', ', wp_list_pluck($categories, 'name'));
    $img            =  "" ;
    $pic = wp_get_attachment_url( $_product->get_image_id());
    


    global $current_user;
    $current_user = wp_get_current_user();
    $date = date("d.m.Y");
    
    $person ="";

    if(empty($current_user->user_firstname) || empty($current_user->user_lastname)) {
        $person = "some guy";
    } else {
        $person = $current_user->user_firstname . ' ' . $current_user->user_lastname;
    }

    //$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail'); 
    $pdf->AddPage('P','A4');
    
    $pdf->AliasNbPages();
    $pdf->SetY(40);
    $pdf->SetFont('Arial','BI',22);
    $pdf->setTExTColor(204,51,102);
    $pdf->Cell(0,20,$product_title,0,0,'C');
    $pdf->SetY(25);
    $pdf->setTextColor(66, 40, 14);
    $pdf->SetFont('Arial','',16);
    $pdf->Cell(0,75,$product_type . $product_sku,0,0,'C');
    
    
    
    if(!$pic) {
    $pdf->SetY(30);
    $pdf->Cell(0,20,'No picture has been set for this product.',0,0,'C');
    } else {
    $pdf->Image($pic,65,50,80);
    }
    $pdf->SetY(130);
    $pdf->Image(PRINT_IMG . 'line.png',-31,160,-132);


    $gallery_pics    = $_product->get_gallery_image_ids();
    foreach( $gallery_pics as $attachment_id ) {
        $image_link = wp_get_attachment_url( $attachment_id);
        if(!$image_link) {
            $pdf->SetY(177);
            $pdf->Cell(0,10,'No table has been set for this product.',0,0,'');

        } else {
            $pdf->Image($image_link,30,175,50);
            //$pdf->Cell(0,10,$image_link,0,0,'L');
        }
    }

    $pdf->SetY(177);
    $pdf->SetX(110);
    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(80,4,$product_desc,0,'LTR',false); 
    $pdf->SetY(250);
    $pdf->SetX(110);
    $pdf->SetFont('Arial','B',10);
    $pdf->MultiCell(80,4,$category_line,0,'LTR',false); 
    //$pdf->Cell(15,150,$img);
    

    //$pdf->Cell(15,150,$img);
    $pdf->SetY(266);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(0,10,'The catalogue was prepared by ' . $person . ' on ' . $date,0,0,'C');
endforeach;

// Last Page

/* $pdf->AddPage('P','A4');
$pdf->Image(PRINT_IMG . 'cover_2.png',0,0,-300); */



global $current_user;
$current_user = wp_get_current_user();

$person ="";

if(empty($current_user->user_firstname) || empty($current_user->user_lastname)) {
    $person = "some guy";
} else {
    $person = $current_user->user_firstname . ' ' . $current_user->user_lastname;
}

$pdf->SetAuthor($person);
$date = date('l jS \of F Y h:i:s A');
$filename = "The offer made by " . $person . " on " . ' ' . $date . ".pdf";
$title = $filename;
$pdf->SetTitle($title);
$pdf->Output('',$filename);

