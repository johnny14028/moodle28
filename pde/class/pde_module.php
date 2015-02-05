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

    private function getContentFile($pathPackage, $file, $name, $default = '', $nameC = '') {
        $returnValue = '';
        switch ($file) {
            case $pathPackage . '/index.php':
                $returnValue = '<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_newmodule
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace newmodule with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).\'/config.php\');
require_once(dirname(__FILE__).\'/lib.php\');

$id = required_param(\'id\', PARAM_INT); // Course.

$course = $DB->get_record(\'course\', array(\'id\' => $id), \'*\', MUST_EXIST);

require_course_login($course);

$params = array(
    \'context\' => context_course::instance($course->id)
);
$event = \mod_newmodule\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot(\'course\', $course);
$event->trigger();

$strname = get_string(\'modulenameplural\', \'mod_newmodule\');
$PAGE->set_url(\'/mod/newmodule/index.php\', array(\'id\' => $id));
$PAGE->navbar->add($strname);
$PAGE->set_title("$course->shortname: $strname");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout(\'incourse\');

echo $OUTPUT->header();
echo $OUTPUT->heading($strname);

if (! $newmodules = get_all_instances_in_course(\'newmodule\', $course)) {
    notice(get_string(\'nonewmodules\', \'newmodule\'), new moodle_url(\'/course/view.php\', array(\'id\' => $course->id)));
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes[\'class\'] = \'generaltable mod_index\';

if ($usesections) {
    $strsectionname = get_string(\'sectionname\', \'format_\'.$course->format);
    $table->head  = array ($strsectionname, $strname);
    $table->align = array (\'center\', \'left\');
} else {
    $table->head  = array ($strname);
    $table->align = array (\'left\');
}

$modinfo = get_fast_modinfo($course);
$currentsection = \'\';
foreach ($modinfo->instances[\'newmodule\'] as $cm) {
    $row = array();
    if ($usesections) {
        if ($cm->sectionnum !== $currentsection) {
            if ($cm->sectionnum) {
                $row[] = get_section_name($course, $cm->sectionnum);
            }
            if ($currentsection !== \'\') {
                $table->data[] = \'hr\';
            }
            $currentsection = $cm->sectionnum;
        }
    }

    $class = $cm->visible ? null : array(\'class\' => \'dimmed\');

    $row[] = html_writer::link(new moodle_url(\'view.php\', array(\'id\' => $cm->id)),
                $cm->get_formatted_name(), $class);
    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
';
                break;
        }
        return $returnValue;
    }

    private function listFile($pathPackage, $name) {
        $returnValue = array();
        array_push($returnValue, $pathPackage . '/index.php');
        array_push($returnValue, $pathPackage . '/localib.php');
        array_push($returnValue, $pathPackage . '/version.php');
        array_push($returnValue, $pathPackage . '/README.txt');
        array_push($returnValue, $pathPackage . '/view.php');
        array_push($returnValue, $pathPackage . '/grade.php');
        array_push($returnValue, $pathPackage . '/lib.php');
        array_push($returnValue, $pathPackage . '/mod_form.php');
        array_push($returnValue, $pathPackage . '/controllers/DefaultController.php');
        array_push($returnValue, $pathPackage . '/db/install.php');
        array_push($returnValue, $pathPackage . '/db/access.php');
        array_push($returnValue, $pathPackage . '/db/uninstall.php');
        array_push($returnValue, $pathPackage . '/db/upgrade.php');
        array_push($returnValue, $pathPackage . '/db/install.xml');
        array_push($returnValue, $pathPackage . '/js/' . $name . '.js');
        array_push($returnValue, $pathPackage . '/js/jquery.js');
        array_push($returnValue, $pathPackage . '/css/' . $name . '.css');
        array_push($returnValue, $pathPackage . '/lang/es/' . $name . '.php');
        array_push($returnValue, $pathPackage . '/lang/en/' . $name . '.php');
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
        array_push($returnValue, $pathPackage . '/backup/moodle2/backup_' . $name . '_activity_task.class.php');
        array_push($returnValue, $pathPackage . '/backup/moodle2/backup_' . $name . '_stepslib.php');
        array_push($returnValue, $pathPackage . '/backup/moodle2/restore_' . $name . '_activity_task.class.php');
        array_push($returnValue, $pathPackage . '/backup/moodle2/restore_' . $name . '_stepslib.php');
        array_push($returnValue, $pathPackage . '/classes/event/course_module_instance_list_viewed.php');
        array_push($returnValue, $pathPackage . '/classes/event/course_module_viewed.php');
        return $returnValue;
    }

    private function listFolder($pathPackage) {
        $returnValue = array();
        array_push($returnValue, $pathPackage);
        array_push($returnValue, $pathPackage . '/backup');
        array_push($returnValue, $pathPackage . '/backup/moodle2');
        array_push($returnValue, $pathPackage . '/classes');
        array_push($returnValue, $pathPackage . '/classes/event');
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

}
