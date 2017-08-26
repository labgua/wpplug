<?php

namespace Labgua\WPPlug;

class AdminController implements Registrable
{

    private $codename;
    private $name;

    private $filevar;

    //array associativo
    private $slugFunctions;
    private $styles;
    private $scripts;


    public function __construct($codename)
    {
        $this->codename = $codename;
        $this->name = $codename . "_admin";

        $this->filevar = PathFactory::getFile($codename);

        $this->slugFunctions = [];
        $this->styles[] = "asset/css/admin.css";
        $this->scripts[] = "asset/js/admin.js";
    }


    public function addPage($slug, $label, $parent = null)
    {
        $this->slugFunctions[$slug] = [
            "slug" => $slug,
            "label" => $label,
            "parent" => $parent,
            //"function" => $function
        ];//$function;
    }

    public function enqueueStyle($filePath)
    {
        $this->styles[] = $filePath;
    }

    public function enqueueScript($filePath)
    {
        $this->scripts[] = $filePath;
    }


    public function __callback_admin_menu()
    {

        array_walk($this->slugFunctions, function ($context) {

            ///var_dump($context);

            $newfunc = function () use ($context) {
                $context['urlPost'] = "admin.php?page=" . $context["slug"];
                ///var_dump($context);
                $context["codename"] = $this->codename;
                require_once plugin_dir_path($this->filevar) . "controller/" . $context["slug"] . ".php";
            };


            if ($context["parent"] == null) {
                add_menu_page(
                    $context["label"],
                    $context["label"],
                    "manage_options",
                    $context["slug"],
                    [&$newfunc, "__invoke"]/////[&$runner, "run"]
                );
            } else {
                add_submenu_page(
                    $context["parent"],
                    $context["label"],
                    $context["label"],
                    "manage_options",
                    $context["slug"],
                    [&$newfunc, "__invoke"]/////[&$runner, "run"]
                );
            }

        });

    }


    public function __callback_admin_style()
    {

        if (is_admin()) {
            foreach ($this->styles as $s) {

                wp_enqueue_style(
                    $this->name,
                    plugins_url($s, $this->filevar)
                );

            }
        }

    }

    public function __callback_admin_script()
    {

        if (is_admin()) {
            foreach ($this->scripts as $s) {

                wp_enqueue_script(
                    $this->name,
                    plugins_url($s, $this->filevar)
                );

            }
        }

    }


    public function register()
    {
        ////registra pages
        add_action('admin_menu', array(&$this, '__callback_admin_menu'));

        ////registra CSS
        add_action('init', array(&$this, '__callback_admin_style'));

        ////registra JS
        add_action('init', array(&$this, '__callback_admin_script'));
    }

}