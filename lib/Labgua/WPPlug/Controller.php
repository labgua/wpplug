<?php

namespace Labgua\WPPlug;

use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * Class Controller
 *
 * This class is a wrapper of TWIG template engine ready to use.
 */
class Controller
{
    private $codename;
    private $loader;
    private $twig;

    private $data;

    public function __construct($context, $pathView = "/view", $cache = false)
    {

        $this->loader = new Twig_Loader_Filesystem(PathFactory::getPath($context["codename"]) . $pathView);

        $twigOptions[] = array();
        if (!$cache) $twigOptions['cache'] = false;
        else $twigOptions['cache'] = PathFactory::getPath() . '/cache';

        $this->twig = new Twig_Environment($this->loader, $twigOptions);


        /*
		$this->data = [];
		$this->data["slug"] = $context["slug"];
		$this->data["urlPost"] = $context["urlPost"];
        */
        $this->codename = $context["codename"];
        $this->data = $context;
    }

    public function getTwig()
    {
        return $this->twig;
    }


    public function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getData()
    {
        return $this->data;
    }

    public function addFlash($status, $message)
    {
        $this->data["flashes"][] = [
            "status" => $status,
            "message" => $message
        ];
    }

    public function render($file)
    {
        echo $this->twig->render($file, $this->data);
    }
}