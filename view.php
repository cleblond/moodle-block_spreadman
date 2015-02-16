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
// COURSE REQUEST MANAGER BLOCK FOR MOODLE
// by Kyle Goslin & Daniel McSweeney
// Copyright 2012-2014 - Institute of Technology Blanchardstown.
// --------------------------------------------------------- 
/**
 * COURSE REQUEST MANAGER
 *
 * @package    block_spreadman
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");

global $CFG, $USER, $DB;
require_once("$CFG->libdir/formslib.php");
require_once('../../course/lib.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_login();

/** Navigation Bar **/
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('spreadmanDisplay', 'block_spreadman'));
$PAGE->navbar->add('View Spreadsheet/Chart');
$PAGE->set_url('/blocks/spreadman/view.php');
$PAGE->set_pagelayout('course');
$context = context_system::instance();
$PAGE->set_context($context);
print_object($context);
$courseid=optional_param('id',NULL,PARAM_INT);
$sheetid=optional_param('sheetid',NULL,PARAM_INT);
$chartid=optional_param('chartid',NULL,PARAM_INT);

$PAGE->set_heading('View Spreadsheet');
$PAGE->set_title('title');
echo $OUTPUT->header();

list($context, $course, $cm) = get_context_info_array($context->id);

if(isset($sheetid)){
$result = $DB->get_record('filter_spreadsheet_sheet', array('sheetid'=>$sheetid));

    if($result->userid == $USER->id){
        $filteredtext = filter_manager::instance()->filter_text('<div class="eo_spreadsheet ' . $sheetid . '"></div>', $context);
        print_object($filteredtext);
	echo $filteredtext;
	//$result = $DB->get_record('filter_chart_users', array('id'=>$chartid));	
    }

}

if(isset($chartid)){
		$result = $DB->get_record('filter_chart_users', array('id'=>$chartid));
	if($result->userid == $USER->id){
		$filteredtext = filter_manager::instance()->filter_text('<div class="eo_chart ' . $chartid . '"></div>', $context);
		echo $filteredtext;

	} //end userid check if
}





echo $OUTPUT->footer();



