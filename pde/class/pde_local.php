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
                echo $this->color->getColoredString('El paquete fue creado en forma satisfactoria: ' . $this->path . '', 'white', 'green');
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
        array_push($returnValue, $pathPackage . '/phpdoc.xml');
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
        array_push($returnValue, $pathPackage . '/mvc/controller/Controller.php');
        array_push($returnValue, $pathPackage . '/mvc/controller/Request.php');
        array_push($returnValue, $pathPackage . '/mvc/controller/Session.php');
        array_push($returnValue, $pathPackage . '/mvc/require/autoload.php');
        array_push($returnValue, $pathPackage . '/mvc/views/error.php');
        array_push($returnValue, $pathPackage . '/mvc/views/header.php');
        array_push($returnValue, $pathPackage . '/mvc/views/footer.php');
        array_push($returnValue, $pathPackage . '/views/Default/index.php');
        array_push($returnValue, $pathPackage . '/model/Model.php');
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
    /**
     * 
     * @param type $pathPackage ruta del plugin
     * @param type $file ruta completa del archivo
     * @param type $name nombre del plugin
     * @param type $default si es controlador o vista
     * @param type $nameC nombre del controlador o vista
     * @return string
     */
    private function getContentFile($pathPackage, $file, $name, $default='', $nameC='') {
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
            case $pathPackage . '/phpdoc.xml':
                $returnValue = '<?xml version="1.0" encoding="UTF-8"?>
<phpdoc>
    <title>Plugin ' . ucfirst(strtolower($name)) . '</title>
    <parser>
        <default-package-name>' . ucfirst(strtolower($name)) . '</default-package-name>
        <target>../../output/local/'.$name.'</target>
        <extensions>
            <extension>php</extension>
        </extensions>        
    </parser>
    <transformer>
        <target>../../output/local/'.$name.'</target>
    </transformer>
    <files>
        <directory>.</directory>
    </files>
</phpdoc>';
                break;
            case $pathPackage . '/controllers/DefaultController.php':
                $returnValue = '<?php
/**
 * Archivo para definir una sola clase de tipo controlador y sus métodos
 * para desarrollar una acción o un caso de uso, según el analisis del
 * requerimiento.
 */

namespace ' . ucfirst(strtolower($name)) . '\Controller;
require_once(__DIR__ . \'/../mvc/command/Command.php\');
require_once(__DIR__ . \'/../model/Model.php\');

/**
 * Clase controladora Default para cargar las vistas iniciales.
 * 
 * Esta clase se autogenera con em metodo index por defecto que carga la vista
 * index de la carpeta views/Default/index.php.
 * 
 * @package ' . ucfirst(strtolower($name)) . '
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
            \'usuario\' => $USER->firstname,
            \'name_plugin\' => get_string(\'pluginname\', \'' . $name . '\'),
            \'objUsuario\' => $objUsuario
        );
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
                <FIELD NAME="timemodified" TYPE="int" LENGTH="11" NOTNULL="true" UNSIGNED="true" DEFAULT="1" SEQUENCE="false"  PREVIOUS="timecreate"  NEXT="status" /> 
                <FIELD NAME="status" TYPE="int" LENGTH="11" NOTNULL="true" UNSIGNED="true" DEFAULT="1" SEQUENCE="false"  PREVIOUS="timemodified" />                
            </FIELDS>
            <KEYS>
                <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
            </KEYS>
        </TABLE>
    </TABLES>
</XMLDB>';
                break;
            case $pathPackage . '/db/install.php':
                $returnValue = '<?php

function xmldb_local_'.$name.'_install(){
}';
                break;
            case $pathPackage . '/views/Default/index.php':
                $returnValue = '<h1>Bievenido <?php echo $usuario ?> al <?php echo $name_plugin ?></h1>

<?php
print_object($objUsuario);
?> ';
                break;
            case $pathPackage . '/mvc/base/Registry.php':
                $returnValue = '<?php

namespace Mvc\Base;

/**
 * Clase para abstraer los metodos get y set
 */
abstract class mvc_base_Registry {

    /**
     * iniciamos los valores de entrada
     */
    public function __construct() {
        
    }

    /**
     * @param string $key nombre de la variable
     */
    protected abstract function get($key);

    /**
     * @param string $key indice del valor
     * @param string $value valor de la variable $key
     */
    protected abstract function set($key, $value);
}';
                break;
            case $pathPackage . '/mvc/base/RequestRegistry.php':
                $returnValue = '<?php
/**
 * Archivo para crear la clase de registro de request para obtener y crear
 * objetos de tipo request y trabajar con sus valores.
 */

namespace Mvc\Base;

require_once(\'mvc/base/Registry.php\');

/**
 * Clase de registro de request
 * 
 * Clase para registrar movimiento y cambios de los request por session.
 * 
 * @package ' . ucfirst(strtolower($name)) . '
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_base_RequestRegistry extends mvc_base_Registry {

    /**
     *
     * @var array 
     */
    private $value;

    /**
     *
     * @var object 
     */
    private static $instance;

    /**
     * Método para inicializar los valores de este objeto
     */
    public function __construct() {
        
    }

    /**
     * Método para obtener su propia instancia o crearla si es que no existe.
     * @return object
     */
    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance)
            self::$instance = new self;
        $returnValue = self::$instance;
        return $returnValue;
    }

    /**
     * Método para retornar el valor de un indice
     * @param string $key
     * @return object
     */
    protected function get($key) {
        return $this->value[$key];
    }

    /**
     * Método para setear el valor de un indice o crear
     * @param string $key
     * @param object $value
     */
    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    /**
     * Método para retornar el objeto request y sus valores cargados hasta el
     * momento.
     * @return object
     */
    public static function getRequest() {
        $returnValue = NULL;
        $returnValue = self::instance()->get(\'request\');
        return $returnValue;
    }

    /**
     * Método para cargar el nuevo request con los valores que se desea
     * @param mvc_controller_Request $objRequest
     */
    public static function setRequest(mvc_controller_Request $objRequest) {
        self::instance()->set(\'request\', $objRequest);
    }

}
';
                break;
            case $pathPackage . '/mvc/base/SessionRegistry.php':
                $returnValue = '<?php

/**
 * Archivo para registrar las sessiones
 */

namespace Mvc\Base;

require_once \'mvc/base/Registry.php\';

/**
 * Clase de registros para las sessiones
 * 
 * Clase que se registra, crea, edita los valores de la session de su propia
 * instancia.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_base_SessionRegistry extends mvc_base_Registry {

    /**
     *
     * @var array 
     */
    private $value = array();

    /**
     *  instancia propia de esta clase
     * @var object 
     */
    private static $instance = NULL;

    /**
     * Método para obtener su propia instancia o crearla si es que no existe.
     * @return object
     */
    public static function instance() {
        $returnValue = NULL;
        if (!self::$instance) {
            self::$instance = new self();
        }
        $returnValue = self::$instance;
        return $returnValue;
    }

    /**
     * Método para obtener un valor de session por su indice
     * @param string $key
     * @return object
     */
    protected function get($key) {
        return $this->value[$key];
    }

    /**
     * Método para setear un valor a un indice de una session
     * @param string $key
     * @param object $value
     */
    protected function set($key, $value) {
        $this->value[$key] = $value;
    }

    /**
     * método para obtener el valor de una session
     * @return type
     */
    public static function getSession() {
        $returnValue = NULL;
        $returnValue = self::instance()->get(\''.$name.'\');
        return $returnValue;
    }

    /**
     * Metodo para crear una variable en la session
     * @param mvc_controller_Session $objSession
     */
    public static function setSession(mvc_controller_Session $objSession) {
        self::instance()->set(\''.$name.'\', $objSession);
    }

}';
                break;
            case $pathPackage . '/mvc/command/Command.php':
                $returnValue = '<?php

/**
 * Archivo  para registrar los metodos de ruteo.
 */

namespace Mvc\Command;

require_once(__DIR__ . \'/../controller/Request.php\');
require_once(__DIR__ . \'/../controller/Session.php\');

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
                    $objRequest->addFeedback(\'Error al anexar la vista del controlador \' . $cmd);
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

}';
                break;
            case $pathPackage . '/mvc/command/CommandResolver.php':
                $returnValue = '<?php

/**
 * Archivo que tiene una clase en la que carga los datos iniciales del request 
 * y resolver casos por defecto.
 */

namespace Mvc\Command;

require_once(__DIR__ . \'/../command/Command.php\');
require_once(__DIR__ . \'/../controller/Request.php\');
require_once(__DIR__ . \'/../command/DefaultCommand.php\');

/**
 * Clase para resolver cada request que recibe el plugin.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_command_CommandResolver {

    /**
     *
     * @var string 
     */
    private static $base_cmd = NULL;

    /**
     * atributo para cargar el valor de cmd
     * @var string 
     */
    private static $default_cmd = NULL;

    /**
     * Método para cargar la vista por defecto.
     * @throws Exception
     */
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

    /**
     * Método para cargar las vistar por cada controlador.
     * @param mvc_controller_Request $objRequest
     * @return object
     */
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

/**
 * Archivo para cargar los request con valores por defecto.
 */

namespace Mvc\Command;

require_once(__DIR__ . \'/../command/Command.php\');
require_once(__DIR__ . \'/../controller/Request.php\');

/**
 * 
 * 
 * Clase para cargar el primer request y tener los valoresde los objetos
 * request y session con valores iniciales para cargar las vistas por defecto.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_command_DefaultCommand extends mvc_command_Command {

    /**
     * Método para cargar el request y obtener las vistas.
     * @param mvc_controller_Request $objRequest
     */
    public function doExecute(mvc_controller_Request $objRequest) {
        $objRequest->addFeedback("Bienvenido: Moodle usando MVC");
    }

}';
                break;
            case  $pathPackage . '/mvc/controller/Controller.php':
                $returnValue = '<?php

/**
 * Archivo para crear la clase del controlador general.
 */

namespace Mvc\Controller;

require_once(\'Request.php\');
require_once(__DIR__ . \'/../command/CommandResolver.php\');

/**
 * Controlador general
 * 
 * Clase de tipo controlador que recibe los request y este ejecuta metodos para
 * resolver el requerimiento y cargar las vistas necesarias.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_controller_Controller {

    /**
     * iniciando los valores del objeto controlador.
     */
    public function __construct() {
        
    }

    /**
     * obtenemos la instancia del mismo objeto
     */
    public static function run() {
        $instance = new mvc_controller_Controller();
        $instance->handleRequest();
    }

    /**
     * Método para manejar los request y ejecutar los metodos que le permiten
     * cargar las vistas.
     */
    public function handleRequest() {
        $objRequest = new mvc_controller_Request();
        $cmd_r = new mvc_command_CommandResolver();
        $cmd = $cmd_r->getCommand($objRequest);
        $cmd->execute($objRequest);
    }

}';
                break;
            case $pathPackage . '/mvc/controller/Request.php':
                $returnValue = '<?php

/**
 * Archivo del controlador de request;
 */

namespace Mvc\Controller;

require_once(__DIR__ . \'/../base/RequestRegistry.php\');

/**
 * Clase controlador de request.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_controller_Request {

    /**
     *
     * @var array 
     */
    private $properties = array();

    /**
     *
     * @var array 
     */
    private $feedback = array();

    /**
     * Método para inciar el manejador de request de este controlador.
     */
    public function __construct() {
        $this->init();
        mvc_base_RequestRegistry::setRequest($this);
    }

    /**
     * Mátodo para inicializar capturando el request.
     * @return void
     */
    public function init() {
        $this->properties = $_REQUEST;
        return;
    }

    /**
     * Método para obtener el valor de un indice de este objeto.
     * @param string $key
     * @return boolean
     */
    public function getProperty($key) {
        if (key_exists($key, $this->properties))
            return $this->properties[$key];
        else
            return false;
    }

    /**
     * Método para setear el valor de un nuevo indice o crearla.
     * @param string $key
     * @param object $value
     * @return boolean
     */
    public function setProperty($key, $value) {
        if (key_exists($key, $this->properties))
            $this->properties[$key] = $value;
        else
            return false;
    }

    /**
     * Método para registrar los mensajes de tipo feedback
     * @param string $message
     */
    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    /**
     * Método para obtener los feedbacks
     * @param string $separator
     * @return string
     */
    public function getFeedbackString($separator = \'\n\') {
        $returnValue = (string) \'\';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}
';
                break;
            case $pathPackage . '/mvc/controller/Session.php':
                $returnValue = '<?php

/**
 * Archivo para controlar las sessiones.
 */

namespace Mvc\Controller;

require_once \'mvc/base/SessionRegistry.php\';

/**
 * Clase controladora de sessiones.
 * 
 * Clase para manipular las sessiones como objeto.
 * 
 * @package Newmodule
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class mvc_controller_Session {

    /**
     *
     * @var array 
     */
    private $properties = array();

    /**
     *
     * @var array 
     */
    private $feedback = array();

    /**
     * Método para incializar los valores de la session en curso.
     */
    public function __construct() {
        $this->init();
        mvc_base_SessionRegistry::setSession($this);
    }

    /**
     * Método para iniciar session si es que no existieray regitrar en su propiedad 
     * el valor de la session.
     * @return void
     */
    public function init() {
        if (strlen(trim(session_id())) > 0)
            $this->properties = &$_SESSION;
        else
            session_start();
        return;
    }

    /**
     * Método para obtener el valor de una session.
     * @param string $key
     * @return boolean
     */
    public function getProperty($key) {
        if (key_exists($key, $this->properties))
            return $this->properties[$key];
        else
            return false;
    }

    /**
     * Método para registrar un valor en una session de PHP.
     * @param string $key
     * @param object $value
     */
    public function setProperty($key, $value) {
        $this->properties[$key] = $value;
    }

    /**
     * Método para eliminar un valor de la sesión.
     * @param string $key
     */
    public function deleteProperty($key) {
        if (key_exists($key, $this->properties))
            unset($this->properties[$key]);
    }

    /**
     * Método para obtener los feedbacks.
     * @return array
     */
    public function getFeedback() {
        $returnValue = array();
        $returnValue = $this->feedback;
        return (array) $returnValue;
    }

    /**
     * Método para registrar los feedbacks
     * @param string $message
     */
    public function addFeedback($message) {
        array_push($this->feedback, $message);
    }

    /**
     * Método para obtener los feedbacks separador por el caracter que desees.
     * @param string $separator
     * @return string
     */
    public function getFeedbackString($separator = \'\n\') {
        $returnValue = (string) \'\';
        $returnValue = implode($separator, $this->feedback);
        return $returnValue;
    }

}';
                break;
            case $pathPackage . '/model/Model.php':
                $returnValue = '<?php

/**
 * Archivo para definir una sola clase de tipo Model que es la que se creará
 * todos los metodos para interactuar con la Base de datos directamente
 * a traves del ORM de Moodle
 */

namespace ' . ucfirst(strtolower($name)) . '\Model;

/**
 * Clase Model para definir los metodos que proveerá los datos de la BD
 * 
 * Esta clase se instanciará en todos los controladores para ser usados por estos
 * y de esta manera tener separados el modelo y el controlador.
 * 
 * @package ' . ucfirst(strtolower($name)) . '
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class Model {

    /**
     * Método para obtener el detalle del usuario que unició session
     * @global array $USER
     * @global array $DB
     * @return object
     */
    public function getUser() {
        global $USER, $DB;
        $returnValue = NULL;
        $user = $DB->get_record(\'user\', array(\'id\' => $USER->id));
        if (is_object($user)) {
            $returnValue = $user;
        }
        return $returnValue;
    }

}';
                break;
            default:
                    if($default=='controller'){
                $returnValue = '<?php
/**
 * Archivo para definir una sola clase de tipo controlador y sus métodos
 * para desarrollar una acción o un caso de uso, según el analisis del
 * requerimiento.
 */

namespace ' . ucfirst(strtolower($name)) . '\Controller;
require_once(__DIR__ . \'/../mvc/command/Command.php\');
require_once(__DIR__ . \'/../model/Model.php\');

/**
 * Clase controladora ' . ucfirst(strtolower($name)) . ' para cargar las vistas iniciales.
 * 
 * Esta clase se autogenera con em metodo index por defecto que carga la vista
 * index de la carpeta views/' . ucfirst(strtolower($name)) . '/index.php.
 * 
 * @package ' . ucfirst(strtolower($name)) . '
 * @author Johnny Huamani <jhuamanip@pucp.pe>
 * @version 1.0
 */
class '.ucwords(strtolower($nameC)).'Controller extends mvc_command_Command {

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
            \'usuario\'=>$USER->firstname,
            \'controlador\'=>\''.ucwords(strtolower($nameC)).'\',
            \'name_plugin\'=>  get_string(\'pluginname\', \'local_' . $name . '\'),
            \'objUsuario\'=> $objUsuario
            ); 
    }
}';                        
                    }else{
                       if($default=='view'){ 
$returnValue = '<h1>Bievenido <?php echo $usuario ?> al controlador <?php echo $controlador ?> del plugin <?php echo $name_plugin ?></h1>

<?php
print_object($objUsuario);
?> ';                  
                    }
                    }
                break;
        }

        return $returnValue;
    }
    
    public function generateController($param){
        $returnValue = FALSE;
        umask(0);
        $pathPlugin = getcwd().'/'.$param[3].'/'.$param[4].'/controllers/';
        $pathPluginView = getcwd().'/'.$param[3].'/'.$param[4].'/views/';
        if(file_exists($pathPlugin)){
            //verificamos si el controlador ya existe
            $file = $pathPlugin.ucwords(strtolower($param[5])).'Controller.php';
            $folderView = $pathPluginView. ucwords(strtolower($param[5])).'/';
            $fileView = $pathPluginView.ucwords(strtolower($param[5])).'/index.php';
            if(!file_exists($file) && !file_exists($folderView) && !file_exists($fileView)){
                //creamos el archivo controladora
                $myfile = fopen($file, "w") or die("Unable to open file!");
                $txt = $this->getContentFile($pathPlugin, $file, $param[4], 'controller', $param[5]);
                fwrite($myfile, $txt);
                fclose($myfile);
                //creamos su vista index por defecto
                mkdir($folderView);
                //creamos el archivo de la vista del nuevo controlador
                $myfileView = fopen($fileView, "w") or die("Unable to open file!");
                $txtView = $this->getContentFile($pathPlugin, $fileView, $param[4], 'view', $param[5]);
                fwrite($myfileView, $txtView);
                fclose($myfileView);
                $returnValue = TRUE;
            }
        }
        return $returnValue;
    }

}
