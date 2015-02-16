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
$plugin->version = 2015021500;  // YYYYMMDDHH (year, month, day, 24-hr time)
$plugin->requires = 2013110500; // YYYYMMDDHH (This is the release version for Moodle 2.0)

$plugin->dependencies = array(
    'filter_chart' => 2015020700,
    'filter_spreadsheet' => 2015020700
);
