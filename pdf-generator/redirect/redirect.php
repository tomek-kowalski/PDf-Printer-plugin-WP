<?php

namespace WPC\Redirect;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Redirect {




public function start_redirect()
{
add_filter( 'login_redirect',[$this,'my_log'],10,3);

}


public function my_log($url,$request,$user ) {

    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if ( $user->has_cap( 'administrator' ) ) {
            $url = admin_url();
        } else {
            $url = home_url( '/index.php/catalogue/');
        }
    }
    return $url;
}


}


$red = new Redirect();
$red->start_redirect();

