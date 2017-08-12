<?php

namespace Labgua\WPPlug;


class CustomPostTypes implements Registrable
{
	
	private $cpt;

	function __construct(){
		$this->cpt = [];
	}

	public function add($post_type, $args){
		$this->cpt[$post_type] = [
			"post_type" => $post_type,
			"args" => $args
		];
	}

	public function addSimpleType($post_type, $single_name, $plural_name, $public = true, $has_archive = true){
		$this->add($post_type, [
			'labels' => [
				'name' => $plural_name,
				'singular_name' => $single_name
			],
			'public' => $public,
			'has_archive' => $has_archive,
		]);
	}

	public function __register_cpt(){
		foreach ($this->cpt as $post_type => $value) {
			register_post_type( $post_type, $value["args"] ); 
		}
	}

	public function register(){
		add_action('init', [&$this, '__register_cpt']);
	}
}