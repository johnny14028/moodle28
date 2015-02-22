<?php

/**
 * Archivo  para registrar los metodos de ruteo.
 */


require_once(__DIR__ . '/../controller/Request.php');
require_once(__DIR__ . '/../controller/Session.php');

/**
 * Clase para abstraer los metodos que utilizaremos para rutear las vistas.
 * 
 * Con esta clase inicializaremos y ejecutaremos los métodos que rutearan
 * las vistas por cada controlador y propiedades con vistas.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
abstract class mvc_command_Command {

    /**
     *
     * @var object 
     */
    public $request;

    /**
     *
     * @var object 
     */
    public $session;

    /**
     *
     * @var string 
     */
    protected $fileView;

    /**
     *
     * @var string 
     */
    protected $headerView;

    /**
     *
     * @var string 
     */
    protected $footerView;

    /**
     *
     * @var boolean 
     */
    private $footerCore = true;

    /**
     *
     * @var boolean 
     */
    private $headerCore = true;

    /**
     * Método para inicializar valores de este objeto.
     */
    public function __construct() {
        
    }

    /**
     * Método para leer la session global.
     * @return type
     */
    public function getSession() {
        $this->session = new mvc_controller_Session();
        return $this->session;
    }

    /**
     * Método para obtener el request que se envía.
     * @return object
     */
    public function getRequest() {
        $this->request = new mvc_controller_Request();
        return $this->request;
    }

    /**
     * Método para cargar las vistas que se puede leer en el request.
     * @global array $CFG
     * @param mvc_controller_Request $objRequest
     */
    public function execute(mvc_controller_Request $objRequest) {
        global $CFG;
        $this->session = new mvc_controller_Session();

        $this->request = $objRequest;

        $func = $objRequest->getProperty('action');
        $cmd = $objRequest->getProperty('cmd');

        if (!$cmd)
            $cmd = 'Default';
        if (!$func)
            $func = 'index';

        $func = str_replace(array('.', '/', '\\'), '', $func);
        $cmd = str_replace(array('.', '/', '\\'), '', $cmd);
        $this->queryString = "cmd=$cmd&action=$func";
        $this->fileViewDefault = "views/{$cmd}/index.php";
        $fileView2 = "views/{$cmd}/$func.php";
        if ($this->headerView == null || $this->footerView == null) {
            $this->headerView = 'mvc/views/header.php';
            $this->footerView = 'mvc/views/footer.php';
        }

        $this->doExecute($objRequest);

        if (method_exists($this, $func)) {
            $retorno = $this->$func();
            if (is_array($retorno)) {
                extract($retorno);
            }
        }
        if (file_exists($fileView2)) {
            if ($this->headerCore) {
                include($this->headerView);
            }
            include($fileView2);
            if ($this->footerCore) {
                include($this->footerView);
            }
        } else {
            if (file_exists($this->fileView)) {
                if ($this->headerCore)
                    include($this->headerView);
                include($this->fileView);
                if ($this->footerCore)
                    include($this->footerView);
            }else {
                if (file_exists($this->fileViewDefault)) {
                    if ($this->headerCore)
                        include($this->headerView);
                    include($this->fileViewDefault);
                    if ($this->footerCore)
                        include($this->footerView);
                }else {
                    $objRequest->addFeedback('Error al anexar la vista del controlador ' . $cmd);
                    include("mvc/views/error.php");
                }
            }
        }
    }

    /**
     * Método para ejecutar un request.
     * @param mvc_controller_Request $objRequest
     */
    public function doExecute(mvc_controller_Request $objRequest) {
        
    }

    /**
     * Método para desabilitar la cabecera de una vista.
     */
    public function disabledHeaderCore() {
        $this->headerCore = false;
    }

    /**
     * Metodo para desabilitar el pie de pagina de una vista.
     */
    public function disabledFooterCore() {
        $this->footerCore = false;
    }

}