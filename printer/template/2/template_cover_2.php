<?php

//namespace Loading;



if ( ! defined( 'WPINC' ) ) {
	die;
}

//define('FPDF_FONTPATH', '/Sora/');

define( 'PRINT_IMG', plugin_dir_path( __FILE__ ) . '/images/');



require(ROOT . "vendor/autoload.php");


class MYPDF extends TCPDF
{
    private $pageNumber = [];
    protected $col = 0;
    public $y0;
    private $currentPageNumber = array();
    private $currentPage = 1;
    private $lastPage = 1;
    private $totalPages = 1;
    private $pageNumbers = array();

    // Page header
    public function Header()
    {
        if ($this->getPage() >= 1) {
            $this->Image(PRINT_IMG . 'logo.png', 90, 10, 0, 20);
            $this->SetFont('dejavusans', '', 10);
            $this->Ln(20);
        }
    }

    public function AddNewPage($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->currentPage++;
        $this->lastPage = $this->currentPage;
        $this->currentPageNumber = $this->getPage();
    }
    public function AddNewPageExtra($orientation = '', $size = '', $rotation = 0)
    {
        parent::AddPage($orientation, $size, $rotation);
        $this->currentPage++;
        $this->lastPage = $this->currentPage;
        $this->currentPageNumber = $this->getPage();
    }

    // Page footer
    public function Footer()
    {
        $totalPages = $this->lastPage;
        $this->pageNumbers[] = $this->currentPageNumber;

        if ($this->getPage() != 1 && $this->getPage() != $totalPages) {
            global $current_user;
            $current_user = wp_get_current_user();
            $date = date("d.m.Y");

            $person = "";

            if (empty($current_user->user_firstname) || empty($current_user->user_lastname)) {
                $person = "some guy";
            } else {
                $person = $current_user->user_firstname . ' ' . $current_user->user_lastname;
            }

            $this->SetY(1);
            $this->SetX(15);
            $this->setTextColor(66, 40, 14);
            $this->SetFont('dejavusans', '', 8);
            $this->Cell(0, 10, 'The catalogue was prepared by ' . $person . ' on ' . $date, 0, 0, 'L');

            $sikorski = "© Sikorski Sausages" . " " . date("Y");
            $text  = "Every effort is taken to ensure that the ingredients and nutritional information listed here is accurate, however,";
            $text .= "data may change from time to time. Please always check the package for the most current information.";

            if (preg_match('/Â/', $sikorski)) {
                $sikorski = urldecode(str_replace("Â", "%C2", "© Sikorski Sausages" . " " . date("Y")));
            } else {
                $sikorski = "©Sikorski Sausages" . " " . date("Y");
            }

            
            $this->Image(PRINT_IMG . 'footer_line.png', 0, 280, 208);
            $this->SetY(-15);
            $this->SetX(15);
            $this->SetFont('dejavusans', '', 10);
            $this->Cell(0, 10, $sikorski, 0, 0, 'L');
            $this->SetY(-12);
            $this->setX(65);
            $this->SetFont('dejavusans', '', 5);
            $this->MultiCell(105, 3, $text, 0, 'L', false);
            $this->SetFont('dejavusans', '', 10);
            $this->MultiCell(33, 0, 'Page ' . $this->getAliasNumPage() . ' of ', 0, 'L', false, '', 172, -12);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetX($x-14, $y-15); 
            $this->Cell(10, 0, $this->getAliasNbPages(), 0, 0, 'L');
        }
    }

    function ChapterTitle($num, $title, $pageNumber ='', $options = array())
    {
        $this->setTextColor(66, 40, 14);
        $this->SetFillColor(255, 255, 255);
        $this->ln(-5);
    
        $topMargin = 2;
        $this->Cell(0, $topMargin, "", 0, 1);
    
        $this->SetFont('dejavusans', '', 10);
        $this->SetLeftMargin(20);
        $cellWidth = 160;
        $lineHeight = 5;
    
        $titleParts = explode(",", $title);
        $numWidth = $this->GetStringWidth($num . ' ');
    
        $displayDots = isset($options['displayDots']) ? $options['displayDots'] : true;
    
        $line = '';
        foreach ($titleParts as $index => $part) {
            $part = trim($part);
            $partWidth = $this->GetStringWidth($part);
    
            $remainingWidth = $cellWidth - $numWidth - $this->GetStringWidth($line);
    
            if ($partWidth > $remainingWidth) {
                $this->Cell($cellWidth, $lineHeight, $line, 0, 1, 'L', false);
                $line = '';
            }
    
            if ($line !== '') {
                $line .= ', ';
            }
            $line .= $part;
        }
    
        if (isset($options['fontColor'])) {
            $fontColor = $options['fontColor'];
            $this->setTextColor($fontColor[0], $fontColor[1], $fontColor[2]);
        }
    
        if (isset($options['fontSize'])) {
            $fontSize = $options['fontSize'];
            $this->SetFont('dejavusans', '', $fontSize);
        }
    
        if (!isset($options['disablePageNumber']) || !$options['disablePageNumber']) {
            if (!isset($options['disableNum']) || !$options['disableNum']) {
                $lineWithNum = $num . ' ' . $line;
            } else {
                $lineWithNum = $line;
            }
    
            $alignment = isset($options['alignment']) ? $options['alignment'] : 'L';
            $dotsWidth = $cellWidth - $numWidth - $this->GetStringWidth($line) - $this->GetStringWidth('  ');
            if ($dotsWidth >= 0) {
                $dots = str_repeat('.', $dotsWidth / $this->GetStringWidth('.'));
            } else {

                $dots = ''; 
            }
            $text = $line . '  ' . $dots;

            if (is_int($pageNumber) && $pageNumber > 0) {
                $text .= ' ' . $pageNumber;
            }

            if ($displayDots) {
                $this->Cell($cellWidth, $lineHeight, $text . ' ' . $pageNumber, 0, 1, $alignment, false);
            } else {
                $this->Cell($cellWidth, $lineHeight, $lineWithNum, 0, 1, $alignment, false);
            }
        }
    
        $this->Ln($lineHeight);
        $this->y0 = $this->GetY();
    }
    

    function getAllObjectTitles()
    {
        $titles = array();
        $unique_titles = array_unique($titles);
        return $unique_titles;
    }
    
    function SetCol($col)
    {
        $this->col = $col;
        $x = 10 + $col * 65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }
    
    public function PrintChapter($num, $title, $pageNumber)
    {
        $this->pageNumber = $this->getPage(); 
        $this->ChapterTitle($num, $title, $pageNumber); 
    }
    

} //class end
$pdf = new MYPDF();

// First page
$pdf->AddNewPage('P','A4');
$pdf->Image(PRINT_IMG . 'cover_1.png',0,0,-300);


//Contents Page - moved

if ( is_null( WC()->cart ) ) {
    wc_load_cart();
}

global $woocommerce;
$page ='';
$product = "";

$items = $woocommerce->cart->get_cart();


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


//Product Page

$a = 0;
$count = 0;
$product_title[] = "";
$product_sku[]   = "";
$product_desc[]  = "";
$category_line[] = "";
$pic[]           = "";
$product_type[]  = "";
$items_per_page  = 2;
$prev_term = '';
$unique_terms = [];
$firstElements = [];
$secondElements = [];
$thirdElements = [];


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

//Product Page


$a = 0;
$count = 0;
$product_title[] = "";   
$product_sku[]   = "";
$product_desc[]  = "";
$category_line[] = "";
$pic[]           = "";
$product_type[]  = "";

$pageNumbers = $pdf->getPage(); 
$pageNumbers = []; 
foreach ($items as $item => $values) {
    $terms = wp_get_post_terms($values['data']->get_id(), 'products_property', ['fields' => 'names']);
    $current_terms = implode(', ', $terms);

    if (!in_array($current_terms, $unique_terms)) {

        $pdf->AddNewPage();

        $pageNumber = $pdf->getPage(); 
        
        $pdf->Ln(1);
        $pdf->SetFont('dejavusans', 'B', 22);
        $pdf->setTextColor(9, 121, 105);
        $pdf->SetY(80); 
        
        $num = $item;
        $options = array(
            'disableNum' => true,
            'fontColor' => array(9, 121, 105),    
            'fontSize' => 22,                    
            'displayDots' => false,              
            'displayPageNumber' => false,        
            'alignment' => 'C'                   
        );

        $pdf->ChapterTitle($num, $current_terms, $pageNumber, $options);
        $pdf->Ln(6);

        $prev_term = $current_terms;
        $page++;
        $unique_terms[] = $current_terms;

        $a = 0;

        }
  
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
        $pdf->AddNewPage();
        $warning = "";

        $warning_1 = "";

        if (count($gallery_image_urls) > 0) {
            foreach ($gallery_image_urls as $gallery_image_url) {
                if ($gallery_image_url) {
                    
                    $pdf->Image($gallery_image_url, 145, $pdf->GetY()+60,45);
                    
                    //$pdf->Image($line,0, $pdf->GetY(), -145, -132);
                }
            }
        } else {
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->Cell(0, 10, 'No gallery images have been set for this product.', 0, 0, 'R');
        } 


        $pdf->Ln(1);
        $pdf->SetFont('dejavusans', 'BI', 22);
        $pdf->setTExTColor(187, 23, 42);
        $pdf->SetXY(20, $pdf->GetY()+35);
        $pdf->Cell(40, 5, $title);
        $pdf->Ln(6);
    
        $pdf->setTextColor(161, 137, 127);
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->SetXY(20, $pdf->GetY()+4);
        $pdf->MultiCell(125, 4, $type . $sku,0,'L');
        $pdf->Ln(10);
    
        if (!empty($pic_url)) {
            $pdf->Image($pic_url, 10, $pdf->GetY()-5, 65, 65);
        }
    
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('dejavusans','',8);
        $pdf->SetX(75);
        $pdf->MultiCell(65, 4,$desc,0,'L');
        $pdf->Ln(7);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('dejavusans','B',7);
        $pdf->SetX(55);
    
        $category_names = array();
        foreach ($categories as $category) {
            $category_thumbnail = get_term_meta($category->term_id, 'thumbnail_id', true);
            
            if (empty($category_thumbnail)) {
                $category_names[] = $category->name;
    
            }
        }
        $category_names = array_unique($category_names);
        $category_row = implode(', ', $category_names);
        $pdf->SetX(75);
        $pdf->MultiCell(70, 4, $category_row, 0, 'L');
        $pdf->ln(1);

        $pdf->Ln(5);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Line(20, $pdf->GetY()+25, 210-20, $pdf->GetY()+25);
        $pdf->Ln(5);
    
    
        $pdf->SetX($pdf->getX()+75);
        $pdf->SetY($pdf->GetY()- 5);
        $category_thumbnails = array();
    
    foreach ($categories as $category) {
        $category_thumbnail = get_term_meta($category->term_id, 'thumbnail_id', true);
        
        if (!empty($category_thumbnail)) {
            $category_thumbnails[] = $category_thumbnail;
        }
    }
    
    
    $pdf->SetX(75);
    $pdf->SetY($pdf->GetY()+5);
    
    foreach ($category_thumbnails as $thumbnail_id) {
        $thumbnail_url = wp_get_attachment_url($thumbnail_id);
        $pdf->SetX($pdf->getX()+2);
        $pdf->Image($thumbnail_url, $pdf->GetX()+54,$pdf->GetY(), 7,7); 
        $pdf->SetX($pdf->GetX() + 8);
        }
        
        

    } 

    $a++;   

    // Check if it's the last item on the page

   if (($a % 2 == 0 && $a != count($items))){

    
        $pdf->SetFont('dejavusans', 'BI', 22);
        $pdf->setTExTColor(187, 23, 42);
        $pdf->SetY(150);
        $pdf->Cell(0, 20, $title, 0, 0, 'L');
        $pdf->SetY($pdf->getY()-20);
        $pdf->setTextColor(161, 137, 127);
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->Cell(0, 75, $type . $sku, 0, 0, 'L');

        if (!$pic_url) {
            $pdf->SetY(176);
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->Cell(0, 20, 'No picture has been set for this product.', 0, 0, 'L');
        } else {
            $pdf->Image($pic_url, 10, 180, 55);
        }

        $pdf->SetY($pdf->getY()+50);
        $pdf->SetX(73);
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->setTextColor(66, 40, 14);
        $pdf->MultiCell(60, 4, $desc, 0, 'LTR', false);
        $pdf->Ln(7);
        $pdf->setTextColor(66, 40, 14);
        $pdf->SetFont('dejavusans','B',7);
        $pdf->SetX(75);

        $category_names = array();
        foreach ($categories as $category) {
            $category_thumbnail = get_term_meta($category->term_id, 'thumbnail_id', true);
            
            if (empty($category_thumbnail)) {
                $category_names[] = $category->name;
    
            }
        }
        $category_names = array_unique($category_names);
        $category_row = implode(', ', $category_names);
        $pdf->SetX(73);
        $pdf->MultiCell(70, 4, $category_row, 0, 'L');
        $pdf->ln(1);
    
    
    
        $pdf->SetX($pdf->getX()+73);
        $pdf->SetY($pdf->GetY()- 5);
        $category_thumbnails = array();
    
    foreach ($categories as $category) {
        $category_thumbnail = get_term_meta($category->term_id, 'thumbnail_id', true);
        
        if (!empty($category_thumbnail)) {
            $category_thumbnails[] = $category_thumbnail;
        }
    }
    
    $pdf->SetX(73);
    $pdf->SetY($pdf->GetY()+15);
    
    foreach ($category_thumbnails as $thumbnail_id) {
        $thumbnail_url = wp_get_attachment_url($thumbnail_id);
        $pdf->SetX($pdf->getX()+2);
        $pdf->Image($thumbnail_url, $pdf->GetX()+52,$pdf->GetY(), 7,7); 
        $pdf->SetX($pdf->GetX() + 8);
    }


// Display gallery images
if (count($gallery_image_urls) > 0) {
    $pdf->SetY(165);
    $pdf->SetFont('dejavusans', '', 8);
    foreach ($gallery_image_urls as $gallery_image_url) {
        if ($gallery_image_url) {
            $pdf->Image($gallery_image_url, 145, $pdf->GetY(),45);
        }
    }
} else {
    $pdf->SetY(165);
    $pdf->SetFont('dejavusans', '', 8);
    $pdf->Cell(0, 10, 'No gallery images have been set for this product.', 0, 0, 'R');
}
   
}  
//$a++;
if (isset($pageNumber) && $pageNumber > 0 && !in_array($pageNumber, $pageNumbers)) {
    $pageNumbers[] .= $pageNumber;
    //error_log('page:' . $pageNumber);
} 
}


// Last Page

$pdf->SetMargins(0, 0, 0); // Set all margins to 0
$pdf->AddNewPage('P', 'A4');
$pageWidth = $pdf->getPageWidth();
$imageWidth = $pageWidth;
$pdf->Image(PRINT_IMG . 'cover_2.png', 0, 0, $imageWidth);


//Contents Page

$pdf->AddNewPage();
$pdf->Image(PRINT_IMG . 'table-contents.png', 5, 50, 220);
$pdf->SetY(90);


$pdf->setTextColor(66, 40, 14);
$pdf->SetFillColor(255, 255, 255);

$topMargin = 2;
$pdf->Cell(0, $topMargin, "", 0, 1);

$pdf->SetFont('dejavusans', '', 10);
$pdf->SetLeftMargin(20);
$cellWidth = 160;
$lineHeight = 4;



foreach ($unique_types as $index => $type) {
    $pageNumber = isset($pageNumbers[$index]) ? $pageNumbers[$index] + 1 : '';
    $pageNumberWidth = $pdf->GetStringWidth($pageNumber); 
    $dotsWidth = $cellWidth - $pdf->GetStringWidth($type) - $pdf->GetStringWidth('  ') - $pdf->GetStringWidth('  ')- $pageNumberWidth;
    $maxDots = floor($dotsWidth / $pdf->GetStringWidth('.'));
    $dots = str_repeat('.', $maxDots);
    
    $pageNumber = isset($pageNumbers[$index]) ? $pageNumbers[$index] + 1 : '';
    $pageNumberWidth = $pdf->GetStringWidth($pageNumber); 

    $textWidth = $cellWidth - $pageNumberWidth;
    $text = $type . '  ' . $dots . '  ';

    $pdf->Cell($textWidth, $lineHeight, $text, 0, 0, 'L', false); 
    $pdf->Cell($pageNumberWidth, $lineHeight, $pageNumber, 0, 1, 'R', false); 
    $pdf->Ln($lineHeight);
    $pdf->y0 = $pdf->GetY();

    while ($pdf->GetY() > $pdf->y0) {
        $pdf->AddNewPage();
        $pdf->SetY($pdf->y0);
    }
}


$pdf->movePage($pdf->getPage(), 2);



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
$pdf->Output($filename);

