<?php

namespace Labgua\WPPlug;

class Services implements Registrable
{

	private $closures;
	
	public function __construct(){
		$this->closures = [];
	}

	/**
	 *	$type : "logged" | "anon" | "all"}
	 **/
	public function add($name, $closure, $type = "all"){
		$this->closures[$name] = [
			"name" => $name,
			"closure" => $closure, 
			"type" => $type,
		];
	}

	public function register(){
		foreach ($this->closures as $name => $cc) {

			if( $cc["type"] == "logged" ){
				add_action( 'wp_ajax_' . $name , [&$cc["closure"], "__invoke"] );
			}
			else if( $cc["type"] == "anon" ){
				add_action( 'wp_ajax_nopriv_' . $name , [&$cc["closure"], "__invoke"] );
			}
			else if( $cc["type"] == "all" ){
				add_action( 'wp_ajax_' . $name , [&$cc["closure"], "__invoke"] );
				add_action( 'wp_ajax_nopriv_' . $name , [&$cc["closure"], "__invoke"] );
			}
			
		}
	}
}