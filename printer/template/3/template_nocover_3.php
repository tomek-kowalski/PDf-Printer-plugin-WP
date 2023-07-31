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

// First page
$pdf->AddPage('P','A4');
$pdf->Image(PRINT_IMG . 'cover_1.png',0,0,-300);


//Contents Page

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
/* $terms = get_terms( array(
	'taxonomy'   =>  $taxonomy,
    'field'      =>  'name',
	'hide_empty' =>  true
) );  */

foreach($items as $item => $values) : ;

$taxonomy       = 'products_property';





$_product               =  wc_get_product( $values['data']->get_id()); 
$product_type           =  wp_get_post_terms( $_product->get_id(), $taxonomy, ['order' => 'ASC', 'fields' => 'names', ]);

$input                  = implode('',$product_type  );


$i<count($items); $i++;
$pdf->PrintChapter($i,$input ,'test 1');


endforeach;

//Product Page


$a = 0;
$count = 0;
$product_title[] = "";   
$product_sku[]   = "";
$product_desc[]  = "";
$category_line[] = "";
$pic[]           = "";
$product_type[]  = "";

// Loop through items array
foreach ($items as $item => $values) {
  
  
    $taxonomy = 'products_property';
    $_product = wc_get_product($values['data']->get_id()); 
    $product_id = $_product->get_id();
    $title = $_product->get_title();
    $sku = " - Product Number" . " " . $_product->get_sku();
    $desc = $_product->get_short_description();
    $categories = get_the_terms($product_id, 'product_cat');
    $type = "Category:" . " " . implode(',' ,wp_get_post_terms($_product->get_id(), $taxonomy, ['fields' => 'names']));
    $category = join(', ', wp_list_pluck($categories, 'name'));
    $img =  "";
    $pic_url = wp_get_attachment_url($_product->get_image_id());
    $gallery_pics = $_product->get_gallery_image_ids();
    $gallery_image_urls = array();


        // Check for duplicates
        if (in_array(strtolower($title), array_map('strtolower', $product_title)) 
         && in_array(strtolower($sku), array_map('strtolower', $product_sku)) 
         && in_array(strtolower($desc), array_map('strtolower', $product_desc))
         && in_array(strtolower($category), array_map('strtolower', $category_line)) 
         && in_array(strtolower($pics_url), array_map('strtolower', $pic))
         && in_array(strtolower($type), array_map('strtolower', $product_type))
            ) {
        continue;
        } 
        if ($count % 2 == 0) {
            // add element to occurrences array if counter is even
            if (
                
            !in_array($item,$product_title ) &&
            !in_array($item,$product_sku ) &&
            !in_array($item,$product_desc ) &&
            !in_array($item,$category_line ) &&
            !in_array($item,$pic ) &&
            !in_array($item,$product_type ) )
                {
            array_push($product_title, $item);
            array_push($product_sku, $item);
            array_push($product_desc, $item);
            array_push($category_line, $item);
            array_push($pic, $item);
            array_push($product_type, $item);
            }
        } else {
            // remove element from occurrences array if counter is odd
            $key   = array_search($item,$product_title);
            $key_1 = array_search($item,$product_sku);
            $key_2 = array_search($item,$product_desc);
            $key_3 = array_search($item,$category_line);
            $key_4 = array_search($item,$pic);
            $key_5 = array_search($item,$product_type);
            

            
            if ($key !== false) {
            unset($product_title[$key]);
            unset($product_sku[$key_1]);
            unset($product_desc[$key_2]);
            unset($category_line[$key_3]);
            unset($pic[$key_4]);
            unset($product_type[$key_5]);
            }
        }
      
        // Add unique items to arrays
        $product_title[] = $title;
        $product_sku[] = $sku;
        $product_desc[] = $desc;
        $product_type[] = $type;
        $category_line[] = $category;
        $pic[] = $pic_url;

    foreach ($gallery_pics as $attachment_id) :
        // Get the URL for the image
        $image_url = wp_get_attachment_url($attachment_id);
    
        $gallery_image_urls[] = $image_url; // Pointer 1
    
    endforeach;


    if ($a % 2 == 0) {
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $warning = "";

        $warning_1 = "";


        $pdf->SetY(28);
        $pdf->SetFont('Arial','BI',22);
        $pdf->setTExTColor(204,51,102);
        $pdf->Cell(0,20,$title,0,0,'L');
        
        $pdf->SetY(8);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,75,$type . $sku,0,0,'L');
        
        
        
       if(!$pic_url) {
        $pdf->SetY(100);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,20,$warning,0,0,'L');
        } else {
        $pdf->Image($pic_url,20,60,40);
        }
        
        $pdf->SetY(50 );
        $pdf->SetX(73);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(60,4,$desc,0,'LTR',false); 
        $pdf->SetY(130);
        $pdf->SetX(73);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(80,4,$category,0,'LTR',false);   
        
        
        if (count($gallery_image_urls) > 0) {
            $pdf->SetY(85);
            foreach ($gallery_image_urls as $gallery_image_url) {
                if ($gallery_image_url) {
                    $pdf->Image($gallery_image_url, 150, 45, 50);
                }
            }
        } else {
            $pdf->SetY(65);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(0, 10, 'No gallery images have been set for this product.', 0, 0, 'R');
        }   

    } 

    $a++;

    $pdf->SetY(130);
    $pdf->Image(PRINT_IMG . 'line.png',-31,145,-132);

    // Check if it's the last item on the page
   if ($a % 2 == 0 || $a == count($items)) {
        
    
    $pdf->SetFont('Arial', 'BI', 22);
        $pdf->setTExTColor(204, 51, 102);
        $pdf->SetY(150);
        $pdf->Cell(0, 20, $title, 0, 0, 'L');
        $pdf->SetY(130);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 75, $type . $sku, 0, 0, 'L');

        if (!$pic_url) {
            $pdf->SetY(176);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(0, 20, 'No picture has been set for this product.', 0, 0, 'L');
        } else {
            $pdf->Image($pic_url, 20, 176, 40);
        }

        $pdf->SetY(172);
        $pdf->SetX(73);
        $pdf->SetFont('Arial', '', 10);
        $pdf->setTextColor(66, 40, 14);
        $pdf->MultiCell(60, 4, $desc, 0, 'LTR', false);
        $pdf->SetY(249);
        $pdf->SetX(73);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(80, 4, $category, 0, 'LTR', false);


// Display gallery images
if (count($gallery_image_urls) > 0) {
    $pdf->SetY(165);
    $pdf->SetFont('Arial', '', 8);
    foreach ($gallery_image_urls as $gallery_image_url) {
        if ($gallery_image_url) {
            $pdf->Image($gallery_image_url, 150, 166, 50);
        }
    }
} else {
    $pdf->SetY(165);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 10, 'No gallery images have been set for this product.', 0, 0, 'R');
}
     
}  

    global $current_user;
$current_user = wp_get_current_user();
$date = date("d.m.Y");

$person ="";

if(empty($current_user->user_firstname) || empty($current_user->user_lastname)) {
    $person = "some guy";
} else {
    $person = $current_user->user_firstname . ' ' . $current_user->user_lastname;
}

$pdf->SetY(265);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,10,'The catalogue was prepared by ' . $person . ' on ' . $date,0,0,'L'); 


}


// Last Page

$pdf->AddPage('P','A4');
$pdf->Image(PRINT_IMG . 'cover_2.png',0,0,-300);



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

