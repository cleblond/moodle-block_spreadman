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
defined('MOODLE_INTERNAL') || die;
function get_sheet_in_course($courseid, $q) {
    global $DB;
    //$sheetid='1411408100';
    //$q='sheet="'.$sheetid.'"';
    //echo $q;
    $modules = $DB->get_records_sql('SELECT name FROM {modules}');
    $course  = $DB->get_record('course', array(
        'id' => $courseid
    ));
    $modinfo = get_fast_modinfo($course);
    $i       = 1;
    $printed = false;
    $return  = '';
    //echo "HERE";
    foreach ($modules as $module) {
        $functionname  = "block_spreadman_search_module_" . $module->name;
        $module_result = '';
        //If a specific function exists called
        if (function_exists($functionname)) {
            $module_result = call_user_func($functionname, $courseid, $module, $q, $modinfo);
        } else {
            $module_result = block_spreadman_search_module($courseid, $module, $q);
        }
        if ($module_result != '') {
            $printed = true;
        }
        $return .= $module_result;
        $i++;
    }
    //Sections must be searched apart*****************************
    $section_result = block_spreadman_search_section($courseid, $q);
    if ($section_result != '') {
        $printed = true;
    }
    $return .= $section_result;
    ////
    return $return;
}
function block_spreadman_search_module($courseid, $module, $q) {
    global $CFG, $DB, $OUTPUT;
    //echo "here2";
    $ret       = '';
    $sqlWere   = 'course=? AND (false';
    $sqlParams = array(
        $courseid
    );
    //At least one search field is needed
    $onefield  = false;
    //The DBman will be use to check if table and field exists
    $dbman     = $DB->get_manager();
    //Check if the table exists
    if ($dbman->table_exists($module->name)) {
        //Check if the fields exists
        if ($dbman->field_exists($module->name, 'name')) {
            $sqlWere .= " OR name LIKE ?";
            $sqlParams[] = "%$q%";
            $onefield    = true;
        }
        if ($dbman->field_exists($module->name, 'intro')) {
            $sqlWere .= " OR intro LIKE ?";
            $sqlParams[] = "%$q%";
            $onefield    = true;
        }
        if ($dbman->field_exists($module->name, 'content')) {
            $sqlWere .= " OR content LIKE ?";
            $sqlParams[] = "%$q%";
            $onefield    = true;
        }
        //Do the search
        if ($onefield) {
            $sql     = "SELECT * FROM {" . $module->name . "} WHERE $sqlWere)";
            //get sql
            $results = $DB->get_records_sql($sql, $sqlParams);
            //To create the link we need more info
            //find modid
            $modid   = $DB->get_record('modules', array(
                'name' => $module->name
            ));
            foreach ($results as $result) {
                $this_course_mod = $DB->get_record('course_modules', array(
                    'course' => $courseid,
                    'module' => $modid->id,
                    'instance' => $result->id
                ));
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
function block_spreadman_search_section($courseid, $q) {
    global $CFG, $DB, $OUTPUT;
    //echo "HERE SECTION";
    $ret       = '';
    $sqlParams = array(
        $courseid,
        "%$q%",
        "%$q%"
    );
    $sql       = "SELECT * FROM {course_sections} WHERE course=? AND (summary LIKE ? OR name LIKE ?)";
    //get sql
    $results   = $DB->get_records_sql($sql, $sqlParams);
    foreach ($results as $result) {
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
function block_spreadman_search_module_label($courseid, $module, $q, $modinfo) {
    global $CFG, $DB, $OUTPUT;
    $ret       = '';
    $sqlParams = array(
        $courseid,
        "%$q%",
        "%$q%"
    );
    $sql       = "SELECT * FROM {label} WHERE course=? AND (intro LIKE ? OR name LIKE ?)";
    //get sql
    $results   = $DB->get_records_sql($sql, $sqlParams);
    //To create the link we need more info
    //find modid
    $modid     = $DB->get_record('modules', array(
        'name' => 'label'
    ));
    //Get All sections
    $sections  = $modinfo->get_sections();
    foreach ($results as $result) {
        $sectionfounded  = null;
        $this_course_mod = $DB->get_record('course_modules', array(
            'course' => $courseid,
            'module' => $modid->id,
            'instance' => $result->id
        ));
        foreach ($sections as $sectionnum => $section) {
            foreach ($section as $mod) {
                //If mod id == the course mod id
                if ($mod == $this_course_mod->id) {
                    //now find the name of the section
                    $sectionfounded = $DB->get_record('course_sections', array(
                        'course' => $courseid,
                        'section' => $sectionnum
                    ));
                    break 2;
                }
            }
        }
        if ($sectionfounded != null) {
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
function block_spreadman_search_module_tab($courseid, $module, $q, $modinfo) {
    global $CFG, $DB, $OUTPUT;
    $ret       = '';
    $sqlParams = array(
        $courseid,
        "%$q%",
        "%$q%",
        "%$q%",
        "%$q%"
    );
    $sql       = "SELECT {tab_content}.id as tabcontentid, {tab}.id as id,{tab}.name, {tab}.intro, {tab}.course, {tab_content}.tabname, {tab_content}.tabcontent
                    FROM {tab_content} 
                        INNER JOIN {tab} ON {tab_content}.tabid = {tab}.id AND {tab}.course = ?
                    WHERE {tab}.name LIKE ? OR {tab}.intro LIKE ?
                          OR {tab_content}.tabname LIKE ? OR {tab_content}.tabcontent LIKE ?";
    //get sql
    $results   = $DB->get_records_sql($sql, $sqlParams);
    //To create the link we need more info
    //find modid
    $modid     = $DB->get_record('modules', array(
        'name' => 'tab'
    ));
    $c         = 1;
    foreach ($results as $result) {
        $this_course_mod = $DB->get_record('course_modules', array(
            'course' => $courseid,
            'module' => $modid->id,
            'instance' => $result->id
        ));
        $ret .= "<a href='$CFG->wwwroot/mod/tab/view.php?id=$this_course_mod->id'><img src='" . $OUTPUT->pix_url('icon', 'tab') . "' alt=''/>&nbsp;$result->name</a><br>";
        $c++;
    }
    return $ret;
}
