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
 * Ajax requests to this script saves the ratings and comments.
 *
 * Require POST params:
 * "save" can be "vote" or "comment" (save type),
 * "questionid" is necessary for every request,
 * "rate" is necessary if the save type is "vote"
 * "text" is necessary if the save type is "comment"
 *
 * @package    qbehaviour_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../config.php');

$data = new \stdClass();
if (!isset($USER->id) || empty($USER->id)) {
    return;
}
$data->userid = $USER->id;

if (!isset($_POST['questionid']) || empty($_POST['questionid'])) {
    return;
}
$data->questionid = intval($_POST['questionid']);

if (!isset($_POST['save']) || empty($_POST['save'])) {
    return;
}

switch($_POST['save']) {
    case 'vote': save_vote($data);
        break;
    case 'comment': save_comment($data);
        break;
}

header('Content-Type: text/html; charset=utf-8');

/**
 * saves question rating
 *
 * @param  stdClass $data requires userid, questionid
 */
function save_vote($data) {
    global $DB, $USER;

    if (!isset($_POST['rate']) || empty($_POST['rate'])) {
        return;
    }
    $data->vote = intval($_POST['rate']);

    $row = $DB->get_record('studentquiz_vote', array('userid' => $USER->id, 'questionid' => $data->questionid));
    if ($row === false) {
        $DB->insert_record('studentquiz_vote', $data);
    } else {
        $row->vote = $data->vote;
        $var = $DB->update_record('studentquiz_vote', $row);
    }
}

/**
 * saves question comment
 *
 * @param  stdClass $data requires userid, questionid
 */
function save_comment($data) {
    global $DB;

    if (!isset($_POST['text']) || empty($_POST['text'])) {
        return;
    }

    // Prevent XSS.
    $data->comment = htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8');
    $data->created = usertime(time(), usertimezone());

    $DB->insert_record('studentquiz_comment', $data);
}


