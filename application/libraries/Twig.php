<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Twig Library
 */


/**
 * Twig Template Library Wrapper
 */
class Twig {

    /**
     * @var Twig_Environment
     */
     protected $twig_instance;
     private $root;
     private $CI;

    /**
     * Twig constructor
     */
     public function __construct() {

        $this->CI = & get_instance();
        $this -> root = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        //HOST
        $this -> root .= '://' . $_SERVER['HTTP_HOST'];

        // All these settings might be loaded from
        // the a config file if you want. Just store
        // them there and fetch the values as:
        // $this->CI->config->item('some_value');
        #$laSettings['debug']            = false;
        $laSettings['charset']          = 'utf-8';
        #$laSettings['base_template_class'] = 'Twig_Template';
        #$laSettings['cache']            = APPPATH . 'cache'; // Comment if you don't want cache
        $laSettings['auto_reload']      = true;
        $laSettings['strict_variables'] = false;
        $laSettings['optimizations']    = -1;

        $loLoader  = new Twig_Loader_Filesystem(array(APPPATH.'views', APPPATH.'views/pages',APPPATH.'views/layouts',APPPATH.'views/layouts/inc', APPPATH.'views/errors'));
        $this->twig_instance = new Twig_Environment($loLoader, $laSettings);
        // Add css function
        $function = new Twig_Function('css', function ($file) {
            return $this -> root . '/assets/css/'.$file;
        });
        $this -> twig_instance -> addFunction($function);
        // Add image function
        $imageFunction = new Twig_Function('image', function ($file) {
            return $this -> root . '/assets/images/'.$file;
        });
        $this -> twig_instance -> addFunction($imageFunction);
    }

    /**
     * __call
     * @param string $method
     * @param array $args
     * @throws Exception
    */
    public function __call($method, $args)
    {
        if ( ! method_exists($this->twig_instance, $method)) {
            throw new Exception("Undefined method $method attempt in the Twig class.");
        }

        $this->CI->output->append_output( call_user_func_array(array($this->twig_instance, $method), $args) );
    }
}
