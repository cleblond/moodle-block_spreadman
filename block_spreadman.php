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
class block_spreadman extends block_base {
    public function init() {
        $this->title = get_string('spreadman', 'block_spreadman');
    }
    public function get_content() {
        global $USER, $DB, $CFG;
        require_once(dirname(__FILE__) . '/locallib.php');
        if ($this->content !== null) {
            return $this->content;
        }
        //print_object($result);
        $course               = $this->page->course;
        $courseid             = $course->id;
        echo "courseid=$courseid";
        $this->content        = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        //  $this->content->footer = 'Footer here...';
        $this->content->text  = '';
        if ($courseid === "1") {
            //Must be my home page!  Get all psreadsheets from all courses.
            //echo "Must be my home page";
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
            $result = $DB->get_records('filter_spreadsheet_sheet', array(
                'userid' => $USER->id
            ));
            foreach ($result as &$row) {
                //print_object($row);
                //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => $row->pageurl));
                //echo $row->sheetid;
                $q = 'sheet="' . $row->sheetid . '"';
                $q = 'eo_spreadsheet ' . $row->sheetid;
                $this->content->text .= get_sheet_in_course($courseid, $q);
            }
        }
        if ($this->content->text !== '') {
            $this->content->text = "<strong>Spreadsheets</strong><br>" . $this->content->text;
        }
        //Now get all charts
        $charttext = '';
        if ($courseid === "1") {
            //Must be my home page!  Get all psreadsheets from all courses.
            //echo "Must be my home page";
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
            $result = $DB->get_records('filter_chart_users', array(
                'userid' => $USER->id
            ));
            foreach ($result as &$row) {
                //print_object($row);
                // $q = 'chart="'.$row->id.'"';
                $q = 'eo_chart ' . $row->id;
                //$this->content->items[] = html_writer::tag('a', ($row->name == '') ? 'Untitled' : $row->name, array('href' => $row->pageurl));
                //echo $row->id;
                $charttext .= get_sheet_in_course($courseid, $q);
            }
        }
        if ($charttext !== '') {
            $charttext = "<strong>Charts/Graphs</strong><br>" . $charttext;
        }
        $this->content->text .= $charttext . '<br>';
        $this->content->text .= html_writer::tag('a', 'Open Manager', array(
            'href' => $CFG->wwwroot . '/blocks/spreadman/detail.php?id=' . $courseid
        ));
        //$this->content->items[] = html_writer::tag('a', 'Menu Option 1', array('href' => 'some_file.php'));
        //$this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/1.gif', 'class' => 'icon'));
        // Add more list items here
        return $this->content;
    }
}
