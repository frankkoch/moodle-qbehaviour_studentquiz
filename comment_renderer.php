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
 * TODO
 *
 * @package    mod_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/check_permission.php');

function comment_renderer($question_id) {
    global $DB;
    $mod_name = 'qbehaviour_studentquiz';

    $comments = $DB->get_records(
        'studentquiz_comment', array('questionid' => $question_id),
        'id DESC'
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
        $date = date('d.m.Y H:i', $comment->created);
        $user = $DB->get_record('user', array('id' => $comment->userid));
        $username = ($user !== false? $user->username: '');
        $html .= html_writer::div(
            (check_created_permission()?
                html_writer::span('remove', 'remove_action',
                array(
                    'data-id' => $comment->id,
                    'data-question_id' => $comment->questionid
                )):
                '')
                . html_writer::tag('p', $date . ' | ' . $username)
                . html_writer::tag('p', $comment->comment),
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
