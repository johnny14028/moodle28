<?php
function autoload($class_name){
    var_dump($class_name);
    $arrayClassName = explode("_", $class_name);
    $file = $arrayClassName[(count($arrayClassName) - 1)];
    if (count($arrayClassName) > 1) {
        unset($arrayClassName[(count($arrayClassName) - 1)]);
    }
    switch ($arrayClassName[0]) {
        case 'mvc': 
            include_once implode("/", $arrayClassName) . "/" . $file . '.php';
            break;
//        default: 
//            include_once implode("/", $arrayClassName) . "/" . $class_name . '.php';
//            break;
    }
}

spl_autoload_register("autoload");