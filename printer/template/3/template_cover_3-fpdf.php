<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
if ( ! defined( 'WPINC' ) ) {
	die;
}

//define('FPDF_FONTPATH', '/Sora/');

define( 'PRINT_IMG', plugin_dir_path( __FILE__ ) . '/images/');


require(ROOT . "tfpdf.php");


class PDF extends tFPDF
{
    protected $col = 0; 
    public $y0;     
    private $currentPageNumber = array();
    private $currentPage = 1; 
    private $lastPage = 1;

//Page header

function Header() {

    if($this->PageNo() >= 1)  {
   
        $this->Image(PRINT_IMG . 'logo.png',90,10,0,20);
        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $this->SetFont('DejaVu','',10);
        $this->Ln(20);
    } 

}//end header



public function AddPage($orientation = '', $size = '', $rotation = 0)
{
    parent::AddPage($orientation, $size, $rotation);
    $this->currentPage++; 
    $this->lastPage = $this->currentPage; 
    $this->currentPageNumber = $this->PageNo();
}

public function AddPageExtra($orientation = '', $size = '', $rotation = 0)
{
    parent::AddPage($orientation, $size, $rotation);
    $this->currentPage++; 
    $this->lastPage = $this->currentPage; 
    $this->currentPageNumber = $this->PageNo();
}

// Page footer

function Footer()
{
    $totalPages = $this->lastPage;
    

if($this->PageNo() != 1 && $this->PageNo() != $totalPages) {

    global $current_user;
    $current_user = wp_get_current_user();
    $date = date("d.m.Y");
    
    $person ="";
    
    if(empty($current_user->user_firstname) || empty($current_user->user_lastname)) {
        $person = "some guy";
    } else {
        $person = $current_user->user_firstname . ' ' . $current_user->user_lastname;
    }
    
    $this->SetY(1);
    $this->setTextColor(66, 40, 14);
    $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $this->SetFont('DejaVu','',8);
    $this->Cell(0,10,'The catalogue was prepared by ' . $person . ' on ' . $date,0,0,'L'); 


    $sikorski = "© Sikorski Sausages" . " " . Date("Y");
    $text  = "Every effort is taken to ensure that the ingredients and nutritional information listed here is accurate, however,";
    $text .= "data may change from time to time. Please always check the package for the most current information.";
    
    if (preg_match('/Â/', $sikorski)) {
        $sikorski = urldecode(str_replace("Â", "%C2", "© Sikorski Sausages" . " " . Date("Y")));
    } else {
        $sikorski = "©Sikorski Sausages" . " " . Date("Y");
    };

    $this->SetY(-20);
    $this->Image(PRINT_IMG . 'footer_line.png',-4, 280,-166);
    $this->SetY(-15);
    $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $this->SetFont('DejaVu','',8);
    $this->Cell(0,10,$sikorski,0,0,'L');
    $this->SetY(-12);
    $this->setX(60);
    $this->SetFont('DejaVu','',6);
    $this->MultiCell(120,3,$text,0, 0, 'C', true);
    $this->SetY(-15);
    $this->SetFont('DejaVu','',8);
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    } 
} // end footer


function ChapterTitle($num, $title) {
    $this->setTextColor(161, 137, 127);
    $this->SetFillColor(255, 255, 255);
    $this->ln(-5);

    $topMargin = 2;
    $this->Cell(0, $topMargin, "", 0, 1);

    $title_parts = explode(",", $title);
    $all_titles = array_merge($title_parts, $this->getAllObjectTitles());
    $unique_titles = array_values(array_unique($all_titles));

    $title_lines = array();

    $current_line = "$num.................................................................................................. ";
    $first_line_limit = 155;

    foreach ($unique_titles as $title) {
        $product_property = trim($title);
        $product_property_length = strlen($product_property);

        if (strlen($current_line) + $product_property_length + 2 <= $first_line_limit) {
            if (strpos($current_line, $product_property) === false) {
                if ($current_line !== "$num.................................................................................................. ") {
                    $current_line .= ", ";
                }
                $current_line .= $product_property;
            }
        } else {
            $title_lines[] = rtrim($current_line, ',');
            $current_line = str_repeat(" ", strlen($num)) . "   " . implode(", ", $title_lines);
        }
    }

    if (!empty(trim($current_line))) {
        $title_lines[] = rtrim($current_line, ',');
    }


    foreach ($title_lines as $line) {
        $this->setX(20);
        $this->SetFont('DejaVu', '', 10);
        $this->MultiCell(0, 0, $line, 0, 1, 'L', true);
    }

    $this->Ln(10);
    $this->y0 = $this->GetY();
}


function getAllObjectTitles() {
    $titles = array();
    $unique_titles = array_unique($titles);
    return $unique_titles;
}

function SetCol($col)
{
    $this->col = $col;
    $x = 10+$col*65;
    $this->SetLeftMargin($x);
    $this->SetX($x);
}

public function PrintChapter($num, $title)
{
    // $this->AddPage(); //Add chapter
    $this->ChapterTitle($num, $title);
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


$listed = array(
    '1' => 'TRADITIONAL KIELBASA',
    '2' => 'GRILLING SAUSAGES',
    '3' => 'DRIED SAUSAGES',
    '4' => 'WIENERS',
    '5' => 'HAMS',
    '6' => 'ALL OUR MEATS',
    '7' => 'BACONS AND ROASTS',
    '8' => 'DELI MEATS',
    '9' => 'SALAMI',
    '10' => 'GRAB AND GO',
    '11' => 'MEAT SNACKS'
);

$taxonomy = 'products_property';
$types = array();
foreach ($items as $item => $values) {
    $_product = wc_get_product($values['data']->get_id());
    $product_type = wp_get_post_terms($_product->get_id(), $taxonomy, ['order' => 'ASC', 'fields' => 'names']);
    $types[] = implode(', ', $product_type);
}


$unique_types = array_unique($types);

usort($unique_types, function ($a, $b) use ($listed) {
    $a_values = explode(', ', $a);
    $b_values = explode(', ', $b);
    $a_first_value = isset($a_values[0]) ? $a_values[0] : '';
    $b_first_value = isset($b_values[0]) ? $b_values[0] : '';

    $a_matched = isset($listed[array_search($a_first_value, $listed)]);
    $b_matched = isset($listed[array_search($b_first_value, $listed)]);

    if ($a_matched && $b_matched) {
        $a_position = array_search($a_first_value, $listed);
        $b_position = array_search($b_first_value, $listed);
        
        if ($a_position === $b_position) {
            return strcmp($a, $b); // Sort the values within the same group alphabetically
        } else {
            return $a_position - $b_position;
        }
    } elseif ($a_matched) {
        return -1;
    } elseif ($b_matched) {
        return 1;
    } else {
        return strcmp($a, $b);
    }
});

$i = 0;
$page = 0;
foreach ($unique_types as $type) {
    $i++;
    $pdf->PrintChapter($i, $type);
    while ($pdf->GetY() > $pdf->y0) {
        $pdf->AddPage();
        $pdf->SetY($pdf->y0);
    }
}

//Product Page

$a = 0;
$product_title[] = "";
$product_sku[]   = "";
$product_desc[]  = "";
$category_line[] = "";
$pic[]           = "";
$product_type[]  = "";
$items_per_page  = 3;
$prev_term = '';
$unique_terms = [];



$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->AddFont('dejavusans', 'B', 'DejaVuSans-Bold.ttf', true);
$pdf->SetFont('dejavusans', 'B', 16);


usort($items, function ($a, $b) use ($listed, &$prev_term) {
    $termsA = wp_get_post_terms($a['data']->get_id(), 'products_property', ['fields' => 'names']);
    $termsB = wp_get_post_terms($b['data']->get_id(), 'products_property', ['fields' => 'names']);

    $typeA = $termsA[0];
    $typeB = $termsB[0];
    $orderA = array_search($typeA, $listed);
    $orderB = array_search($typeB, $listed);

    if ($orderA !== false && $orderB !== false) {
        if ($orderA !== $orderB) {
            return $orderA - $orderB;
        }
    } elseif ($orderA !== false) {
        return -1;
    } elseif ($orderB !== false) {
        return 1;
    }

    $termComparison = strcmp(implode(', ', array_slice($termsA, 1)), implode(', ', array_slice($termsB, 1)));
    if ($termComparison !== 0) {
        return $termComparison;
    }

    $titleA = $a['data']->get_title();
    $titleB = $b['data']->get_title();
    return strcmp($titleA, $titleB);
});



foreach ($items as $key => $values) {
    $terms = wp_get_post_terms($values['data']->get_id(), 'products_property', ['fields' => 'names']);
    $current_term = $terms[0];

    if (!in_array($current_term, $unique_terms)) {
       $pdf->AddPage();     
       
        $pdf->Ln(1);
        $pdf->SetFont('dejavusans', 'B', 22);
        $pdf->setTextColor(9, 121, 105);
        $pdf->SetY(80); 
        $pdf->Cell(0, 5, $current_term, 0, 0, 'C');
        $pdf->Ln(6);

        $pdf->AddPage();

        $prev_term = $current_term;
        $unique_terms[] = $current_term;
        $a = 0;

    }

    if ($a == $items_per_page) {
        $pdf->AddPage();
        $a = 0;
        $page++;
    }



    $line = PRINT_IMG . 'line.png';
    $taxonomy = 'products_property';
    $_product = wc_get_product($values['data']->get_id());
    $product_id = $_product->get_id();
    $title = $_product->get_title();
    $sku = " - Product Number" . " " . $_product->get_sku();
    $desc = $_product->get_short_description();
    $categories = get_the_terms($product_id, 'product_cat');
    $type = "Category:" . " " . implode(', ', wp_get_post_terms($_product->get_id(), $taxonomy, ['fields' => 'names']));
    $category = join(', ', wp_list_pluck($categories, 'name'));
    $img =  "";
    $pic_url = wp_get_attachment_url($_product->get_image_id());
    $gallery_pics = $_product->get_gallery_image_ids();
    $gallery_image_urls = array();

    foreach ($gallery_pics as $pic_id) {
        $gallery_image_urls[] = wp_get_attachment_url($pic_id);
    }

    $product_title[] = $title;
    $product_sku[] = $sku;
    $product_desc[] = $desc;
    $category_line[] = $category . ' ' . $type;
    $pic[] = $pic_url;
    
    if (count($gallery_image_urls) > 0) {
        foreach ($gallery_image_urls as $gallery_image_url) {
            if ($gallery_image_url) {
                
                $pdf->Image($gallery_image_url, 150, $pdf->GetY()+5,40, 55);
                
                //$pdf->Image($line,0, $pdf->GetY(), -145, -132);
            }
        }
    } else {
        $pdf->SetFont('DejaVu', '', 8);
        $pdf->Cell(0, 10, 'No gallery images have been set for this product.', 0, 0, 'R');
    }
    

   
    $pdf->Ln(1);
    $pdf->SetFont('dejavusans', 'B', 15);
    $pdf->setTExTColor(187, 23, 42);
    $pdf->SetX(20);
    $pdf->Cell(40, 5, $title);
    $pdf->Ln(6);

    $pdf->setTextColor(161, 137, 127);
    $pdf->SetFont('DejaVu', '', 10);
    $pdf->SetX(20);
    $pdf->MultiCell(125, 4, $type . $sku);
    $pdf->Ln(10);

    if (!empty($pic_url)) {
        $pdf->Image($pic_url, 10, $pdf->GetY()-5, 45, 45);
    }

    $pdf->setTextColor(66, 40, 14);
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $pdf->SetFont('DejaVu','',8);
    $pdf->SetX(55);
    $pdf->MultiCell(85, 4,$desc);
    $pdf->Ln(15);
    

    if (($a + 1) % 3 !== 0) {
        $pdf->Ln(27);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Line(20, $pdf->GetY()-10, 210-20, $pdf->GetY()-10);
    } else {

        
    }
    $a++;
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

