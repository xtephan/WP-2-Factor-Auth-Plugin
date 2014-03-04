<?php
/*
 * @package 2FA
 */

/*
 * Register settings page in menu
 */
add_action( 'admin_menu', 'register_menu_page' );

function register_menu_page(){
    add_menu_page( '2 Factor Authentication', '2 Factor Auth', 'manage_options', 'tfa_settings', 'tfa_menu_page');
}

function tfa_menu_page(){
    require dirname( __FILE__ ) . '/admin_view.php';
}