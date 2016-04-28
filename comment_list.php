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
 * Ajax request to this script gives all comments of a question back. 
 * Requires GET param "question_id"
 * 
 * @package    mod_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('MOODLE_INTERNAL', true);

require_once(dirname(__FILE__) . '/lib.php');

if (!isset($_GET['questionid']) || empty($_GET['questionid'])) {
    return http_response_code(404);
}

header('Content-Type: text/html; charset=utf-8');

echo comment_renderer(intval($_GET['questionid']));
