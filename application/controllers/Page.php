<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('twigloader');
	}

	public function about()
	{
		$tab = [
			'Key 1' => 'Value 1',
			'Key 2' => 'Value 2',
			'Key 3' => 'Value 3',
			'Key 4' => 'Value 4',
			'Key 5' => 'Value 5',
			'Key 6' => 'Value 6'
		];
		$this -> twigloader -> setTemplateName('about') -> loadFinal();
	}

	public function view($page = 'home')
	{
        if ( ! file_exists(APPPATH.'views/pages/'.$page.'.twig'))
        {
            // Whoops, we don't have a page for that!
            show_404();
        }
		#var_dump($this -> twigloader);
		$this -> twigloader -> setTemplateName('home') -> setTitle('HOME') -> loadFinal();
	}
}
