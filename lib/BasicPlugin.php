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

    protected $basePath = null;     //<! Plugin basepath

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
     * Setups a plugin
     */
    protected function setup() {

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

        //plugin basepath
        $this->basePath = dirname(dirname(__FILE__));

        //for plugin activation
        if( !$this->get_option('first_time') ) {
            $this->install();
            $this->update_option('first_time',true);
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