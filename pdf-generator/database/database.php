<?php // 

if ( ! defined( 'WPINC' ) ) {
	die;
}


class Data_price {


	private static $_instance = null;

	public static function instance() {

		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
	return self::$_instance;

	}



	public function insert_price() {
	global $wpdb;


	$meta_key = "";
	$meta_value = "";
	$sql_if = "";
	$sql = "";


	$products = get_posts( array(
		'numberposts' => -1,
		'post_type'	  => 'product',
		'post_status' => 'publish',
		'meta_query'  => array('0' => array('key' => '_price', 'value' => '1', 'compare' => 'NOT EXISTS')
	) ,
));


if($products) {


    foreach($products as $product) : ;

	$table_name = $wpdb->prefix . 'postmeta';  
	$table = $wpdb->prefix . 'posts';  
	
	$meta_id = NULL;	
	$meta_key = "meta_key 	  = '_price'";
	$meta_value = "meta_value = '1'";

        $post_id = $product->ID;

        $sql_if = "SELECT post_id FROM $table_name WHERE post_id=$post_id AND $meta_key";
        
        $result =  $wpdb->query( $sql_if );	
        
        if($result) {
                return false;
        }; 
        if (empty($result)){
                $sql = " INSERT INTO {$table_name} SET meta_id='$meta_id', post_id='$post_id', $meta_key, $meta_value";
                $sql .= " ON DUPLICATE KEY UPDATE post_id='$post_id',$meta_key, $meta_value";
                return $wpdb->query( $sql );
        };

    endforeach;
   
	}
    

}

function __construct() {

add_action('wp_footer', [$this, 'insert_price'],10,2 );
add_action('admin_init', [$this, 'insert_price'],10,2 );
}

}


Data_price::instance();