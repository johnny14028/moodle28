<?php

require_once(__DIR__ . '/../controller/Request.php');
require_once(__DIR__ . '/../controller/Session.php');

abstract class mvc_command_Command {

    public $request;
    public $session;
    protected $fileView;
    protected $headerView;
    protected $footerView;
    private $footerCore = true;
    private $headerCore = true;

    public function __construct() {
        
    }

    public function getSession() {
        $this->session = new mvc_controller_Session();
        return $this->session;
    }

    public function getRequest() {
        $this->request = new mvc_controller_Request();
        return $this->request;
    }

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
                if(is_array($retorno)){
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

    public function doExecute(mvc_controller_Request $objRequest) {
        
    }

    public function disabledHeaderCore() {
        $this->headerCore = false;
    }

    public function disabledFooterCore() {
        $this->footerCore = false;
    }
}
