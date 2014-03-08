<?php
/**
 * Class BasicPlugin
 * @author Stefan Fodor (stefan@unserialized.dk)
 * Collection of convenience methods for plugins
 */
class BasicPlugin {

    /*
     * Class variables
     */
    private static $__instance = null; //<! Singleton

    protected $hooks    = array();   //<! List of hooks
    protected $filters  = array();   //<! List of filters
    protected $menuPages = array(); //<! Menu pages

    protected $basePath = null;     //<! Plugin basepath
    protected $baseUrl = null;      //<! Plugin base url

    /**
     * Singleton implementation
     *
     * @uses this::setup
     * @return object
     */
    public static function instance( $className = '' ) {
        if( ! is_a( self::$__instance, $className ) ) {
            self::$__instance = new $className;
            self::$__instance->setup();
        }

        return self::$__instance;
    }

    /**
     * Queues a hook for loading
     *
     * @param string $hook
     * @param string $action
     * @return bool
     * @throws BadMethodCallException
     */
    protected function addHook( $hook = null, $action = null ) {

        //sanity check
        if( empty($hook) && !is_string($hook) ) {
            throw new BadMethodCallException("Invalid hook name");
        }

        if( empty($action) && !is_string($action) ) {
            throw new BadMethodCallException("Invalid action name");
        }

        $this->hooks[] = array( $hook, $action );

        return true;
    }

    /**
     * Queues a filter for loading
     *
     * @param string $filter
     * @param string $action
     * @return bool
     * @throws BadMethodCallException
     */
    protected function addFilter( $filter = null, $action = null ) {

        //sanity check
        if( empty($filter) && !is_string($filter) ) {
            throw new BadMethodCallException("Invalid filter name");
        }

        if( empty($filter) && !is_string($filter) ) {
            throw new BadMethodCallException("Invalid action name");
        }

        $this->filters[] = array( $filter, $action );

        return true;
    }

    /**
     * Queues up menu pages
     *
     * @param $page_title
     * @param $menu_title
     * @param $capability
     * @param $file
     */
    protected function addMenuPage(  $page_title, $menu_title, $tag, $file, $capability = 'manage_options'  ) {
        $this->menuPages[] = array(
            $page_title,
            $menu_title,
            $capability,
            $tag,
            $file
        );
    }

    /**
     * Register the menu pages in the queue
     */
    public function registerMenuPages() {

        //use this to send the controller to closure
        $that = $this;

        foreach( $this->menuPages as $thisMenuPage ) {

            $viewFile = $this->basePath . "/view/dashboard/" . $thisMenuPage[4];

            add_menu_page(
                $thisMenuPage[0],
                $thisMenuPage[1],
                $thisMenuPage[2],
                $thisMenuPage[3],
                function() use ( $viewFile, $that )  {
                    include $viewFile;
                }
            );
        }

        //load css and js scripts
        if( file_exists($this->basePath . "/view/dashboard/main.css") ) {
            wp_register_style( $this->prefix . 'css_main.css', $this->baseUrl . "/view/dashboard/main.css" );
        }

        if( file_exists($this->basePath . "/view/dashboard/main.js") ) {
            wp_register_script( $this->prefix . 'js_main.js', $this->baseUrl . "/view/dashboard/main.js", array('jquery') );
        }
    }

    /**
     * Load dashboardCSS
     */
    public function loadCSS() {
        wp_enqueue_style( $this->prefix . 'css_main.css' );
    }

    /**
     * Load dashboardJS
     */
    public function loadJS() {
        wp_enqueue_script('jquery');
        wp_enqueue_script( $this->prefix . 'js_main.js' );
    }

    /**
     * Setups a plugin
     */
    protected function setup() {

        //plugin basepath and base url
        $this->basePath = dirname(dirname(__FILE__));
        $this->baseUrl = dirname(plugin_dir_url(__FILE__));

        //init menu pages
        if( !empty( $this->menuPages ) ) {
            $this->addHook( 'admin_menu', 'registerMenuPages' );
        }

        //for plugin activation
        if( !$this->get_option('first_time') ) {
            $this->install();
            $this->update_option('first_time',true);
        }

        //init filters
        if( is_array( $this->filters ) ) {

            foreach( $this->filters as $thisFilter ) {
                add_filter(
                    $thisFilter[0],                                 //FilterName
                    array($this, $thisFilter[1][0]),                   //function to use
                    isset($thisFilter[1][1]) ? $thisFilter[1][1] : 10,    //priority
                    isset($thisFilter[1][2]) ? $thisFilter[1][2] : 1      //accepted args number
                );
            }

        }

        //init hooks
        if( is_array( $this->hooks ) ) {

            foreach( $this->hooks as $thisHook ) {
                add_action( $thisHook[0], array($this, $thisHook[1]) );
            }

        }

    }

    /**
     * Saves the options with prefix appended
     * @param $option_name
     */
    public function get_option( $option_name ) {
        return get_option( $this->prefix . $option_name );
    }

    /**
     * Saves the options with prefix appended
     * @param $option_name
     */
    public function update_option( $option_name, $value = null ) {
        update_option( $this->prefix . $option_name, $value );
    }

    /**
     * Includes a file
     * @param $fileName
     */
    protected function inc( $fileName ) {
        include( $this->basePath . $fileName );
    }

    /**
     * Requires a file
     * @param $fileName
     * @param $once
     */
    protected function req( $fileName, $once = true ) {
        if( $once ) {
            require_once( $this->basePath . $fileName );
        } else {
            require( $this->basePath . $fileName );
        }
    }
}