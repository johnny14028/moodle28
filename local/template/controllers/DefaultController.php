<?php
require_once(__DIR__.'/../mvc/command/Command.php');
require_once(__DIR__.'/../model/Model.php');
class DefaultController extends mvc_command_Command{
    
    private $model;
    
    public function __construct() {
        $this->model = new Model();
    }


    public function index(){
        global $USER;
        $objUsuario = $this->model->getUser();
        return array(
            'usuario'=>$USER->firstname,
            'name_plugin'=>  get_string('pluginname', 'local_template'),
            'objUsuario'=> $objUsuario
            );
    }
    
    public function test(){
        
    }
}