<?php

namespace Labgua\WPPlug;


class CustomPostTypes implements Registrable
{

    private $codename;

    private $cpt;
    private $cmb;

    function __construct($codename)
    {

        $this->codename = $codename;

        $this->cpt = [];
        $this->cmb = [];
    }

    public function add($post_type, $args)
    {
        $this->cpt[$post_type] = [
            "post_type" => $post_type,
            "args" => $args
        ];
    }

    public function addSimpleType($post_type, $single_name, $plural_name, $public = true, $has_archive = true)
    {
        $this->add($post_type, [
            'labels' => [
                'name' => $plural_name,
                'singular_name' => $single_name
            ],
            'public' => $public,
            'has_archive' => $has_archive,
            'supports' => false
        ]);
    }


    public function addSimpleTypeWithCMB($post_type, $single_name, $plural_name, $fields)
    {
        $this->add($post_type, [
            'labels' => [
                'name' => $plural_name,
                'singular_name' => $single_name
            ],
            'public' => true,
            'has_archive' => true,
        ]);


        $codename = $this->codename;
        $cmb_id = $codename . '_' . $post_type;
        $newCMB["args"] = [
            "id" => $cmb_id,
            "title" => $single_name,
            "object_types" => $post_type,
        ];

        foreach ($fields as $field) {
            $newCMB["fields"][] = [
                "name" => $field["name"],
                "desc" => $field["desc"],
                "id" => $cmb_id . '_' . $field["id"], //// usato in wp_postmeta come meta_key !!!
                "type" => $field["type"],
            ];
        }


        $this->cmb[$cmb_id] = $newCMB;

    }

    /*
    public function newSimpleMetabox($post_type, $id, $title, $context = 'normal', $priority = 'high'){
        $args = array(
            'id'            => $id,
            'title'         => $title,
            'object_types'  => $post_type, // Post type
            'context'       => $context,
            'priority'      => $priority,
            'show_names'    => true, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        );

        $this->cmb[] = $args;

        //return $cmb;
    }
    */

    public function addMetabox($args, $fields)
    {

        $id = $args["id"];
        $this->cmb[$id] = [
            "args" => $args,
            "fields" => $fields
        ];

    }


    public function __register_cpt()
    {
        foreach ($this->cpt as $post_type => $value) {
            register_post_type($post_type, $value["args"]);
        }
    }

    public function __register_cmb2()
    {

        array_walk($this->cmb, function ($cmb) {

            $cmb_args = $cmb["args"];
            $cmb_fields = $cmb["fields"];

            $cmb = new_cmb2_box($cmb_args);

            foreach ($cmb_fields as $field) {
                $cmb->add_field($field);
            }

        });

        /// see https://nooshu.github.io/blog/2010/10/20/page-not-found-with-custom-post-types/
        flush_rewrite_rules(false);

    }

    public function register()
    {
        add_action('init', [&$this, '__register_cpt']);
        add_action('cmb2_admin_init', [&$this, '__register_cmb2']);
    }

}