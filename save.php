<?php


define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../config.php');

$data = new \stdClass();
if (!isset($USER->id) || empty($USER->id)) {
    return;
}
$data->user_userid = $USER->id;

if (!isset($_POST['questionid']) || empty($_POST['questionid'])) {
    return;
}
$data->question_id = intval($_POST['questionid']);

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

function save_vote($data) {
    global $DB, $USER;

    if (!isset($_POST['rate']) || empty($_POST['rate'])) {
        return;
    }
    $data->studentquiz_vote_point = intval($_POST['rate']);

    $row = $DB->get_record('studentquiz_vote', array('user_userid' => $USER->id, 'question_id' => $data->question_id));
    if ($row === false) {
        $DB->insert_record('studentquiz_vote', $data);
    } else {
        $row->studentquiz_vote_point = $data->studentquiz_vote_point;
        $var = $DB->update_record('studentquiz_vote', $row);
    }
}

function save_comment($data) {
    global $DB;

    if (!isset($_POST['text']) || empty($_POST['text'])) {
        return;
    }

    // prevent XSS
    $data->studentquiz_comment_text = htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8');
    $data->studentquiz_comment_created = usertime(time(), usertimezone());

    $DB->insert_record('studentquiz_comment', $data);
}


