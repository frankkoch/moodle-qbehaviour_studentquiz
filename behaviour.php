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
 * Question behaviour where the student can submit questions one at a time for immediate feedback ..
 *
 * with additional functionality to rate and
 * comment the questions.
 *
 * @package    qbehaviour_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../immediatefeedback/behaviour.php');
require_once(dirname(__FILE__) . '/gradecalc.php');

/**
 * Question behaviour for immediate feedback with voting and commenting questions.
 *
 * After the student submit his answer, he have to rate the question, may write
 * a comment or read the discousion around the question.
 *
 * Everything else match the immediate feedback behaviour.
 *
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_studentquiz extends qbehaviour_immediatefeedback {

    /**
     * qbehaviour_studentquiz constructor.
     * @param question_attempt $qa
     * @param string $preferredbehaviour
     */
    public function __construct(question_attempt $qa, $preferredbehaviour) {
        global $PAGE;

        $PAGE->requires->js_call_amd('qbehaviour_studentquiz/studentquiz', 'initialise');
        parent::__construct($qa, $preferredbehaviour);
    }

    /**
     * Process save
     * @param question_attempt_pending_step $pendingstep
     * @return bool
     * @throws coding_exception
     */
    public function process_save(question_attempt_pending_step $pendingstep) {
        $status = question_behaviour_with_save::process_save($pendingstep);
        return $status;
    }

    /**
     * Get the display state string
     * @param bool $showcorrectness
     * @return string
     * @throws coding_exception
     */
    public function get_state_string($showcorrectness) {
        global $USER, $quiz;
        if ( !defined('GRADE_CALCULATED') && strpos($_SERVER['REQUEST_URI'], 'quiz/review.php') > 0 && $quiz != null) {
            get_user_quiz_grade($USER->id, $quiz);
            define('GRADE_CALCULATED', true);
        }

        switch($this->qa->get_state()) {
            case question_state::$gradedpartial:
            case question_state::$gradedright:
            case question_state::$invalid:
            case question_state::$gradedwrong:
                return get_string('answeredandmodified', 'qbehaviour_studentquiz');
            case question_state::$complete:
                return get_string('answered', 'qbehaviour_studentquiz');
                break;
            case question_state::$todo:
                return get_string('notyetanswered', 'qbehaviour_studentquiz');
            default:
                return $this->qa->get_state()->default_string($showcorrectness);
        }
    }

}
