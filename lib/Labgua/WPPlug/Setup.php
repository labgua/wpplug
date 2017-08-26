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

    private $dependencies;

	public function __construct($codename, $version)
	{
		$this->codename = $codename;
		$this->version = $version;

        $this->filevar = PathFactory::getFile($codename);

        $this->onInstall = null;
		$this->onActivate = null;
		$this->onDeactivate = null;

		$this->dependencies = [];

	}



    public function addDependency($plugin_name){
        $this->dependencies[] = $plugin_name;
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

	public function __onActivate(){

	    //check dependencies [ installed & actived ]
        $plugin_names = [];
        $plugin_paths = [];
        $all_plugins = get_plugins();
        foreach ($all_plugins as $path => $plugin){
            $plugin_names[] = $plugin["TextDomain"];
            $plugin_paths[ $plugin["TextDomain"] ] = $path;
        }

        //var_dump($plugin_names);
        //var_dump($plugin_paths);

        foreach ($this->dependencies as $dependency){

            //not installed ...
            if( !in_array($dependency, $plugin_names) )
                wp_die("Dependency '<b>$dependency</b>' not installed!", "Missing Dependency");

            //not activated ...
            if( !is_plugin_active( $plugin_paths[$dependency] )  )
                wp_die("Dependency '<b>$dependency</b>' not activated!", "Inactivated Dependency");

        }


        //run onActivate closure..
        if( $this->onActivate != null  ){
            /** @var \Closure $f */
            $f = $this->onActivate;
            $f();
        }

    }


	public function register(){

		//registro install
        add_action('init', [ &$this, "__install" ], 0);

		//registro onActivate
        register_activation_hook($this->filevar, [&$this, '__onActivate']);

		//registro onDeactivate
        if( $this->onDeactivate != null )
		    register_deactivation_hook($this->filevar, [&$this->onDeactivate, '__invoke']);

		//registro uninstall
		////register_uninstall_hook( $this->filevar , array( &$this, 'uninstall' ) );

	}

}