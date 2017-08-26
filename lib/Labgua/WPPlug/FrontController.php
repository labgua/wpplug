<?php

namespace Labgua\WPPlug;

class FrontController implements Registrable
{

    private $codename;
    private $name;
    private $filevar;

    private $shortFunctions;
    private $styles;
    private $scripts;

    public function __construct($codename)
    {

        $this->codename = $codename;
        $this->name = $codename . "_front";

        $this->filevar = PathFactory::getFile($codename);

        $this->styles[] = "asset/css/front.css";
        $this->scripts[] = "asset/js/front.js";

        $this->shortFunctions = [];
    }

    public function addShortcode($shortcode, $pairs = [], $preheaderController = null)
    {


        //// preheder
        if ($preheaderController != null) {
            $preFunc = function () use ($shortcode, $preheaderController) {
                if (!is_admin()) {
                    global $post, $current_user;
                    if (!empty($post->post_content) && strpos($post->post_content, '[' . $shortcode . ']') !== false) {
                        $preheaderController($post, $current_user);
                    }
                }
            };
        }


        //// controller
        ///  $atts -> attributes passed from shortcode in use
        ///  $content -> the content used in the shortcode
        ///
        /// define:
        ///  $context[key] : value (from defined  where used)
        ///  $context["shortcode_content"] : the content used in the shortcode
        $newfunc = function ($atts, $content) use ($shortcode, $pairs) {

            $data_shortcode = shortcode_atts($pairs, $atts);
            $content = do_shortcode($content);


            $context = $data_shortcode;
            $context["codename"] = $this->codename;
            $context["shortcode_content"] = $content;

            ///var_dump($context);

            require_once plugin_dir_path($this->filevar) . "controller/" . $shortcode . ".php";
        };


        $this->shortFunctions[$shortcode] = [
            "shortcode" => $shortcode,
            "function" => $newfunc,
            "preheader" => $preFunc,
        ];

    }

    public function addRoute()
    {
        //TODO creare un metodo che crea una pagina con Rewrite API
    }


    public function __callback_front_style()
    {

        if (!is_admin()) {
            foreach ($this->styles as $s) {

                wp_enqueue_style(
                    $this->name,
                    plugins_url($s, $this->filevar)
                );

            }
        }

    }

    public function __callback_front_script()
    {

        if (!is_admin()) {
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

        /// registra shortcode + preheader
        foreach ($this->shortFunctions as $shortcode => $value) {
            add_shortcode($shortcode, [&$value["function"], "__invoke"]);

            if ($value["preheader"] != null) {
                add_action('wp', [&$value["preheader"], "__invoke"], 1);
            }
        }


        ///registra CSS
        add_action('init', array(&$this, '__callback_front_style'));

        //registra JS
        add_action('init', array(&$this, '__callback_front_script'));

    }

}