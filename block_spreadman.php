<?php

//include('searchlib.php');
class block_spreadman extends block_base {
    public function init() {
        $this->title = get_string('spreadman', 'block_spreadman');
    }




public function get_content() {
global  $USER, $DB, $CFG;
  if ($this->content !== null) {
    return $this->content;
  }

  //print_object($result);
  $course = $this->page->course;
  $courseid=$course->id;
  echo "courseid=$courseid";
  $this->content         = new stdClass;
  $this->content->items  = array();
  $this->content->icons  = array();
//  $this->content->footer = 'Footer here...';
  $this->content->text = '';


if ($courseid === "1") {
        //Must be my home page!  Get all psreadsheets from all courses.
	echo "Must be my home page";
	$courses = enrol_get_my_courses();

	//print_object($courses);
/*
	foreach ($courses as $course){

	$result = $DB->get_records('filter_spreadsheet_sheet',array('userid'=>$USER->id));
		foreach ($result as &$row) {
                  $q = 'sheet="'.$row->sheetid.'"';
		    //print_object($row);
		  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => 'jjj'));
		  $this->content->text .= $this->get_sheet_in_course($course->id, $q);
		}

	}

*/

} else {

	//Add each spreadsheet for this course to block!
	$result = $DB->get_records('filter_spreadsheet_sheet',array('userid'=>$USER->id));
	foreach ($result as &$row) {
	   //print_object($row);
	  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => $row->pageurl));
          //echo $row->sheetid;
          $q = 'sheet="'.$row->sheetid.'"';
	  $this->content->text .= $this->get_sheet_in_course($courseid, $q);
	}
}


  if ($this->content->text !== ''){
    $this->content->text = "<strong>Spreadsheets</strong><br>".$this->content->text;

    }

//Now get all charts
$charttext='';
if ($courseid === "1") {

        //Must be my home page!  Get all psreadsheets from all courses.
	echo "Must be my home page";
	$courses = enrol_get_my_courses();

	//print_object($courses);
/*
	foreach ($courses as $course){

	$result = $DB->get_records('filter_chart_users',array('userid'=>$USER->id));
		foreach ($result as &$row) {
		    //print_object($row);
                  $q = 'chart="'.$row->id.'"';
		  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => 'jjj'));
		  $charttext .= $this->get_sheet_in_course($course->id, $q);
		}

	}
*/


} else {

	//Add each spreadsheet for this course to block!
	$result = $DB->get_records('filter_chart_users',array('userid'=>$USER->id));
	foreach ($result as &$row) {
	   //print_object($row);
          $q = 'chart="'.$row->id.'"';
	  //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => $row->pageurl));
          echo $row->id;
	  $charttext .= $this->get_sheet_in_course($courseid, $q);
	}
}

  if ($charttext !== ''){
    $charttext = "<strong>Charts/Graphs</strong><br>".$charttext;

    }


       $this->content->text .= $charttext.'<br>';



       $this->content->text .= html_writer::tag('a', 'Open Manager', array('href' => $CFG->wwwroot.'/blocks/spreadman/detail.php?id='.$courseid));
  //$this->content->items[] = html_writer::tag('a', 'Menu Option 1', array('href' => 'some_file.php'));
  //$this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/1.gif', 'class' => 'icon'));
 
  // Add more list items here
 





  return $this->content;

}



public function get_sheet_in_course($courseid, $q)
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
        $module_result = $this->block_spreadman_search_module($courseid, $module, $q);
    }
    if ($module_result != '')
    {
        $printed = true;
    }
    $return .= $module_result;
    $i++;
}

//Sections must be searched apart*****************************
$section_result = $this->block_spreadman_search_section($courseid, $q);

if ($section_result != '')
{
    $printed = true;
}
$return .= $section_result;
////

return $return;


}








public function block_spreadman_search_module($courseid, $module, $q)
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

/**
 * Search in a module content in the common fields (name, intro, content)
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @param int $courseid The course ID
 * @param string $q The string searched
 * @return string Return the result in HTML
 */
public function block_spreadman_search_section($courseid, $q)
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
public function block_spreadman_search_module_label($courseid, $module, $q, $modinfo)
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








 
}   // Here's the closing bracket for the class definition


    // The PHP tag and the curly bracket for the class definition 
    // will only be closed after there is another function added in the next section.
