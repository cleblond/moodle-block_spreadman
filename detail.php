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
 * @package    block_cmanager
 * @copyright  2014 Kyle Goslin, Daniel McSweeney
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
//$PAGE->navbar->add(get_string('modrequestfacility', 'block_cmanager'));
$PAGE->set_url('/blocks/spreadman/detail.php');
$context = context_system::instance();
$PAGE->set_context($context);
$courseid=optional_param('id','1',PARAM_INT);

$PAGE->set_heading(get_string('pluginname', 'block_spreadman'));
$PAGE->set_title(get_string('pluginname', 'block_spreadman'));
echo $OUTPUT->header();


//echo "here";

  //print_object($result);
  //$course = $PAGE->course;
  //$courseid=$course->id;
  //echo "courseid=$courseid";
  $content         = new stdClass;
  $content->items  = array();
  $content->text = '<table><tr><th>Name</th><th>Page Location</th></tr>';


if ($courseid === 1) {
        //Must be my home page!  Get all psreadsheets from all courses.
	//echo "Must be my home page";
	$courses = enrol_get_my_courses();

	//print_object($courses);

	foreach ($courses as $course){

	$result = $DB->get_records('filter_spreadsheet_sheet',array('userid'=>$USER->id));
		foreach ($result as &$row) {
                  $q = 'sheet="'.$row->sheetid.'"';
		    //print_object($row);
		  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => 'jjj'));
		  $content->text .= get_sheet_in_course($course->id, $q);
		}

	}



} else {

	//Add each spreadsheet for this course to block!
        $orphaned =  array();
        $title = array();
	$result = $DB->get_records('filter_spreadsheet_sheet',array('userid'=>$USER->id));
	foreach ($result as &$row) {
	   //print_object($row);
	  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => $row->pageurl));
          //echo $row->sheetid;
          $q = 'sheet="'.$row->sheetid.'"';
          $name = ($row->name ==NULL) ? 'Untitled Sheet - ' : $row->name;
          $currentqresult = get_sheet_in_course($courseid, $q);
          if($currentqresult !== ''){
          $content->text .= '<tr><td>'.$name.'</td><td>'.$currentqresult.'</td></tr>';
          } else {
          array_push($orphaned, $row->sheetid);
          array_push($title, $name);
          ///orphan sheet
          }
	  
	}
}
//print_object($orphaned);
  //add orphaned to output
   $content->text .= '<tr><th>Orphaned Spreadsheets</th></tr>';
   $i=0;
   foreach ($orphaned as $orphane) {
//   $content->text .= '<tr><td>'.$orphane.'</td></tr>';
   $content->text .= '<tr><td>'.$orphane.'</td><td>'.$title[$i].'</td></tr>';
   $i++;
   }

  if ($content->text !== ''){
    $content->text = "<tr><td><h3>Spreadsheets</h3></td></tr>".$content->text;

    }

//Now get all charts
$charttext='';
if ($courseid === 1) {
        //Must be my home page!  Get all psreadsheets from all courses.
	//echo "Must be my home page";
	$courses = enrol_get_my_courses();

	//print_object($courses);

	foreach ($courses as $course){

	$result = $DB->get_records('filter_chart_users',array('userid'=>$USER->id));
		foreach ($result as &$row) {
		    //print_object($row);
                  $q = 'chart="'.$row->id.'"';
		  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => 'jjj'));
		  $charttext .= get_sheet_in_course($course->id, $q);
		}

	}



} else {

	//Add each chart for this course to block!
        $orphaned = array();
        $title = array();
	$result = $DB->get_records('filter_chart_users',array('userid'=>$USER->id));
	foreach ($result as &$row) {
	   //print_object($row);
          $q = 'chart="'.$row->id.'"';
	  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => $row->pageurl));
          //echo $row->id;
          $name = ($row->title ==NULL) ? 'Untitled Chart - ' : $row->title;
          $currentqresult = get_sheet_in_course($courseid, $q);
          if($currentqresult !== ''){
          $charttext .= '<tr><td>'.$name.'</td><td>'.$currentqresult.'</td></tr>';
          }  else {
          array_push($orphaned, $row->id);
          array_push($title, $name);
          ///orphan sheet
          }
	  //$charttext .= get_sheet_in_course($courseid, $q);
	}
}

  if ($charttext !== ''){
    $charttext = "<tr><td><h3>Charts/Graphs</h3></td></tr><tr><th>Name</th><th>Page Location</th></tr>".$charttext;

    }


  //add orphaned to output
   $charttext .= '<tr><th>Orphaned Charts</th></tr>';
   $i=0;
   foreach ($orphaned as $orphane) {
   $charttext .= '<tr><td>'.$orphane.'</td><td>'.$title[$i].'</td></tr>';
   $i++;
   }



       $content->text .= $charttext;



       //$content->text .= html_writer::tag('a', 'Open Manager', array('href' => $CFG->wwwroot.'/blocks/spreadman/detail.php'));
  //$this->content->items[] = html_writer::tag('a', 'Menu Option 1', array('href' => 'some_file.php'));
  //$this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/1.gif', 'class' => 'icon'));
 
  // Add more list items here
 


  echo $content->text.'</table>';

  echo $OUTPUT->footer();
function get_sheet_in_course($courseid, $q)
{
global $DB;

//$sheetid='1411408100';
//$q='sheet="'.$sheetid.'"';
//echo $q;
$modules = $DB->get_records_sql('SELECT name FROM {modules}');
$course = $DB->get_record('course', array('id' => $courseid));
$modinfo = get_fast_modinfo($course);
$i = 1;
$printed = false;
$return='';
//echo "HERE";
foreach ($modules as $module)
{
    $functionname = "block_spreadman_search_module_" . $module->name;
    $module_result = '';
    //If a specific function exists called
    if (function_exists($functionname))
    {
        $module_result = call_user_func($functionname, $courseid, $module, $q, $modinfo);
    }
    else
    {
        $module_result = block_spreadman_search_module($courseid, $module, $q);
    }
    if ($module_result != '')
    {
        $printed = true;
    }
    $return .= $module_result;
    $i++;
}

//Sections must be searched apart*****************************
$section_result = block_spreadman_search_section($courseid, $q);

if ($section_result != '')
{
    $printed = true;
}
$return .= $section_result;
////

return $return;


}



function block_spreadman_search_module($courseid, $module, $q)
{
    global $CFG, $DB, $OUTPUT;
    //echo "here2";
    $ret = '';
    $sqlWere = 'course=? AND (false';
    $sqlParams = array($courseid);
    //At least one search field is needed
    $onefield = false;

    //The DBman will be use to check if table and field exists
    $dbman = $DB->get_manager();


    //Check if the table exists
    if ($dbman->table_exists($module->name))
    {
        //Check if the fields exists
        if ($dbman->field_exists($module->name, 'name'))
        {
            $sqlWere .= " OR name LIKE ?";
            $sqlParams[] = "%$q%";
            $onefield = true;
        }
        if ($dbman->field_exists($module->name, 'intro'))
        {
            $sqlWere .= " OR intro LIKE ?";
            $sqlParams[] = "%$q%";
            $onefield = true;
        }
        if ($dbman->field_exists($module->name, 'content'))
        {
            $sqlWere .= " OR content LIKE ?";
            $sqlParams[] = "%$q%";
            $onefield = true;
        }

        //Do the search
        if ($onefield)
        {
            $sql = "SELECT * FROM {" . $module->name . "} WHERE $sqlWere)";

            //get sql
            $results = $DB->get_records_sql($sql, $sqlParams);
            //To create the link we need more info
            //find modid
            $modid = $DB->get_record('modules', array('name' => $module->name));

            foreach ($results as $result)
            {
                $this_course_mod = $DB->get_record('course_modules', array('course' => $courseid, 'module' => $modid->id, 'instance' => $result->id));
                $ret .= "<a href='$CFG->wwwroot/mod/$module->name/view.php?id=$this_course_mod->id'><img src='" . $OUTPUT->pix_url('icon', $module->name) . "' alt='$module->name -'/>&nbsp;$result->name</a><br>";
            }
        }
    }
    return $ret;
}

function block_spreadman_search_section($courseid, $q)
{
    global $CFG, $DB, $OUTPUT;
    //echo "HERE SECTION";
    $ret = '';
    $sqlParams = array($courseid, "%$q%", "%$q%");

    $sql = "SELECT * FROM {course_sections} WHERE course=? AND (summary LIKE ? OR name LIKE ?)";

    //get sql
    $results = $DB->get_records_sql($sql, $sqlParams);

    foreach ($results as $result)
    {
        $link = "<a href='$CFG->wwwroot/course/view.php?id=$courseid#section-$result->section'><img src='" . $OUTPUT->pix_url('icon', 'label') . "' alt='section - '/>&nbsp;$result->name</a><br>";
        $ret .= $link;
    }
    return $ret;
}

/**
 * Search in a module content in the common fields (name, intro, content)
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $courseid The course ID
 * @param stdClass $module The module object
 * @param string $q The string searched
 * @param stdClass $modinfo The modinfo object
 * @return string Return the result in HTML
 */
function block_spreadman_search_module_label($courseid, $module, $q, $modinfo)
{
    global $CFG, $DB, $OUTPUT;

    $ret = '';
    $sqlParams = array($courseid, "%$q%", "%$q%");

    $sql = "SELECT * FROM {label} WHERE course=? AND (intro LIKE ? OR name LIKE ?)";
    //get sql

    $results = $DB->get_records_sql($sql, $sqlParams);
    //To create the link we need more info
    //find modid
    $modid = $DB->get_record('modules', array('name' => 'label'));

    //Get All sections
    $sections = $modinfo->get_sections();

    foreach ($results as $result)
    {
        $sectionfounded = null;
        $this_course_mod = $DB->get_record('course_modules', array('course' => $courseid, 'module' => $modid->id, 'instance' => $result->id));

        foreach ($sections as $sectionnum => $section)
        {
            foreach ($section as $mod)
            {
                //If mod id == the course mod id
                if ($mod == $this_course_mod->id)
                {
                    //now find the name of the section
                    $sectionfounded = $DB->get_record('course_sections', array('course' => $courseid, 'section' => $sectionnum));
                    break 2;
                }
            }
        }

        if ($sectionfounded != null)
        {
            $ret .= "<a href='$CFG->wwwroot/course/view.php?id=$courseid#section-$sectionfounded->section'><img src='" . $OUTPUT->pix_url('icon', 'label') . "' alt='label - '/>&nbsp;$result->name</a><br>";
        }
    }
    return $ret;
}

/**
 * Search in a module tab content
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $courseid The course ID
 * @param stdClass $module The module object
 * @param string $q The string searched
 * @param stdClass $modinfo The modinfo object
 * @return string Return the result in HTML
 */
function block_spreadman_search_module_tab($courseid, $module, $q, $modinfo)
{
    global $CFG, $DB, $OUTPUT;

    $ret = '';
    $sqlParams = array($courseid, "%$q%", "%$q%", "%$q%", "%$q%");

    $sql = "SELECT {tab_content}.id as tabcontentid, {tab}.id as id,{tab}.name, {tab}.intro, {tab}.course, {tab_content}.tabname, {tab_content}.tabcontent
                    FROM {tab_content} 
                        INNER JOIN {tab} ON {tab_content}.tabid = {tab}.id AND {tab}.course = ?
                    WHERE {tab}.name LIKE ? OR {tab}.intro LIKE ?
                          OR {tab_content}.tabname LIKE ? OR {tab_content}.tabcontent LIKE ?";
    //get sql
    $results = $DB->get_records_sql($sql, $sqlParams);
    //To create the link we need more info
    //find modid
    $modid = $DB->get_record('modules', array('name' => 'tab'));
    $c = 1;
    foreach ($results as $result)
    {
        $this_course_mod = $DB->get_record('course_modules', array('course' => $courseid, 'module' => $modid->id, 'instance' => $result->id));
        $ret .= "<a href='$CFG->wwwroot/mod/tab/view.php?id=$this_course_mod->id'><img src='" . $OUTPUT->pix_url('icon', 'tab') . "' alt=''/>&nbsp;$result->name</a><br>";
        $c++;
    }

    return $ret;
}




