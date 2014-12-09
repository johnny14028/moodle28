<?php

include_once 'colors.php';

class pde_local {

    private $path = 'local';
    private $color;

    public function __construct() {
        $this->color = new Colors();
    }

    public function run($name) {
        $this->path = getcwd() . '/' . $this->path . '/';
        //verificamos que tenga los permisos necesarios
        if (is_writable($this->path)) {
            //echo $this->color->getColoredString('se ha creado el paquete de tipo local en la ruta  ' . $this->path . '', 'white', 'green');
            //verificamos que no exista un paquete con el mismo nombre
            if (!$this->existPackage($this->path, $name)) {
                $this->createPackage($this->path . $name, $name);
            } else {
                echo $this->color->getColoredString('El nombre del paquete ya existe en la ruta ' . $this->path . '', 'white', 'red');
            }
        } else {
            echo $this->color->getColoredString('la ruta ' . $this->path . ' No tiene permisos de escritura', 'white', 'red');
        }
    }

    public function createPackage($pathPackage, $name) {
        //creamos el paquete
        //mkdir($pathPackage);
        $folders = $this->listFolder($pathPackage);
        if (is_array($folders) && count($folders) > 0) {
            foreach ($folders as $index => $path) {
                mkdir($path);
            }
        }
        $files = $this->listFile($pathPackage, $name);
        if (is_array($files) && count($files) > 0) {
            foreach ($files as $indice => $file) {
                $myfile = fopen($file, "w") or die("Unable to open file!");
                $txt = $this->getContentFile($pathPackage, $file, $name);
                fwrite($myfile, $txt);
                fclose($myfile);
            }
        }
    }

    private function listFile($pathPackage, $name) {
        $returnValue = array();
        array_push($returnValue, $pathPackage . '/index.php');
        array_push($returnValue, $pathPackage . '/settings.php');
        array_push($returnValue, $pathPackage . '/version.php');
        array_push($returnValue, $pathPackage . '/controllers/DefaultController.php');
        array_push($returnValue, $pathPackage . '/db/install.php');
        array_push($returnValue, $pathPackage . '/db/access.php');
        array_push($returnValue, $pathPackage . '/db/install.xml');
        array_push($returnValue, $pathPackage . '/js/' . $name . '.js');
        array_push($returnValue, $pathPackage . '/js/jquery.js');
        array_push($returnValue, $pathPackage . '/css/' . $name . '.css');
        array_push($returnValue, $pathPackage . '/lang/es/local_' . $name . '.php');
        array_push($returnValue, $pathPackage . '/lang/en/local_' . $name . '.php');
        array_push($returnValue, $pathPackage . '/mvc/base/Registry.php');
        array_push($returnValue, $pathPackage . '/mvc/base/RequestRegistry.php');
        array_push($returnValue, $pathPackage . '/mvc/base/SessionRegistry.php');
        array_push($returnValue, $pathPackage . '/mvc/command/Command.php');
        array_push($returnValue, $pathPackage . '/mvc/command/CommandResolver.php');
        array_push($returnValue, $pathPackage . '/mvc/command/DefaultCommand.php');
        array_push($returnValue, $pathPackage . '/mvc/base/Controller.php');
        array_push($returnValue, $pathPackage . '/mvc/base/Request.php');
        array_push($returnValue, $pathPackage . '/mvc/base/Session.php');
        array_push($returnValue, $pathPackage . '/mvc/require/autoload.php');
        array_push($returnValue, $pathPackage . '/mvc/views/error.php');
        array_push($returnValue, $pathPackage . '/mvc/views/header.php');
        array_push($returnValue, $pathPackage . '/mvc/views/footer.php');
        array_push($returnValue, $pathPackage . '/views/Default/index.php');
        return $returnValue;
    }

    private function listFolder($pathPackage) {
        $returnValue = array();
        array_push($returnValue, $pathPackage);
        array_push($returnValue, $pathPackage . '/controllers');
        array_push($returnValue, $pathPackage . '/db');
        array_push($returnValue, $pathPackage . '/js');
        array_push($returnValue, $pathPackage . '/css');
        array_push($returnValue, $pathPackage . '/pix');
        array_push($returnValue, $pathPackage . '/lang');
        array_push($returnValue, $pathPackage . '/lang/en');
        array_push($returnValue, $pathPackage . '/lang/es');
        array_push($returnValue, $pathPackage . '/mvc');
        array_push($returnValue, $pathPackage . '/mvc/base');
        array_push($returnValue, $pathPackage . '/mvc/command');
        array_push($returnValue, $pathPackage . '/mvc/controller');
        array_push($returnValue, $pathPackage . '/mvc/require');
        array_push($returnValue, $pathPackage . '/mvc/views');
        array_push($returnValue, $pathPackage . '/views');
        array_push($returnValue, $pathPackage . '/views/Default');
        array_push($returnValue, $pathPackage . '/model');
        return $returnValue;
    }

    public function existPackage($path, $package) {
        $returnValue = FALSE;
        $dirs = array_filter(glob($path . '*'), 'is_dir');
        if (in_array($path . $package, $dirs)) {
            $returnValue = TRUE;
        }
        return $returnValue;
    }

    private function getContentFile($pathPackage, $file, $name) {
        $returnValue = '';
        switch ($file) {
            case $pathPackage . '/index.php':
                $returnValue = '<?php
include(\'../../config.php\');
require_once(\'mvc/controller/Controller.php\');

$url = new moodle_url(\'/local/' . $name . '/index.php\');

$PAGE->set_url($url);

require_login();

//$PAGE->set_pagelayout(\'local\');

$context_system = context_system::instance();

//$r = has_capability(\'local/' . $name . ':templatecapability\', $context_system);

$name = get_string(\'pluginname\', \'local_' . $name . '\');
if (isset($_SERVER[\'HTTP_X_REQUESTED_WITH\'])
        AND strtolower($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === \'xmlhttprequest\') {
    mvc_controller_Controller::run();
} else {
    $PAGE->set_context($context_system);

    $PAGE->navbar->add($name);

    $PAGE->set_title($name);

    $PAGE->set_heading($name);

    $PAGE->requires->js(\'/local/' . $name . '/js/jquery.js\');
    $PAGE->requires->js(\'/local/' . $name . '/js/' . $name . '.js\');
    echo $OUTPUT->header();
    mvc_controller_Controller::run();
    echo $OUTPUT->footer();
}';
                break;
            case $pathPackage . '/version.php':
                $returnValue = '<?php

defined(\'MOODLE_INTERNAL\') || die;

$plugin->version = 2010022400;    // The (date) version of this plugin
$plugin->release = \'2.0\';
$plugin->requires = 2010021900; // Requires this Moodle version
$plugin->component = \'local_' . $name . '\';';
                break;
            case $pathPackage . '/controllers/DefaultController.php':
                $returnValue = '<?php
require_once(__DIR__.\'/../mvc/command/Command.php\');
class DefaultController extends mvc_command_Command{
    
    public function index(){
        
    }
}';
                break;
            case $pathPackage . '/lang/en/local_' . $name . '.php';
                $returnValue = '<?php

$string[\'hello\'] = \'Hi {$a}\';
$string[\'' . $name . ':templatecapability\'] = \'Some capabiities\';
$string[\'pluginname\'] = \'Plugin local ' . $name . '\';';
                break;
            case $pathPackage . '/lang/es/local_' . $name . '.php';
                $returnValue = '<?php

$string[\'hello\'] = \'Hola {$a}\';
$string[\'' . $name . ':templatecapability\'] = \'Alguna capacidad\';
$string[\'pluginname\'] = \'Plugin local ' . $name . '\';';
                break;
            case $pathPackage . '/db/access.php':
                $returnValue = '<?php
defined(\'MOODLE_INTERNAL\') || die();

$capabilities = array(
    \'local/' . $name . ':templatecapability\' => array(
        \'captype\' => \'read\',
        \'contextlevel\' => CONTEXT_SYSTEM,
    ),
    \'local/' . $name . ':create\' => array(
        \'riskbitmask\' => RISK_PERSONAL,
        \'captype\' => \'read\',
        \'contextlevel\' => CONTEXT_SYSTEM,
        \'archetypes\' => array(
            \'manager\' => CAP_ALLOW,
            \'teacher\' => CAP_ALLOW
        ),
    ),
    \'local/' . $name . ':control\' => array(
        \'riskbitmask\' => RISK_PERSONAL,
        \'captype\' => \'read\',
        \'contextlevel\' => CONTEXT_SYSTEM,
        \'archetypes\' => array(
            \'manager\' => CAP_ALLOW,
            \'teacher\' => CAP_ALLOW
        ),
    )    
);';
                break;
            case $pathPackage . '/db/install.xml':
                $returnValue = '<?xml version="1.0" encoding="UTF-8"?>
<XMLDB PATH="local/' . $name . '/db" VERSION="2010022400" COMMENT="XMLDB file for Moodle local/' . $name . '"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="../../../lib/xmldb/xmldb.xsd">
    <TABLES>
        <TABLE NAME="local_' . $name . '" COMMENT="Main library table.">
            <FIELDS>
                <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="true" NEXT="userid"/>
                <FIELD NAME="userid" TYPE="int" LENGTH="11" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" PREVIOUS="id" NEXT="name"/>
                <FIELD NAME="name" TYPE="char"  LENGTH="256" NOTNULL="true" SEQUENCE="false"  PREVIOUS="userid" NEXT="fullname"/>
                <FIELD NAME="fullname" TYPE="text" NOTNULL="true" SEQUENCE="false" PREVIOUS="name" NEXT="timecreate"/>
                <FIELD NAME="timecreate" TYPE="int" LENGTH="11" NOTNULL="true" UNSIGNED="true" DEFAULT="1" SEQUENCE="false"  PREVIOUS="fullname"  NEXT="timemodified"/>
                <FIELD NAME="timemodified" TYPE="int" LENGTH="11" NOTNULL="true" UNSIGNED="true" DEFAULT="1" SEQUENCE="false"  PREVIOUS="timecreate"  NEXT="state" /> 
                <FIELD NAME="status" TYPE="int" LENGTH="11" NOTNULL="true" UNSIGNED="true" DEFAULT="1" SEQUENCE="false"  PREVIOUS="timemodified" />                
            </FIELDS>
            <KEYS>
                <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
            </KEYS>
        </TABLE>
    </TABLES>
</XMLDB>';
                break;
            case $pathPackage . '/views/Default/index.php':
                $returnValue = '<h1>Bienvenido al plugin local ' . $name . ' con el action index</h1>';
                break;
            case $pathPackage . '/mvc/base/Registry.php':
                $returnValue = '<?php

abstract class mvc_base_Registry {

    public function __construct() {
        
    }

    protected abstract function get($key);

    protected abstract function set($key, $value);
}';
                break;
            case $pathPackage . '/mvc/base/RequestRegistry.php':
                $returnValue = '<?php

require_once(\'mvc/base/Registry.php\');

class mvc_base_RequestRegistry extends mvc_base_Registry {

    private $value;
    private static $instance;

    public function __construct() {
        
    }

    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance)
            self::$instance = new self;
        $returnValue = self::$instance;
        return $returnValue;
    }

    protected function get($key) {
        return $this->value[$key];
    }

    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    public static function getRequest() {
        $returnValue = NULL;
        $returnValue = self::instance()->get(\'request\');
        return $returnValue;
    }

    public static function setRequest(mvc_controller_Request $objRequest) {
        self::instance()->set(\'request\', $objRequest);
    }

}';
                break;
            case $pathPackage . '/mvc/base/SessionRegistry.php':
                $returnValue = '<?php

require_once \'mvc/base/Registry.php\';

class mvc_base_SessionRegistry extends mvc_base_Registry {

    private $value = array();
    private static $instance = NULL;

    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance) {
            self::$instance = new self();
        }
        $returnValue = self::$instance;
        return $returnValue;
    }

    protected function get($key) {
        return $this->value[$key];
    }

    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    public static function getSession() {
        $returnValue = NULL;
        $returnValue = self::instance()->get(\'' . $name . '\');
        return $returnValue;
    }

    public static function setSession(mvc_controller_Session $objSession) {
        self::instance()->set(\'' . $name . '\', $objSession);
    }

}
';
                break;
            case $pathPackage . '/mvc/command/Command.php':
                $returnValue = '<?php

require_once(__DIR__ . \'/../controller/Request.php\');
require_once(__DIR__ . \'/../controller/Session.php\');

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

        $func = $objRequest->getProperty(\'action\');
        $cmd = $objRequest->getProperty(\'cmd\');

        if (!$cmd)
            $cmd = \'Default\';
        if (!$func)
            $func = \'index\';

        $func = str_replace(array(\'.\', \'/\', \'\\\'), \'\', $func);
        $cmd = str_replace(array(\'.\', \'/\', \'\\\'), \'\', $cmd);
        $this->queryString = "cmd=$cmd&action=$func";
        $this->fileViewDefault = "views/{$cmd}/index.php";
        $fileView2 = "views/{$cmd}/$func.php";
        if ($this->headerView == null || $this->footerView == null) {
            $this->headerView = \'mvc/views/header.php\';
            $this->footerView = \'mvc/views/footer.php\';
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
                    $objRequest->addFeedback(\'Error al anexar la vista del controlador \' . $cmd);
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
';
                break;
            case $pathPackage . '/mvc/command/CommandResolver.php':
                $returnValue = '<?php
require_once(__DIR__.\'/../command/Command.php\');
require_once(__DIR__.\'/../controller/Request.php\');
require_once(__DIR__.\'/../command/DefaultCommand.php\');
class mvc_command_CommandResolver {

    private static $base_cmd = NULL;
    private static $default_cmd = NULL;

    public function __construct() {
        if (!self::$base_cmd) {
            self::$base_cmd = new ReflectionClass(\'mvc_command_Command\');
        }

        if (!self::$default_cmd) {
            self::$default_cmd = new mvc_command_DefaultCommand();
        }

        $filePath = \'controllers/DefaultController.php\';
        $className = \'DefaultController\';

        if (file_exists($filePath)) {
            require_once $filePath;
            if (class_exists($className)) {
                $cmd_class = new ReflectionClass($className);
                if ($cmd_class->isSubclassOf(self::$base_cmd)) {
                    self::$default_cmd = $cmd_class->newInstance();
                }
            } else {
                throw new Exception("La clase del controlador por defecto no existe: $className.");
            }
        } else {
            throw new Exception("La ruta del controlador por defecto no existe: $filePath.");
        }
    }

    public function getCommand(mvc_controller_Request $objRequest) {
        $returnValue = NULL;
        $cmd = $objRequest->getProperty(\'cmd\');
        $func = $objRequest->getProperty(\'action\');

        if (!$cmd)
            $cmd = \'Default\';
        if (!$func)
            $func = \'Index\';
        $cmd = str_replace(array(\'.\', \'/\', \'\\\'), \'\', $cmd);
        $func = str_replace(array(\'.\', \'/\', \'\\\'), \'\', $func);



        $filepath_tmp = "controllers/";
        $prefijoClassName = \'Controller\';

        $filePath = "controllers/{$cmd}{$prefijoClassName}.php";

        $className = $cmd . $prefijoClassName;

        if (file_exists($filePath)) {
            require_once($filePath);
            if (class_exists($className)) {
                $cmd_class = new ReflectionClass($className);
                if ($cmd_class->isSubclassOf(self::$base_cmd)) {
                    $returnValue = $cmd_class->newInstance();
                } else {
                    $objRequest->addFeedback("El command $cmd no es un Command");
                    echo "El command $cmd no es un Command";
                }
            } else {
                $objRequest->addFeedback("La clase \'$className\' del comando $cmd no se encontro");
                echo ("La clase \'$className\' del comando $cmd no se encontro. Incluido desde FilePath: $filepath");
                $returnValue = clone self::$default_cmd;
            }
        } else {
            $objRequest->addFeedback("El comando $cmd no se encontro");
            echo ("El comando $cmd no se encontro");
            $returnValue = clone self::$default_cmd;
        }
        return $returnValue;
    }

}';
                break;
            case $pathPackage . '/mvc/command/DefaultCommand.php':
                $returnValue = '<?php
require_once(__DIR__.\'/../command/Command.php\');
require_once(__DIR__.\'/../controller/Request.php\');
class mvc_command_DefaultCommand extends mvc_command_Command
{
    public function doExecute(mvc_controller_Request $objRequest)
    {
        $objRequest->addFeedback("Bienvenido: Moodle usando MVC");
    }
}';
                break;
            case  $pathPackage . '/mvc/base/Controller.php':
                $returnValue = '<?php

require_once(\'Request.php\');
require_once(__DIR__.\'/../command/CommandResolver.php\');

class mvc_controller_Controller {

    public function __construct() {
        
    }
    /**
     * obtenemos la instancia del mismo objeto
     */
    public static function run() {
        $instance = new mvc_controller_Controller();
        $instance->handleRequest();
    }

    public function handleRequest() {
        $objRequest = new mvc_controller_Request();
        $cmd_r = new mvc_command_CommandResolver();        
        $cmd = $cmd_r->getCommand($objRequest);
        $cmd->execute($objRequest);
    }

}';
                break;
            case $pathPackage . '/mvc/base/Request.php':
                $returnValue = '<?php
require_once(__DIR__.\'/../base/RequestRegistry.php\');

class mvc_controller_Request {

    private $properties = array();
    private $feedback = array();

    public function __construct() {
        $this->init();
        mvc_base_RequestRegistry::setRequest($this);
    }

    public function init() {
        $this->properties = $_REQUEST;
        return;
    }

    public function getProperty($key) {
        if (key_exists($key, $this->properties))
            return $this->properties[$key];
        else
            return false;
    }

    public function setProperty($key, $value) {
        if (key_exists($key, $this->properties))
            $this->properties[$key] = $value;
        else
            return false;
    }

    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    public function getFeedbackString($separator = \'\n\') {
        $returnValue = (string) \'\';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}';
                break;
            case $pathPackage . '/mvc/base/Session.php':
                $returnValue = '<?php

require_once \'mvc/base/SessionRegistry.php\';

class mvc_controller_Session {

    private $properties = array();
    private $feedback = array();

    public function __construct() {
        $this->init();
        mvc_base_SessionRegistry::setSession($this);
    }

    public function init() {
        if (strlen(trim(session_id())) > 0)
            $this->properties = &$_SESSION;
        else
            session_start();
        return;
    }

    public function getProperty($key) {
        if (key_exists($key, $this->properties))
            return $this->properties[$key];
        else
            return false;
    }

    public function setProperty($key, $value) {
        $this->properties[$key] = $value;
    }

    public function deleteProperty($key) {
        if (key_exists($key, $this->properties))
            unset($this->properties[$key]);
    }

    public function getFeedback() {
        $returnValue = array();
        $returnValue = $this->feedback;
        return (array) $returnValue;
    }

    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    public function getFeedbackString($separator = \'\n\') {
        $returnValue = (string) \'\';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}';
                break;
        }

        return $returnValue;
    }

}
