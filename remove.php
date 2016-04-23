<?php


define('AJAX_SCRIPT', true);
define('MOODLE_INTERNAL', true);

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/check_permission.php');


if (!isset($_POST['id']) || empty($_POST['id'])) {
    return http_response_code(404);
}

header('Content-Type: text/html; charset=utf-8');

if (check_created_permission()) {
    $DB->delete_records('studentquiz_comment', array('id' => intval($_POST['id'])));
} else {
    return http_response_code(401);
}
