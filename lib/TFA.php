<?php
/*
* @package 2FA
* @author Stefan Fodor (stefan@unserialized.dk)
 * Brain is here. a controller more of less
*/

require_once 'BasicPlugin.php';

/**
 * Class TFA
 * Main brains of the operation
 */
class TFA extends BasicPlugin {

    /*
     * Class variables
     */
    protected $prefix='2fa_';

    /**
     * Get Singleton singleton
     *
     * @return object|void
     */
    public static function instance() {
        return parent::instance( __CLASS__ );
    }

    /**
     * Constuctor
     */
    public function __construct() {

        //add hooks
        $this->addHook( 'login_form', 'loginFormInjection' );

        //add filter
        $this->addFilter( 'authenticate', array( 'verifyToken', 100, 3 ) );

        //add menu pages
        $this->addMenuPage( "2 Factor Authentication", "2 Factor Auth", "tfa_settings", "2fa.php" );
    }

    /**
     * Method called automatically on plugin activation
     */
    public function install() {

        //first time run, generate new secret key
        $this->loadDependencies();
        $this->update_option('secret_key', Authenticator::createSecret() );
    }

    /**
     * HTML snippet with code input
     */
    public function loginFormInjection() {
        //inject only if enabled
        if( $this->get_option('is_enabled') ) {
            $this->inc( "/view/login_form_addition.html" );
        }
    }

    /**
     * Verifies an user submitted token
     *
     * @param $user
     * @param $username
     * @param $password
     * @return WP_Error
     */
    public function verifyToken( $user, $username, $password ) {

        //do the check only if the login did not failed already
        //and if the two factor is enabled
        if( !is_wp_error($user) && $this->get_option('is_enabled') ) {

            $this->loadDependencies();

            $isValid = Authenticator::validateToken(
                $this->get_option('secret_key'),   //secret
                $_POST["token"]                 //user submitted token
            );

            return $isValid ? $user : new WP_Error("incorrect_password", "Invalid Token!");

        } else { //return the original result
            return $user;
        }

    }

    /**
     * Return a link to the secret key as QR Code
     *
     * @return string
     */
    public function getQRCode() {
        $urlencoded = urlencode( 'otpauth://totp/' . get_bloginfo('name') . '?secret=' . $this->get_option('secret_key') );
        return 'https://chart.googleapis.com/chart?chs=250x250&chld=M|0&cht=qr&chl='.$urlencoded.'';
    }

    /**
     * Create and save new secret
     */
    public function generateNewSecret() {
        $this->loadDependencies();
        $this->update_option('secret_key', Authenticator::createSecret() );
    }

    /*
     * Load class dependencies for the auth algorithm
     *
     * Since it is rare that this are used, load them only when needed
     */
    private function loadDependencies() {
        $this->req("/lib/Base32.php");
        $this->req("/lib/Authenticator.php");
    }

}