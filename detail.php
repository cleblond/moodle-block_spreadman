<?php
// --------------------------------------------------------- 
// block_cmanager is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// block_cmanager is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// --------------------------------------------------------- 
/**
 * Spreadsheet/Chart Manager
 *
 * @package    block_spreadman
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once(dirname(__FILE__) . '/locallib.php');
global $CFG, $USER, $DB;
require_once("$CFG->libdir/formslib.php");
require_once('../../course/lib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_login();
/** Navigation Bar **/
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('spreadmanDisplay', 'block_spreadman'));
$PAGE->navbar->add('Manager');
$PAGE->set_url('/blocks/spreadman/detail.php');
$PAGE->set_pagelayout('course');
$context = context_system::instance();
$PAGE->set_context($context);
$courseid = optional_param('id', NULL, PARAM_INT);
$PAGE->set_heading(get_string('pluginname', 'block_spreadman'));
$PAGE->set_title(get_string('pluginname', 'block_spreadman'));
echo $OUTPUT->header();
$courses = enrol_get_my_courses();
if (!isset($courseid) or $courseid == 1) {
    $cselector     = '';
    $firstcourseid = key($courses);
    $courseid      = $firstcourseid;
}
$cselector = 'Choose Course - <select onChange="document.location.href=this[selectedIndex].value">';
$selected  = ($courseid == 0) ? 'selected' : '';
$cselector .= '<option value="detail.php?id=0" ' . $selected . '>Orphaned</option>';
foreach ($courses as $course) {
    $selected = ($courseid == $course->id) ? 'selected' : '';
    $cselector .= '<option value="detail.php?id=' . $course->id . '" ' . $selected . '>' . $course->shortname . '</option>';
}
$cselector .= '</select>';
$content        = new stdClass;
$content->items = array();
$spreadtext     = '';
$currentqresult = '';
// Get all spreadsheets
if ($courseid === 0) {
    $content->text = '<tr><th><h3>Spreadsheets</h3></th></tr>';
    //Must be my home page!  Get all psreadsheets from all courses.
    $courses       = enrol_get_my_courses();
    //print_object($courses);
    $result        = $DB->get_records('filter_spreadsheet_sheet', array(
        'userid' => $USER->id
    ));
    foreach ($result as $row) {
        foreach ($courses as $course) {
            $q = 'eo_spreadsheet ' . $row->sheetid;
            $currentqresult .= get_sheet_in_course($course->id, $q);
        }
        if ($currentqresult === '') {
            $name = ($row->name == NULL) ? 'Untitled Sheet' : $row->name;
            $name = '<a href="view.php?courseid=' . $courseid . '&sheetid=' . $row->sheetid . '">' . $name . '</a>';
            //echo $name;
            $spreadtext .= '<tr><td>' . $name . '</td></tr>';
        }
        $currentqresult = '';
        ;
    }
    $content->text .= $cselector . $spreadtext . '</table>';
    //echo $content->;
} else {
    $content->text = '<tr><th><h3>Spreadsheets</h3></th></tr>';
    //Add each spreadsheet for this course to block!
    $orphaned      = array();
    $title         = array();
    $result        = $DB->get_records('filter_spreadsheet_sheet', array(
        'userid' => $USER->id
    ));
    //print_object($result);
    foreach ($result as $row) {
        $q              = 'eo_spreadsheet ' . $row->sheetid;
        $name           = ($row->name == NULL) ? 'Untitled Sheet' : $row->name;
        $name           = '<a href="view.php?courseid=' . $courseid . '&sheetid=' . $row->sheetid . '">' . $name . '</a>';
        $currentqresult = get_sheet_in_course($courseid, $q);
        if ($currentqresult !== '') {
            $spreadtext .= '<tr><td>' . $name . '</td><td>' . $currentqresult . '</td></tr>';
        } else {
            array_push($orphaned, $row->sheetid);
            array_push($title, $name);
            ///orphan sheet
        }
    }
    if ($spreadtext == '') {
        $content->text .= '<tr><td>No spreadsheets in this course</td></tr>';
    } else {
        $content->text .= '<tr><th>Name</th><th>Page Location</th></tr>' . $spreadtext;
    }
    if ($content->text !== '') {
        $content->text = $cselector . '<table class="spreadman">' . $content->text . '</table>';
    }
}
//Now get all charts
$charttext = '';
if ($courseid === 0) {
    $charttext .= '<table class="spreadman"><tr><th><h3>Charts</h3></th><th>' . $currentqresult . '</th></tr>';
    $courses = enrol_get_my_courses();
    //print_object($courses);
    $result  = $DB->get_records('filter_chart_users', array(
        'userid' => $USER->id
    ));
    foreach ($result as $row) {
        foreach ($courses as $course) {
            $q = 'eo_chart ' . $row->id;
            $currentqresult .= get_sheet_in_course($course->id, $q);
        }
        if ($currentqresult === '') {
            $name = ($row->title == NULL) ? 'Untitled Chart' : $row->title;
            $name = '<a href="view.php?courseid=' . $courseid . '&chartid=' . $row->id . '">' . $name . '</a>';
            $charttext .= '<tr><td>' . $name . '</td><td>' . $currentqresult . '</td></tr>';
        }
        $currentqresult = '';
        ;
    }
    $content->text .= $charttext . '</table>';
} else {
    //Add each chart for this course!
    $charttext .= '<table class="spreadman"><tr><th><h3>Charts</h3></th></tr>';
    $orphaned = array();
    $title    = array();
    $result   = $DB->get_records('filter_chart_users', array(
        'userid' => $USER->id
    ));
    foreach ($result as $row) {
        $q              = 'eo_chart ' . $row->id;
        $name           = ($row->title == NULL) ? 'Untitled Chart' : $row->title;
        $name           = '<a href="view.php?courseid=' . $courseid . '&chartid=' . $row->id . '">' . $name . '</a>';
        $currentqresult = get_sheet_in_course($courseid, $q);
        if ($currentqresult !== '') {
            $charttext .= '<tr><td>' . $name . '</td><td>' . $currentqresult . '</td></tr>';
        } else {
            array_push($orphaned, $row->id);
            array_push($title, $name);
            ///orphan sheet
        }
    }
    if ($charttext === '') {
        //$charttext .= '<tr><td>No charts in this course</td></tr>';
        $charttext .= '<table class="spreadman"><tr><td><h3>Charts</h3></td><td></td></tr><tr><td>No charts in this course</td></tr>';
    } else {
        $charttext .= '<tr><th>Name</th><th>Page Location</th></tr>' . $charttext;
    }
    $content->text .= $charttext . '</table>';
} //end if courseid==0
echo $content->text;
echo $OUTPUT->footer();
