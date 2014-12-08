<?php

require_once(__DIR__ . '/../mvc/command/Command.php');

class TestController extends mvc_command_Command {

    public function index() {
        global $CFG;
        return array(
            'saludo' => get_string('pluginname', 'local_template')
        );
    }

    public function ajax() {
        $this->disabledFooterCore();
        $this->disabledHeaderCore();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode(array('html' => 'ajax2'));
        }
    }

}
