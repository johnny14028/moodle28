<?php

include_once 'colors.php';

class pde_module {

    private $path = 'mod';
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

    public function existPackage($path, $package) {
        $returnValue = FALSE;
        $dirs = array_filter(glob($path . '*'), 'is_dir');
        if (in_array($path . $package, $dirs)) {
            $returnValue = TRUE;
        }
        return $returnValue;
    }

}
