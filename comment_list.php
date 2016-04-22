<?php


define('AJAX_SCRIPT', true);
define('MOODLE_INTERNAL', true);

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/comment_renderer.php');


if (!isset($_GET['question_id']) || empty($_GET['question_id'])) {
    return http_response_code(404);
}

header('Content-Type: text/html; charset=utf-8');

echo comment_renderer(intval($_GET['question_id']));
