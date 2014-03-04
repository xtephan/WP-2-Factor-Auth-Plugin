<?php
/**
 * @package 2FA
 */
/*
Plugin Name: 2Factor Authentication
Plugin URI: N/A
Description: 2 Factor Authentication in WP
Version: 0.9
Author: Stefan Fodor
Author URI: http://unserialized.dk
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*
 * Add the field for the token at the end of the form
 */
add_action( 'login_form', function() {
    include dirname( __FILE__ ) . '/form.html';
});

/*
 * Hook up to authenticate filter to verify the token
 * More info here: http://codex.wordpress.org/Plugin_API/Filter_Reference/authenticate
 */
add_filter(
    'authenticate', //filter name
    'verify_token', //callable function
    100,            //priority 100, save it for last
    3               //expecting 3 parameters
);

/*
 * Verify the token callable function
 */
function verify_token( $user, $username, $password ) {

    //do the check only if the login did not failed already
    if( !is_wp_error($user) ) {

        //load the required library
        require_once dirname( __FILE__ ) .'/google_authenticator.php';

        //check if valid
        $isValid = GoogleAuthenticator::validateToken(
            get_option('2fa_secret_key'),   //secret
            $_POST["token"]                 //user submitted token
        );

        //return result;
        return $isValid ? $user : new WP_Error("2fa_fail","Invalid Token!");
    }

    //return the original result
    return $user;
}

/*
 * Register the admin file
 */
if ( is_admin() ) {
    require_once dirname( __FILE__ ) . '/admin.php';
}