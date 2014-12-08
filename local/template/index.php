<?php

//include_once 'mvc/require/autoload.php';
include('../../config.php');
require_once('mvc/controller/Controller.php');

$url = new moodle_url('/local/template/index.php');

$PAGE->set_url($url);

require_login();

//$PAGE->set_pagelayout('local');

$context_system = context_system::instance();

//$r = has_capability('local/template:templatecapability', $context_system);

$name = get_string('pluginname', 'local_template');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    mvc_controller_Controller::run();
} else {
    $PAGE->set_context($context_system);

    $PAGE->navbar->add($name);

    $PAGE->set_title($name);

    $PAGE->set_heading($name);

    $PAGE->requires->js('/local/template/js/jquery.js');
    $PAGE->requires->js('/local/template/js/template.js');
    echo $OUTPUT->header();
    mvc_controller_Controller::run();
    echo $OUTPUT->footer();
}
