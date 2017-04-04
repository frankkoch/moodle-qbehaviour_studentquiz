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
 * Serves with Methods to calculate the Grade of a StudentQuiz.
 *
 * Created by PhpStorm.
 * User: Galaxus
 * Date: 25.11.2016
 * Time: 13:54
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot . '/mod/studentquiz/lib.php');

/**
 * This Method updates the grade of the current User and StudentQuiz
 *
 * @param $userid
 * @param $quiz stdClass default quiz-Object
 * @return bool | int result if sucksessfully updated grades
 */
function get_user_quiz_grade($userid, $quiz) {
    global $DB;
    $sql = 'select sp.studentquizcoursemodule as cmid, sq.id as sqid '
        .'from {course_modules} cm '
        .'left join {studentquiz_practice} sp on sp.quizcoursemodule = cm.id '
        .'left join {studentquiz} sq on sq.coursemodule = sp.studentquizcoursemodule '
        .'WHERE cm.instance = :quid AND sp.userid = :userid';

    $squizdata = $DB->get_record_sql($sql, array('quid' => $quiz->id, 'userid' => $userid));
    if ($squizdata->cmid != null) {
        $sql = 'select sq.*, cm.idnumber as cmidnumber, m.name as modname '
            .'from {studentquiz} sq '
            .'  left join {modules} m on m.name = \'studentquiz\' '
            .'  left join {course_modules} cm on cm.instance = sq.coursemodule '
            .'                                     and cm.course = sq.course '
            .'                                     and cm.module = m.id '
            .'where sq.coursemodule = :cmid and m.visible = 1 ';
        $studentquiz = $DB->get_record_sql($sql, array('cmid' => $squizdata->cmid));

        $sql = 'select round(sum(sub.maxmark), 1) as usermaxmark, round(sum(sub.mark), 1) usermark, '
            .'  (SELECT round(sum(q.defaultmark), 1) '
            .'     FROM {question} q '
            .'       LEFT JOIN {question_categories} qc ON q.category = qc.id '
            .'       LEFT JOIN {context} c ON qc.contextid = c.id '
            .'     WHERE c.instanceid = :cmid AND c.contextlevel = 70) as stuquizmaxmark '
            .'from ( '
            .'    SELECT suatt.id, suatt.questionid, questionattemptid, max(fraction) as fraction, suatt.maxmark,  '
            .'max(fraction) * suatt.maxmark as mark '
            .'from {question_attempt_steps} suats '
            .'  left JOIN {question_attempts} suatt on suats.questionattemptid = suatt.id '
            .'WHERE state in (\'gradedright\', \'gradedpartial\') '
            .'        AND userid = :userid AND suatt.questionid IN (SELECT q.id '
            .'                                            FROM {question} q '
            .'                                              LEFT JOIN {question_categories} qc ON q.category = qc.id '
            .'                                              LEFT JOIN {context} c ON qc.contextid = c.id '
            .'                                            WHERE c.instanceid = :cmid2 AND c.contextlevel = 70) '
            .'GROUP BY suatt.questionid, suatt.id, suats.questionattemptid) as sub ';

        $grades = $DB->get_record_sql($sql, array(
            'cmid' => $squizdata->cmid, 'cmid2' => $squizdata->cmid,
            'userid' => $userid));
        $newgrade = array(
            'userid' => $userid,
            'rawgrade' => $grades->usermark);
        $res = studentquiz_grade_item_update($studentquiz, $newgrade);
        return $res;
    }
    return false;

}


