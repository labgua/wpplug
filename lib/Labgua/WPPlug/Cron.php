<?php

namespace Labgua\WPPlug;

class Cron implements Registrable {

    private $codename;
    private $filevar;

    private $jobs;

    public function __construct($codename){
        $this->codename = $codename;
        $this->filevar = PathFactory::getFile($codename);
        $this->jobs = [];
    }

    public function defineInterval($name_interval, $num_sec){

        $func = function ($schedules) use ($name_interval, $num_sec){
            $schedules[$name_interval] = [
                'interval' => $num_sec,
                'display' => $name_interval
            ];
            return $schedules;
        };

        add_filter('cron_schedules', [&$func, "__invoke"] );
    }

    public function addJob( $name_job, $recurrence, \Closure $closure, $args = [], $disableOnDeactivation = true){
        $this->jobs[$name_job] = [
            "name" => $name_job,
            "function" => $closure,
            "recurrence" => $recurrence,
            "args" => $args,
            "onDeact" => $disableOnDeactivation,
        ];
    }

    public function removeJob($name_job){
        //todo...
    }


    public function register(){

        array_walk($this->jobs, function($job){

            /// (1) define the action...
            add_action($job["name"], [&$job["function"], "__invoke"]);



            /// (2) onActivation, register the action
            $onActivation = function() use($job){
                ///var_dump("onActivated:" . $j);
                wp_schedule_event(time(), $job["recurrence"], $job["name"], [$job["args"]] );
            };
            register_activation_hook($this->filevar, [&$onActivation, "__invoke"]);



            /// (3) if required, onDeactivation clear it...
            if( $job->jobs["onDeact"] ){
                $onDeactivation = function() use($job){

                    ///wp_clear_scheduled_hook( [&$j["function"], "__invoke" ] );
                    wp_clear_scheduled_hook( $job["name"] );
                };
                register_deactivation_hook($this->filevar,  [&$onDeactivation, "__invoke"]);
            }

        });

    }
}
