<?php
/**
*	© newQuery - 2017
*	@author Thomas Wilmshorst
*	@link: http://blyat.eu/
*	@version 1.0.0
*	----------------------------------------------------------------------------------------------
*	Description:
	TLC - Twig Loading Class
*	----------------------------------------------------------------------------------------------
*/


class TwigLoader
{
	/*
	*	Array: Made of the options that if not NULL will be rendered
	*/
	public $options = [];

	/*
	*	String: Template's name
	*	Without the prefix and extension
	*/
	public $tpl;

	/*
	*	String: The name of a class::method to be call during the instanciation of the class
	*/
	public $action;


	# TWIG properties

	/*
	*	Array: Made of the options that if not NULL will be rendered
	*/
	public $renders;

	/*
	*	Array: Twig template
	*/
	public $template;

	/**
	 * Code Igniter object
	 */
	public $CI;

	/**
	 * Array containing the paths of the templates directories
	 */
	const TPL_PATHS = [
		APPPATH.'views',
		APPPATH.'views/pages',
		APPPATH.'views/layouts',
		APPPATH.'views/layouts/inc',
		APPPATH.'views/errors'
	];

	/**
	*	@param String: The template's name
	*	@param Array: Made of the options you want to be in your TemplatingArray
	*	@param String: The name of a class::method() you want to be executed right after instanciating the class
	*/
	public function __construct(String $tpl = NULL, Array $renders = NULL, String $action = NULL)
	{
		$this -> CI = & get_instance();
		self::loadTwig();
		if($tpl != NULL) self::loadTemplate($tpl);
		if($renders != NULL) self::loadRenders($renders);
		if($action != NULL) self::$action();
	}

	/**
	*	@param String: The title you want to be displayed between the <title></title> tags
	*	@return $this
	*/
	public function setTitle(String $title)
	{
		$this -> options = array_merge($this -> options, array('title' => $title));
		return $this;
	}

	/**
	*	@param Array: Made of the options you want to be using in your template
	*	@return $this
	*/
	public function setRenders(Array $renders)
	{
		if(is_array($renders) && $renders != "") $this -> options = array_merge($this -> options, $renders);
		return $this;
	}

	/**
	*	@param String: Template's name - Will be displayed in you Twig templating array and usable
	*	@return $this
	*/
	public function setTemplateName(String $name)
	{
		if(is_string($name) && $name != "") $this -> tpl = strtolower(trim($name));
		$this -> options = array_merge($this -> options, array('tplName' => $name));
		return $this;
	}

	/**
	*	Loading Twig Environments, Templates directories, extensions
	*	@return $this
	*/
	public function loadTwig()
	{
		$this -> root = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        //HOST
        $this -> root .= '://' . $_SERVER['HTTP_HOST'];

		// The directories where it contains .twig
		$loader = new Twig_Loader_Filesystem(self::TPL_PATHS);
		$this -> twig = new Twig_Environment($loader, array(
			'debug' => true,
		    #'cache' => '/path/to/compilation_cache',
		));
		$this -> twig -> addExtension(new Twig_Extension_Debug());

		$function = new Twig_Function('css', function ($file) {
            return $this -> root . '/assets/css/'.$file;
        });
        $this -> twig -> addFunction($function);

		return $this;
	}

	/**
	*	@param String: The template's name to load
	*	@return $this
	*/
	public function loadTemplate(String $tpl = NULL)
	{
		$template = strtolower($tpl);
		if(isset($this -> tpl) && $this -> tpl != '' && $tpl == NULL)
		{
			if(self::checkIfTemplateExist($tpl))
			{
				$this -> template = $this -> twig -> load($this -> tpl.'.twig');
				self::setTemplateName($this -> tpl);
			}
			else throw new Exception("Template does not exist - view Exception in Twig.class.php, method loadTemplate");
		}
		elseif($tpl != NULL)
		{
			if(self::checkIfTemplateExist($tpl))
			{
				$this -> template = $this -> twig -> load($template.'.twig');
				self::setTemplateName($template);
			}
			else throw new Exception("Template does not exist - view Exception in Twig.class.php, method loadTemplate");
		}

		return $this;
	}

	/**
	*	@param Array: made of the options you want to be able to use in the TWIG template (Kinda the same as setRenders, but it also loads them)
	*	@return $this
	*/
	public function loadRenders(Array $options = NULL)
	{
		if($options != NULL && is_array($options))
		{
			$this -> options = array_merge($this -> options, $options);
			$this -> renders = $this -> options;
		}
		elseif($options == NULL && isset($this -> options) && $this -> options != NULL) $this -> renders = $this -> options;

		if($this -> renders !== NULL) $this -> renderedTemplate = true;

		return $this;
	}

	/**
	*	This method should always be the last one to be called
	*	It is meant to load and echo out the template
	*/
	public function loadFinal()
	{
		// In case you didn't use loadTemplate but setTemplateName during routing
		if(!isset($this -> template) && isset($this -> tpl)) self::loadTemplate($this -> tpl);

		// Load les renders si ils ont été ajouté depuis setRenders et non pas par le constructeur
		if(isset($this -> options) && is_array($this -> options) && $this -> options != NULL) self::loadRenders();

		// Rendering it
		if(isset($this -> renderedTemplate)) echo $this -> template -> render($this -> renders);
		else echo $this -> template -> render();
		exit;
	}

	/**
	*	@param String: Template's name - without the prefix & extension
	*	@return Boolean
	*/
	private function checkIfTemplateExist($tpl)
	{
		$paths = self::TPL_PATHS;
		foreach ($paths as $path)
		{
			if(file_exists($path.'/'.$tpl.'.twig'))
			{
				return true;
				break;
			}
		}
		return false;
	}
}
