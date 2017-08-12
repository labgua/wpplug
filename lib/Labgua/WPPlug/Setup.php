<?php

namespace Labgua\WPPlug;


class Setup implements Registrable
{
	
	private $codename;
	private $version;
	private $filevar;

	private $onInstall;
	private $onActivate;
	private $onDeactivate;
	///private $onUninstall;

	public function __construct($codename, $version)
	{
		$this->codename = $codename;
		$this->version = $version;

        $this->filevar = PathFactory::getFile($codename);

        $this->onInstall = null;
		$this->onActivate = null;
		$this->onDeactivate = null;

	}




	public function setOnInstall($closure){
		$this->onInstall = $closure;
	}

	public function setOnActivate($closure){
		$this->onActivate = $closure;
	}

	public function setOnDeactivate($closure){
		$this->onDeactivate = $closure;
	}

	/*
	public function setOnUninstall($closure){
		$this->onUninstall = $closure;
	}*/




	public function __install(){

		$db_version = get_option($this->codename, 0);

		if( empty( $db_version ) ){

		    if( $this->onInstall != null  ){
                /** @var \Closure $f */
                $f = $this->onInstall;
                $f();
            }

			update_option($this->codename, $this->version);
		}

	}

	/*
	public function uninstall(){
		$f = $this->onUninstall;
		$f();

		delete_option($this->codename);
	}
	*/


	public function register(){

		//registro install
        add_action('init', [ &$this, "__install" ], 0);

		//registro onActivate
        if( $this->onActivate != null )
		    register_activation_hook($this->filevar, [&$this->onActivate, '__invoke']);

		//registro onDeactivate
        if( $this->onDeactivate != null )
		    register_deactivation_hook($this->filevar, [&$this->onDeactivate, '__invoke']);

		//registro uninstall
		////register_uninstall_hook( $this->filevar , array( &$this, 'uninstall' ) );

	}

}