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
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace ' . $name . ' with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).\'/config.php\');
require_once(dirname(__FILE__).\'/lib.php\');

$id = required_param(\'id\', PARAM_INT); // Course.

$course = $DB->get_record(\'course\', array(\'id\' => $id), \'*\', MUST_EXIST);

require_course_login($course);

$params = array(
    \'context\' => context_course::instance($course->id)
);
$event = \mod_' . $name . '\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot(\'course\', $course);
$event->trigger();

$strname = get_string(\'modulenameplural\', \'mod_' . $name . '\');
$PAGE->set_url(\'/mod/' . $name . '/index.php\', array(\'id\' => $id));
$PAGE->navbar->add($strname);
$PAGE->set_title("$course->shortname: $strname");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout(\'incourse\');

echo $OUTPUT->header();
echo $OUTPUT->heading($strname);

if (! $' . $name . 's = get_all_instances_in_course(\'' . $name . '\', $course)) {
    notice(get_string(\'no' . $name . 's\', \'' . $name . '\'), new moodle_url(\'/course/view.php\', array(\'id\' => $course->id)));
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
foreach ($modinfo->instances[\'' . $name . '\'] as $cm) {
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
            case $pathPackage . '/localib.php':
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
 * Internal library of functions for module ' . $name . '
 *
 * All the ' . $name . ' specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

/*
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 *function ' . $name . '_do_something_useful(array $things) {
 *    return new stdClass();
 *}
 */
';
                break;
            case $pathPackage . '/version.php':
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
 * Defines the version and other meta-info about the plugin
 *
 * Setting the $plugin->version to 0 prevents the plugin from being installed.
 * See https://docs.moodle.org/dev/version.php for more info.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

$plugin->component = \'mod_' . $name . '\';
$plugin->version = 2010022400;
$plugin->release = \'v0.0\';
$plugin->requires = 2010021900;
$plugin->maturity = MATURITY_ALPHA;
$plugin->cron = 0;
$plugin->dependencies = array();
';
                break;
            case $pathPackage . '/README.txt':
                $returnValue = 'The following steps should get you up and running with
this module template code.

* DO NOT PANIC!

* Unzip the archive and read this file

* Rename the ' . $name . '/ folder to the name of your module (eg "widget").
  The module folder MUST be lower case and can\'t contain underscores. You should check the CVS contrib
  area at http://cvs.moodle.org/contrib/plugins/mod/ to make sure that
  your name is not already used by an other module. Registering the plugin
  name @ http://moodle.org/plugins will secure it for you.

* Edit all the files in this directory and its subdirectories and change
  all the instances of the string "' . $name . '" to your module name
  (eg "widget"). If you are using Linux, you can use the following command
  $ find . -type f -exec sed -i \'s/' . $name . '/widget/g\' {} \;

  On a mac, use:
  $ find . -type f -exec sed -i \'\' \'s/' . $name . '/widget/g\' {} \;

* Rename the file lang/en/' . $name . '.php to lang/en/widget.php
  where "widget" is the name of your module

* Rename all files in backup/moodle2/ folder by replacing "' . $name . '" with
  the name of your module

  On Linux you can perform this and previous steps by calling:
  $ find . -depth -name \'*' . $name . '*\' -execdir bash -c \'mv -i "$1" "${1//' . $name . '/widget}"\' bash {} \;

* Place the widget folder into the /mod folder of the moodle
  directory.

* Go to Settings > Site Administration > Development > XMLDB editor
  and modify the module\'s tables.
  Make sure, that the web server has write-access to the db/ folder.
  You need at least one table, even if your module doesn\'t use it.

* Modify version.php and set the initial version of you module.

* Visit Settings > Site Administration > Notifications, you should find
  the module\'s tables successfully created

* Go to Site Administration > Plugins > Activity modules > Manage activities
  and you should find that this ' . $name . ' has been added to the list of
  installed modules.

* You may now proceed to run your own code in an attempt to develop
  your module. You will probably want to modify mod_form.php and view.php
  as a first step. Check db/access.php to add capabilities.

We encourage you to share your code and experience - visit http://moodle.org

Good luck!
';
                break;
            case $pathPackage . '/view.php':
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
 * Prints a particular instance of ' . $name . '
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Replace ' . $name . ' with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))) . \'/config.php\');
require_once(dirname(__FILE__) . \'/lib.php\');
require_once(\'mvc/controller/Controller.php\');

$id = optional_param(\'id\', 0, PARAM_INT); // Course_module ID, or
$n = optional_param(\'n\', 0, PARAM_INT);  // ... ' . $name . ' instance ID - it should be named as the first character of the module.

if ($id) {
    $cm = get_coursemodule_from_id(\'' . $name . '\', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record(\'course\', array(\'id\' => $cm->course), \'*\', MUST_EXIST);
    $' . $name . ' = $DB->get_record(\'' . $name . '\', array(\'id\' => $cm->instance), \'*\', MUST_EXIST);
} else if ($n) {
    $' . $name . ' = $DB->get_record(\'' . $name . '\', array(\'id\' => $n), \'*\', MUST_EXIST);
    $course = $DB->get_record(\'course\', array(\'id\' => $' . $name . '->course), \'*\', MUST_EXIST);
    $cm = get_coursemodule_from_instance(\'' . $name . '\', $' . $name . '->id, $course->id, false, MUST_EXIST);
} else {
    error(\'You must specify a course_module ID or an instance ID\');
}

require_login($course, true, $cm);

$event = \mod_' . $name . '\event\course_module_viewed::create(array(
            \'objectid\' => $PAGE->cm->instance,
            \'context\' => $PAGE->context,
        ));
$event->add_record_snapshot(\'course\', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $' . $name . ');
$event->trigger();

// Print the page header.

$PAGE->set_url(\'/mod/' . $name . '/view.php\', array(\'id\' => $cm->id));
$PAGE->set_title(format_string($' . $name . '->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol(\'some-html-id\');
 * $PAGE->add_body_class(\'' . $name . '-\'.$somevar);
 */

if (isset($_SERVER[\'HTTP_X_REQUESTED_WITH\'])
        AND strtolower($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === \'xmlhttprequest\') {
    mvc_controller_Controller::run();
} else {
// Output starts here.
    echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
    if ($' . $name . '->intro) {
        echo $OUTPUT->box(format_module_intro(\'' . $name . '\', $' . $name . ', $cm->id), \'generalbox mod_introbox\', \'' . $name . 'intro\');
    }

// Replace the following lines with you own code.
    $PAGE->requires->js(\'/mod/' . $name . '/js/jquery.js\');
    $PAGE->requires->js(\'/mod/' . $name . '/js/' . $name . '.js\');
// Finish the page.
    mvc_controller_Controller::run();
    echo $OUTPUT->footer();
}
';
                break;
            case $pathPackage . '/grade.php':
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
 * Redirect the user to the appropriate submission related page
 *
 * @package   mod_' . $name . '
 * @category  grade
 * @copyright 2015 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "../../../config.php");

$id = required_param(\'id\', PARAM_INT);// Course module ID.
// Item number may be != 0 for activities that allow more than one grade per user.
$itemnumber = optional_param(\'itemnumber\', 0, PARAM_INT);
$userid = optional_param(\'userid\', 0, PARAM_INT); // Graded user ID (optional).

// In the simplest case just redirect to the view page.
redirect(\'view.php?id=\'.$id);
';
                break;
            case $pathPackage . '/lib.php':
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
 * Library of interface functions and constants for module ' . $name . '
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the ' . $name . ' specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

/**
 * Example constant, you probably want to remove this :-)
 */
define(\'' . strtoupper(strtolower($name)) . '_ULTIMATE_ANSWER\', 42);

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function ' . $name . '_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the ' . $name . ' into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $' . $name . ' Submitted data from the form in mod_form.php
 * @param mod_' . $name . '_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted ' . $name . ' record
 */
function ' . $name . '_add_instance(stdClass $' . $name . ', mod_' . $name . '_mod_form $mform = null) {
    global $DB;

    $' . $name . '->timecreated = time();

    // You may have to add extra stuff in here.

    $' . $name . '->id = $DB->insert_record(\'' . $name . '\', $' . $name . ');

    ' . $name . '_grade_item_update($' . $name . ');

    return $' . $name . '->id;
}

/**
 * Updates an instance of the ' . $name . ' in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $' . $name . ' An object from the form in mod_form.php
 * @param mod_' . $name . '_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function ' . $name . '_update_instance(stdClass $' . $name . ', mod_' . $name . '_mod_form $mform = null) {
    global $DB;

    $' . $name . '->timemodified = time();
    $' . $name . '->id = $' . $name . '->instance;

    // You may have to add extra stuff in here.

    $result = $DB->update_record(\'' . $name . '\', $' . $name . ');

    ' . $name . '_grade_item_update($' . $name . ');

    return $result;
}

/**
 * Removes an instance of the ' . $name . ' from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function ' . $name . '_delete_instance($id) {
    global $DB;

    if (! $' . $name . ' = $DB->get_record(\'' . $name . '\', array(\'id\' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records(\'' . $name . '\', array(\'id\' => $' . $name . '->id));

    ' . $name . '_grade_item_delete($' . $name . ');

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $' . $name . ' The ' . $name . ' instance record
 * @return stdClass|null
 */
function ' . $name . '_user_outline($course, $user, $mod, $' . $name . ') {

    $return = new stdClass();
    $return->time = 0;
    $return->info = \'\';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $' . $name . ' the module instance record
 */
function ' . $name . '_user_complete($course, $user, $mod, $' . $name . ') {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in ' . $name . ' activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function ' . $name . '_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link ' . $name . '_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added \'cmid\' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user\'s activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group\'s activity only, defaults to 0 (all groups)
 */
function ' . $name . '_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link ' . $name . '_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added \'cmid\' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users\' full names
 */
function ' . $name . '_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function ' . $name . '_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array(\'moodle/site:accessallgroups\') if the
 * module uses that capability.
 *
 * @return array
 */
function ' . $name . '_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of ' . $name . '?
 *
 * This function returns if a scale is being used by one ' . $name . '
 * if it has support for grading and scales.
 *
 * @param int $' . $name . 'id ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given ' . $name . ' instance
 */
function ' . $name . '_scale_used($' . $name . 'id, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists(\'' . $name . '\', array(\'id\' => $' . $name . 'id, \'grade\' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of ' . $name . '.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any ' . $name . ' instance
 */
function ' . $name . '_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists(\'' . $name . '\', array(\'grade\' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given ' . $name . ' instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $' . $name . ' instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function ' . $name . '_grade_item_update(stdClass $' . $name . ', $reset=false) {
    global $CFG;
    require_once($CFG->libdir.\'/gradelib.php\');

    $item = array();
    $item[\'itemname\'] = clean_param($' . $name . '->name, PARAM_NOTAGS);
    $item[\'gradetype\'] = GRADE_TYPE_VALUE;

    if ($' . $name . '->grade > 0) {
        $item[\'gradetype\'] = GRADE_TYPE_VALUE;
        $item[\'grademax\']  = $' . $name . '->grade;
        $item[\'grademin\']  = 0;
    } else if ($' . $name . '->grade < 0) {
        $item[\'gradetype\'] = GRADE_TYPE_SCALE;
        $item[\'scaleid\']   = -$' . $name . '->grade;
    } else {
        $item[\'gradetype\'] = GRADE_TYPE_NONE;
    }

    if ($reset) {
        $item[\'reset\'] = true;
    }

    grade_update(\'mod/' . $name . '\', $' . $name . '->course, \'mod\', \'' . $name . '\',
            $' . $name . '->id, 0, null, $item);
}

/**
 * Delete grade item for given ' . $name . ' instance
 *
 * @param stdClass $' . $name . ' instance object
 * @return grade_item
 */
function ' . $name . '_grade_item_delete($' . $name . ') {
    global $CFG;
    require_once($CFG->libdir.\'/gradelib.php\');

    return grade_update(\'mod/' . $name . '\', $' . $name . '->course, \'mod\', \'' . $name . '\',
            $' . $name . '->id, 0, null, array(\'deleted\' => 1));
}

/**
 * Update ' . $name . ' grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $' . $name . ' instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function ' . $name . '_update_grades(stdClass $' . $name . ', $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.\'/gradelib.php\');

    // Populate array of grade objects indexed by userid.
    $grades = array();

    grade_update(\'mod/' . $name . '\', $' . $name . '->course, \'mod\', \'' . $name . '\', $' . $name . '->id, 0, $grades);
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area \'intro\' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function ' . $name . '_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for ' . $name . ' file areas
 *
 * @package mod_' . $name . '
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function ' . $name . '_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the ' . $name . ' file areas
 *
 * @package mod_' . $name . '
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the ' . $name . '\'s context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function ' . $name . '_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

/* Navigation API */

/**
 * Extends the global navigation tree by adding ' . $name . ' nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the ' . $name . ' module instance
 * @param stdClass $course current course record
 * @param stdClass $module current ' . $name . ' instance record
 * @param cm_info $cm course module information
 */
function ' . $name . '_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // TODO Delete this function and its docblock, or implement it.
}

/**
 * Extends the settings navigation with the ' . $name . ' settings
 *
 * This function is called when the context for the page is a ' . $name . ' module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $' . $name . 'node ' . $name . ' administration node
 */
function ' . $name . '_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $' . $name . 'node=null) {
    // TODO Delete this function and its docblock, or implement it.
}
';
                break;
            case $pathPackage . '/mod_form.php':
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
 * The main ' . $name . ' configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

require_once($CFG->dirroot.\'/course/moodleform_mod.php\');

/**
 * Module instance settings form
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_' . $name . '_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement(\'header\', \'general\', get_string(\'general\', \'form\'));

        // Adding the standard "name" field.
        $mform->addElement(\'text\', \'name\', get_string(\'' . $name . 'name\', \'' . $name . '\'), array(\'size\' => \'64\'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType(\'name\', PARAM_TEXT);
        } else {
            $mform->setType(\'name\', PARAM_CLEAN);
        }
        $mform->addRule(\'name\', null, \'required\', null, \'client\');
        $mform->addRule(\'name\', get_string(\'maximumchars\', \'\', 255), \'maxlength\', 255, \'client\');
        $mform->addHelpButton(\'name\', \'' . $name . 'name\', \'' . $name . '\');

        // Adding the standard "intro" and "introformat" fields.
        $this->add_intro_editor();

        // Adding the rest of ' . $name . ' settings, spreading all them into this fieldset
        // ... or adding more fieldsets (\'header\' elements) if needed for better logic.
        $mform->addElement(\'static\', \'label1\', \'' . $name . 'setting1\', \'Your ' . $name . ' fields go here. Replace me!\');

        $mform->addElement(\'header\', \'' . $name . 'fieldset\', get_string(\'' . $name . 'fieldset\', \'' . $name . '\'));
        $mform->addElement(\'static\', \'label2\', \'' . $name . 'setting2\', \'Your ' . $name . ' fields go here. Replace me!\');

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
';
                break;
            case $pathPackage . '/controllers/DefaultController.php':
                $returnValue = '<?php
require_once(__DIR__.\'/../mvc/command/Command.php\');
require_once(__DIR__.\'/../model/Model.php\');
class DefaultController extends mvc_command_Command{
    private $model;
    
    public function __construct() {
        $this->model = new Model();
    }
    
    public function index(){
        global $USER;
        $objUsuario = $this->model->getUser();
        return array(
            \'usuario\'=>$USER->firstname,
            \'name_plugin\'=>  get_string(\'pluginname\', \'' . $name . '\'),
            \'objUsuario\'=> $objUsuario
            );        
    }
}';
                break;
            case $pathPackage . '/db/install.php':
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
 * Provides code to be executed during the module installation
 *
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_' . $name . '_install() {
}

/**
 * Post installation recovery procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_' . $name . '_install_recovery() {
}
';
                break;
            case $pathPackage . '/db/access.php':
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
 * Capability definitions for the ' . $name . ' module
 *
 * The capabilities are loaded into the database table when the module is
 * installed or updated. Whenever the capability definitions are updated,
 * the module version number should be bumped up.
 *
 * The system has four possible values for a capability:
 * CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
 *
 * It is important that capability names are unique. The naming convention
 * for capabilities that are specific to modules and blocks is as follows:
 *   [mod/block]/<plugin_name>:<capabilityname>
 *
 * component_name should be the same as the directory name of the mod or block.
 *
 * Core moodle capabilities are defined thus:
 *    moodle/<capabilityclass>:<capabilityname>
 *
 * Examples: mod/forum:viewpost
 *           block/recent_activity:view
 *           moodle/site:deleteuser
 *
 * The variable name for the capability definitions array is $capabilities
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

// Modify capabilities as needed and remove this comment.
$capabilities = array(
    \'mod/' . $name . ':addinstance\' => array(
        \'riskbitmask\' => RISK_XSS,
        \'captype\' => \'write\',
        \'contextlevel\' => CONTEXT_COURSE,
        \'archetypes\' => array(
            \'editingteacher\' => CAP_ALLOW,
            \'manager\' => CAP_ALLOW
        ),
        \'clonepermissionsfrom\' => \'moodle/course:manageactivities\'
    ),

    \'mod/' . $name . ':view\' => array(
        \'captype\' => \'read\',
        \'contextlevel\' => CONTEXT_MODULE,
        \'legacy\' => array(
            \'guest\' => CAP_ALLOW,
            \'student\' => CAP_ALLOW,
            \'teacher\' => CAP_ALLOW,
            \'editingteacher\' => CAP_ALLOW,
            \'manager\' => CAP_ALLOW
        )
    ),

    \'mod/' . $name . ':submit\' => array(
        \'riskbitmask\' => RISK_SPAM,
        \'captype\' => \'write\',
        \'contextlevel\' => CONTEXT_MODULE,
        \'legacy\' => array(
            \'student\' => CAP_ALLOW
        )
    ),
);
';
                break;
            case $pathPackage . '/db/uninstall.php':
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
 * Provides code to be executed during the module uninstallation
 *
 * @see uninstall_plugin()
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom uninstallation procedure
 */
function xmldb_' . $name . '_uninstall() {
    return true;
}
';
                break;
            case $pathPackage . '/db/upgrade.php':
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
 * This file keeps track of upgrades to the ' . $name . ' module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there\'s something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

/**
 * Execute ' . $name . ' upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_' . $name . '_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    /*
     * And upgrade begins here. For each one, you\'ll need one
     * block of code similar to the next one. Please, delete
     * this comment lines once this file start handling proper
     * upgrade code.
     *
     * if ($oldversion < YYYYMMDD00) { //New version in version.php
     * }
     *
     * Lines below (this included)  MUST BE DELETED once you get the first version
     * of your module ready to be installed. They are here only
     * for demonstrative purposes and to show how the ' . $name . '
     * iself has been upgraded.
     *
     * For each upgrade block, the file ' . $name . '/version.php
     * needs to be updated . Such change allows Moodle to know
     * that this file has to be processed.
     *
     * To know more about how to write correct DB upgrade scripts it\'s
     * highly recommended to read information available at:
     *   http://docs.moodle.org/en/Development:XMLDB_Documentation
     * and to play with the XMLDB Editor (in the admin menu) and its
     * PHP generation posibilities.
     *
     * First example, some fields were added to install.xml on 2007/04/01
     */
    if ($oldversion < 2007040100) {

        // Define field course to be added to ' . $name . '.
        $table = new xmldb_table(\'' . $name . '\');
        $field = new xmldb_field(\'course\', XMLDB_TYPE_INTEGER, \'10\', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, \'0\', \'id\');

        // Add field course.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field intro to be added to ' . $name . '.
        $table = new xmldb_table(\'' . $name . '\');
        $field = new xmldb_field(\'intro\', XMLDB_TYPE_TEXT, \'medium\', null, null, null, null, \'name\');

        // Add field intro.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field introformat to be added to ' . $name . '.
        $table = new xmldb_table(\'' . $name . '\');
        $field = new xmldb_field(\'introformat\', XMLDB_TYPE_INTEGER, \'4\', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, \'0\',
            \'intro\');

        // Add field introformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Once we reach this point, we can store the new version and consider the module
        // ... upgraded to the version 2007040100 so the next time this block is skipped.
        upgrade_mod_savepoint(true, 2007040100, \'' . $name . '\');
    }

    // Second example, some hours later, the same day 2007/04/01
    // ... two more fields and one index were added to install.xml (note the micro increment
    // ... "01" in the last two digits of the version).
    if ($oldversion < 2007040101) {

        // Define field timecreated to be added to ' . $name . '.
        $table = new xmldb_table(\'' . $name . '\');
        $field = new xmldb_field(\'timecreated\', XMLDB_TYPE_INTEGER, \'10\', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, \'0\',
            \'introformat\');

        // Add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timemodified to be added to ' . $name . '.
        $table = new xmldb_table(\'' . $name . '\');
        $field = new xmldb_field(\'timemodified\', XMLDB_TYPE_INTEGER, \'10\', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, \'0\',
            \'timecreated\');

        // Add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index course (not unique) to be added to ' . $name . '.
        $table = new xmldb_table(\'' . $name . '\');
        $index = new xmldb_index(\'courseindex\', XMLDB_INDEX_NOTUNIQUE, array(\'course\'));

        // Add index to course field.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Another save point reached.
        upgrade_mod_savepoint(true, 2007040101, \'' . $name . '\');
    }

    // Third example, the next day, 2007/04/02 (with the trailing 00),
    // some actions were performed to install.php related with the module.
    if ($oldversion < 2007040200) {

        // Insert code here to perform some actions (same as in install.php).

        upgrade_mod_savepoint(true, 2007040200, \'' . $name . '\');
    }

    /*
     * And that\'s all. Please, examine and understand the 3 example blocks above. Also
     * it\'s interesting to look how other modules are using this script. Remember that
     * the basic idea is to have "blocks" of code (each one being executed only once,
     * when the module version (version.php) is updated.
     *
     * Lines above (this included) MUST BE DELETED once you get the first version of
     * yout module working. Each time you need to modify something in the module (DB
     * related, you\'ll raise the version and add one upgrade block here.
     *
     * Finally, return of upgrade result (true, all went good) to Moodle.
     */
    return true;
}
';
                break;
            case $pathPackage . '/db/install.xml':
                $returnValue = '<?xml version="1.0" encoding="UTF-8" ?>
<XMLDB PATH="mod/' . $name . '/db" VERSION="20101203" COMMENT="XMLDB file for Moodle mod/' . $name . '"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="../../../lib/xmldb/xmldb.xsd"
>
  <TABLES>
    <TABLE NAME="' . $name . '" COMMENT="Default comment for ' . $name . ', please edit me">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="true"/>
        <FIELD NAME="course" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="false" COMMENT="Course ' . $name . ' activity belongs to"/>
        <FIELD NAME="name" TYPE="char" LENGTH="255" NOTNULL="true" SEQUENCE="false" COMMENT="name field for moodle instances"/>
        <FIELD NAME="intro" TYPE="text" NOTNULL="true" SEQUENCE="false" COMMENT="General introduction of the ' . $name . ' activity"/>
        <FIELD NAME="introformat" TYPE="int" LENGTH="4" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" COMMENT="Format of the intro field (MOODLE, HTML, MARKDOWN...)"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="false"/>
        <FIELD NAME="timemodified" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false"/>
        <FIELD NAME="grade" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="100" SEQUENCE="false" COMMENT="The maximum grade. Can be negative to indicate the use of a scale."/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
      </KEYS>
      <INDEXES>
        <INDEX NAME="course" UNIQUE="false" FIELDS="course"/>
      </INDEXES>
    </TABLE>
  </TABLES>
</XMLDB>
';
                break;
            case $pathPackage . '/lang/es/' . $name . '.php':
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
 * English strings for ' . $name . '
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

$string[\'modulename\'] = \'' . $name . '\';
$string[\'modulenameplural\'] = \'' . $name . 's\';
$string[\'modulename_help\'] = \'Use the ' . $name . ' module for... | The ' . $name . ' module allows...\';
$string[\'' . $name . 'fieldset\'] = \'Custom example fieldset\';
$string[\'' . $name . 'name\'] = \'' . $name . ' name\';
$string[\'' . $name . 'name_help\'] = \'This is the content of the help tooltip associated with the ' . $name . 'name field. Markdown syntax is supported.\';
$string[\'' . $name . '\'] = \'' . $name . '\';
$string[\'pluginadministration\'] = \'' . $name . ' administration\';
$string[\'pluginname\'] = \'' . $name . '\';
$string[\'no' . $name . 's\'] = \'Not found this module in mod directory\';
';
                break;
            case $pathPackage . '/lang/en/' . $name . '.php':
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
 * English strings for ' . $name . '
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

$string[\'modulename\'] = \'' . $name . '\';
$string[\'modulenameplural\'] = \'' . $name . 's\';
$string[\'modulename_help\'] = \'Use the ' . $name . ' module for... | The ' . $name . ' module allows...\';
$string[\'' . $name . 'fieldset\'] = \'Custom example fieldset\';
$string[\'' . $name . 'name\'] = \'' . $name . ' name\';
$string[\'' . $name . 'name_help\'] = \'This is the content of the help tooltip associated with the ' . $name . 'name field. Markdown syntax is supported.\';
$string[\'' . $name . '\'] = \'' . $name . '\';
$string[\'pluginadministration\'] = \'' . $name . ' administration\';
$string[\'pluginname\'] = \'' . $name . '\';
$string[\'no' . $name . 's\'] = \'Not found this module in mod directory\';
';
                break;
            case $pathPackage . '/views/Default/index.php':
                $returnValue = '<h1>Bievenido <?php echo $usuario ?> al <?php echo $name_plugin ?></h1>

<?php
print_object($objUsuario);
?> ';
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

        $func = str_replace(array(\'.\', \'/\', \'\\\\\'), \'\', $func);
        $cmd = str_replace(array(\'.\', \'/\', \'\\\\\'), \'\', $cmd);
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
        $cmd = str_replace(array(\'.\', \'/\', \'\\\\\'), \'\', $cmd);
        $func = str_replace(array(\'.\', \'/\', \'\\\\\'), \'\', $func);



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
            case $pathPackage . '/mvc/controller/Controller.php':
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
            case $pathPackage . '/mvc/controller/Request.php':
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
            case $pathPackage . '/mvc/controller/Session.php':
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
            case $pathPackage . '/model/Model.php':
                $returnValue = '<?php

class Model {
    
    public function getUser(){
        global $USER, $DB;
        $returnValue = NULL;
        $user = $DB->get_record(\'user\', array(\'id\'=>$USER->id));
        if(is_object($user)){
            $returnValue = $user;
        }
        return $returnValue;
    }
    
}';
                break;
            case $pathPackage . '/backup/moodle2/backup_' . $name . '_activity_task.class.php':
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
 * Defines backup_' . $name . '_activity_task class
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die;

require_once($CFG->dirroot . \'/mod/' . $name . '/backup/moodle2/backup_' . $name . '_stepslib.php\');

/**
 * Provides the steps to perform one complete backup of the ' . $name . ' instance
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_' . $name . '_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the ' . $name . '.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_' . $name . '_activity_structure_step(\'' . $name . '_structure\', \'' . $name . '.xml\'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, \'/\');

        // Link to the list of ' . $name . 's.
        $search = \'/(\'.$base.\'\/mod\/' . $name . '\/index.php\?id\=)([0-9]+)/\';
        $content = preg_replace($search, \'$@' . strtoupper(strtolower($name)) . 'INDEX*$2@$\', $content);

        // Link to ' . $name . ' view by moduleid.
        $search = \'/(\'.$base.\'\/mod\/' . $name . '\/view.php\?id\=)([0-9]+)/\';
        $content = preg_replace($search, \'$@' . strtoupper(strtolower($name)) . 'VIEWBYID*$2@$\', $content);

        return $content;
    }
}
';
                break;
            case $pathPackage . '/backup/moodle2/backup_' . $name . '_stepslib.php':
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
 * Define all the backup steps that will be used by the backup_' . $name . '_activity_task
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die;

/**
 * Define the complete ' . $name . ' structure for backup, with file and id annotations
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_' . $name . '_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value(\'userinfo\');

        // Define the root element describing the ' . $name . ' instance.
        $' . $name . ' = new backup_nested_element(\'' . $name . '\', array(\'id\'), array(
            \'name\', \'intro\', \'introformat\', \'grade\'));

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $' . $name . '->set_source_table(\'' . $name . '\', array(\'id\' => backup::VAR_ACTIVITYID));

        // If we were referring to other tables, we would annotate the relation
        // with the element\'s annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $' . $name . '->annotate_files(\'mod_' . $name . '\', \'intro\', null);

        // Return the root element (' . $name . '), wrapped into standard activity structure.
        return $this->prepare_activity_structure($' . $name . ');
    }
}
';
                break;
            case $pathPackage . '/backup/moodle2/restore_' . $name . '_activity_task.class.php':
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
 * Provides the restore activity task class
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(\'MOODLE_INTERNAL\') || die();

require_once($CFG->dirroot . \'/mod/' . $name . '/backup/moodle2/restore_' . $name . '_stepslib.php\');

/**
 * Restore task for the ' . $name . ' activity module
 *
 * Provides all the settings and steps to perform complete restore of the activity.
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_' . $name . '_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // We have just one structure step here.
        $this->add_step(new restore_' . $name . '_activity_structure_step(\'' . $name . '_structure\', \'' . $name . '.xml\'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content(\'' . $name . '\', array(\'intro\'), \'' . $name . '\');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule(\'' . strtoupper(strtolower($name)) . 'VIEWBYID\', \'/mod/' . $name . '/view.php?id=$1\', \'course_module\');
        $rules[] = new restore_decode_rule(\'' . strtoupper(strtolower($name)) . 'INDEX\', \'/mod/' . $name . '/index.php?id=$1\', \'course\');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * ' . $name . ' logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule(\'' . $name . '\', \'add\', \'view.php?id={course_module}\', \'{' . $name . '}\');
        $rules[] = new restore_log_rule(\'' . $name . '\', \'update\', \'view.php?id={course_module}\', \'{' . $name . '}\');
        $rules[] = new restore_log_rule(\'' . $name . '\', \'view\', \'view.php?id={course_module}\', \'{' . $name . '}\');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule(\'' . $name . '\', \'view all\', \'index.php?id={course}\', null);

        return $rules;
    }
}
';
                break;
            case $pathPackage . '/backup/moodle2/restore_' . $name . '_stepslib.php':
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
 * Define all the restore steps that will be used by the restore_' . $name . '_activity_task
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one ' . $name . ' activity
 *
 * @package   mod_' . $name . '
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_' . $name . '_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element(\'' . $name . '\', \'/activity/' . $name . '\');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_' . $name . '($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid(\'scale\', abs($data->grade)));
        }

        // Create the ' . $name . ' instance.
        $newitemid = $DB->insert_record(\'' . $name . '\', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add ' . $name . ' related files, no need to match by itemname (just internally handled context).
        $this->add_related_files(\'mod_' . $name . '\', \'intro\', null);
    }
}
';
                break;
            case $pathPackage . '/classes/event/course_module_instance_list_viewed.php':
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
 * The mod_' . $name . ' instance list viewed event.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_' . $name . '\event;

defined(\'MOODLE_INTERNAL\') || die();

/**
 * The mod_' . $name . ' instance list viewed event class.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_instance_list_viewed extends \core\event\course_module_instance_list_viewed {
}
';
                break;
            case $pathPackage . '/classes/event/course_module_viewed.php':
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
 * Defines the view event.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_' . $name . '\event;

defined(\'MOODLE_INTERNAL\') || die();

/**
 * The mod_' . $name . ' instance list viewed event class
 *
 * If the view mode needs to be stored as well, you may need to
 * override methods get_url() and get_legacy_log_data(), too.
 *
 * @package    mod_' . $name . '
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Initialize the event
     */
    protected function init() {
        $this->data[\'objecttable\'] = \'' . $name . '\';
        parent::init();
    }
}
';
                break;
            default:
                if ($default == 'controller') {
                    $returnValue = '<?php
require_once(__DIR__.\'/../mvc/command/Command.php\');
require_once(__DIR__.\'/../model/Model.php\');
class ' . ucwords(strtolower($nameC)) . 'Controller extends mvc_command_Command{
    private $model;
    
    public function __construct() {
        $this->model = new Model();
    }
    
    public function index(){
        global $USER;
        $objUsuario = $this->model->getUser();
        return array(
            \'usuario\'=>$USER->firstname,
            \'controlador\'=>\'' . ucwords(strtolower($nameC)) . '\',
            \'name_plugin\'=>  get_string(\'pluginname\', \'' . $name . '\'),
            \'objUsuario\'=> $objUsuario
            );        
    }
}';
                } else {
                    if ($default == 'view') {
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
    
    public function generateController($param){
        $returnValue = FALSE;
        $param[3] = 'mod';
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
