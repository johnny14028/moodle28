<?php

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
 * Prints a particular instance of preguntados
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_preguntados
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Replace preguntados with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once('mvc/controller/Controller.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... preguntados instance ID - it should be named as the first character of the module.
$format = optional_param('format', 'html', PARAM_ACTION);  // ... preguntados instance ID - it should be named as the first character of the module.
if ($id) {
    $cm = get_coursemodule_from_id('preguntados', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $preguntados = $DB->get_record('preguntados', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $preguntados = $DB->get_record('preguntados', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $preguntados->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('preguntados', $preguntados->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_preguntados\event\course_module_viewed::create(array(
            'objectid' => $PAGE->cm->instance,
            'context' => $PAGE->context,
        ));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $preguntados);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/preguntados/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($preguntados->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('preguntados-'.$somevar);
 */

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    mvc_controller_Controller::run();
} else {
    switch ($format) {
        case 'json':
            header('Content-Type: application/json');
            mvc_controller_Controller::run();
            break;
        default:
            // Output starts here.
            echo $OUTPUT->header();

            // Conditions to show the intro can change to look for own settings or whatever.
            if ($preguntados->intro) {
                echo $OUTPUT->box(format_module_intro('preguntados', $preguntados, $cm->id), 'generalbox mod_introbox', 'preguntadosintro');
            }

            // Replace the following lines with you own code.
            $PAGE->requires->js('/mod/preguntados/js/jquery.js');
            $PAGE->requires->js('/mod/preguntados/js/preguntados.js');
            // Finish the page.
            mvc_controller_Controller::run();
            echo $OUTPUT->footer();
            break;
    }
}
