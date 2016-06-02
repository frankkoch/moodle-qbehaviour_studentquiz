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

        // Add jQuery and studentquiz frontend logic everything else didnt work!
        $PAGE->requires->js('/question/behaviour/studentquiz/jquery-1.12.3.min.js', true);
        $PAGE->requires->js('/question/behaviour/studentquiz/studentquiz.js', true);
        parent::__construct($qa, $preferredbehaviour);
    }

    /**
     * process save
     * @param question_attempt_pending_step $pendingstep
     * @return bool
     * @throws coding_exception
     */
    public function process_save(question_attempt_pending_step $pendingstep) {
        $status = question_behaviour_with_save::process_save($pendingstep);
        return $status;
    }

    /**
     * get the display state string
     * @param bool $showcorrectness
     * @return string
     * @throws coding_exception
     */
    public function get_state_string($showcorrectness) {
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
