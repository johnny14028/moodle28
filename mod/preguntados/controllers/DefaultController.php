<?php
/**
 * Archivo para definir una sola clase de tipo controlador y sus métodos
 * para desarrollar una acción o un caso de uso, según el analisis del
 * requerimiento.
 */

require_once(__DIR__ . '/../mvc/command/Command.php');
require_once(__DIR__ . '/../model/Model.php');

/**
 * Clase controladora Default para cargar las vistas iniciales.
 * 
 * Esta clase se autogenera con em metodo index por defecto que carga la vista
 * index de la carpeta views/Default/index.php.
 * 
 * @package Preguntados
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class DefaultController extends mvc_command_Command {

    /**
     * atributo para guardar la instancia a la clase modelo, donde se tendrá a
     * metodos con acceso a la BD.
     * @var object 
     */
    private $model;

    /**
     * Metodo inicializador para cargar las instancias de los atributos.
     */
    public function __construct() {
        $this->model = new Model();
    }

    /**
     * Método para cargar la vista por defecto, la vista index en la carpeta
     * con el mismo nombre del controlador dentro de la carpeta views.
     * @global array $USER
     * @return array
     */
    public function index() {
        global $USER;
        $objUsuario = $this->model->getUser();
        return array(
            'usuario' => $USER->firstname,
            'name_plugin' => get_string('pluginname', 'preguntados'),
            'objUsuario' => $objUsuario
        );
    }
    
    public function getInit(){
        global $USER;
        $returnValue = array();
        $returnValue['Validacion'] ='';
        $returnValue['Token'] =md5(uniqid(rand(), true));
        $returnValue['idCurso'] =$_SESSION['course_preguntados'];
        $returnValue['idUsuario'] =$USER->id;
        $returnValue['idGrupo'] =$this->getIdGroup();
        $returnValue['retos'] ='';
        echo json_encode($returnValue);
    }
    
    private function getIdGroup(){
        global $USER, $DB;
        $objGroupMember = $DB->get_record('groups_members', array('userid'=>$USER->id));
        return $objGroupMember->groupid;
    }
}