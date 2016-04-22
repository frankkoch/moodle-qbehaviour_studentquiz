<?php

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/check_permission.php');

function comment_renderer($question_id) {
    global $DB;
    $mod_name = 'qbehaviour_studentquiz';

    $comments = $DB->get_records(
        'studentquiz_comment', array('question_id' => $question_id),
        'studentquiz_comment_id DESC'
    );

    if (empty($comments)) {
        return html_writer::div(get_string('no_comments', $mod_name));
    }

    $html = '';
    $index = 0;
    foreach ($comments as $comment) {
        $hide = '';
        if ($index > 1) {
            $hide = 'hidden';
        }
        $date = date('d.m.Y H:i', $comment->studentquiz_comment_created);
        $user = $DB->get_record('user', array('id' => $comment->user_userid));
        $username = ($user !== false? $user->username: '');
        $html .= html_writer::div(
            (check_created_permission()?
                html_writer::span('remove', 'remove_action',
                array(
                    'data-id' => $comment->studentquiz_comment_id,
                    'data-question_id' => $question_id
                )):
                '')
                . html_writer::tag('p', $date . ' | ' . $username)
                . html_writer::tag('p', $comment->studentquiz_comment_text),
                $hide
            );

        ++$index;
    }

    if (count($comments) > 2) {
        $html .= html_writer::div(
            html_writer::tag('button', get_string('show_more', $mod_name), array('type' => 'button', 'class' => 'show_more'))
            . html_writer::tag('button', get_string('show_less', $mod_name), array('type' => 'button', 'class' => 'show_less hidden')),
            'button_controls'
        );
    }

    return $html;
}
