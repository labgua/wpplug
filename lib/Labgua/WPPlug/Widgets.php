<?php

namespace Labgua\WPPlug;

class Widgets implements Registrable {

    private $codename;

    private $pathWidgets;
    private $classNames;

    public function __construct($codename, $pathWidget = "/widgets" ){
        $this->classNames = [];
        $this->codename = $codename;
        $this->pathWidgets = PathFactory::getPath($codename) . $pathWidget;
    }

    public function add($class_name){
        $this->classNames[] = $class_name;
    }

    public function __callback_register_widget(){
        foreach ($this->classNames as $nc) {
            require_once $this->pathWidgets . '/' . $nc . ".php";
            register_widget($nc);
        }
    }

    public function register(){
        foreach ($this->classNames as $nc) {
            add_action( 'widgets_init', [&$this, "__callback_register_widget"]);
        }
    }

}