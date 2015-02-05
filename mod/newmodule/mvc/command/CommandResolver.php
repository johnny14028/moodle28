<?php
require_once(__DIR__.'/../command/Command.php');
require_once(__DIR__.'/../controller/Request.php');
require_once(__DIR__.'/../command/DefaultCommand.php');
class mvc_command_CommandResolver {

    private static $base_cmd = NULL;
    private static $default_cmd = NULL;

    public function __construct() {
        if (!self::$base_cmd) {
            self::$base_cmd = new ReflectionClass('mvc_command_Command');
        }

        if (!self::$default_cmd) {
            self::$default_cmd = new mvc_command_DefaultCommand();
        }

        $filePath = 'controllers/DefaultController.php';
        $className = 'DefaultController';

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
        $cmd = $objRequest->getProperty('cmd');
        $func = $objRequest->getProperty('action');

        if (!$cmd)
            $cmd = 'Default';
        if (!$func)
            $func = 'Index';
        $cmd = str_replace(array('.', '/', '\\'), '', $cmd);
        $func = str_replace(array('.', '/', '\\'), '', $func);



        $filepath_tmp = "controllers/";
        $prefijoClassName = 'Controller';

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
                $objRequest->addFeedback("La clase '$className' del comando $cmd no se encontro");
                echo ("La clase '$className' del comando $cmd no se encontro. Incluido desde FilePath: $filepath");
                $returnValue = clone self::$default_cmd;
            }
        } else {
            $objRequest->addFeedback("El comando $cmd no se encontro");
            echo ("El comando $cmd no se encontro");
            $returnValue = clone self::$default_cmd;
        }
        return $returnValue;
    }

}