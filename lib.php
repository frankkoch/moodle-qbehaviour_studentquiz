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
 * Studentquiz qbehaviour lib
 *
 * @package    qbehaviour_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../config.php');

/**
 * Check permission if is no student
 *
 * @return boolean the current user is not a student
 */
function check_created_permission() {
    global $USER;

    $admins = get_admins();
    foreach ($admins as $admin) {
        if ($USER->id == $admin->id) {
            return true;
        }
    }

    if (!user_has_role_assignment($USER->id, 5)) {
        return true;
    }

    return false;
}

/**
 * Generate some HTML to render comments
 *
 * @param  int $questionid Question id
 * @return string HTML fragment
 */
function comment_renderer($questionid) {
    global $DB;
    $modname = 'qbehaviour_studentquiz';

    $comments = $DB->get_records(
        'studentquiz_comment', array('questionid' => $questionid),
        'id DESC'
    );

    if (empty($comments)) {
        return html_writer::div(get_string('no_comments', $modname));
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
        $username = ($user !== false ? $user->username : '');
        $html .= html_writer::div(
            (check_created_permission() ? html_writer::span('remove', 'remove_action',
                array(
                    'data-id' => $comment->id,
                    'data-question_id' => $comment->questionid
                )) : '')
                . html_writer::tag('p', $date . ' | ' . $username)
                . html_writer::tag('p', $comment->comment),
                $hide
            );

        ++$index;
    }

    if (count($comments) > 2) {
        $html .= html_writer::div(
            html_writer::tag('button', get_string('show_more', $modname), array('type' => 'button', 'class' => 'show_more'))
            . html_writer::tag('button', get_string('show_less', $modname)
                , array('type' => 'button', 'class' => 'show_less hidden')), 'button_controls'
        );
    }

    return $html;
}