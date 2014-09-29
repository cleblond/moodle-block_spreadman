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

require_once($CFG->dirroot . "/filter/spreadsheet/codebase/php/grid_cell_connector.php");

    $script = '<script src="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/spreadsheet.php?load=js"></script>';
            $script .= '<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlx_core.css">
                       <link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlxspreadsheet.css">
                       <link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlxgrid_wp.css">';



            $script = '<script>
		func'.$id.' = function() {
			var dhx_sh1'.$id.' = new dhtmlxSpreadSheet({
				load: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/php/data.php",
				save: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/php/data.php",
				parent: "gridobj'.$id.'",
				icons_path: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/imgs/icons/",
				math: true,
				autowidth: false,
				autoheight: false
			}); 
			dhx_sh1'.$id.'.load("'.$matches[1].'" , "'.$key.'");
		};
	        </script>
                <div class="ssheet_cont" id="gridobj'.$id.'"></div>';



echo $OUTPUT->footer();



