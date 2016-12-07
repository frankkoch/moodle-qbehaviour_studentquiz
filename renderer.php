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
 * Defines the renderer StudentQuiz class for question behaviours.
 *
 * @package    qbehaviour_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

/**
 * Renderer class studentquix for question behaviours with rating and commenting.
 *
 * Coordinates rendering rating and commenting in additional to default.
 *
 * @package    qbehaviour_studentquiz
 * @copyright  2016 HSR (http://www.hsr.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_studentquiz_renderer extends qbehaviour_renderer {

    /**
     * Generate some HTML to display comment list
     *
     * @param  int $questionid Question id
     * @return string HTML fragment
     */
    public function comment_list($questionid) {
        return qbehaviour_studentquiz_comment_renderer($questionid);
    }

    /**
     * Add submit button to controls
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function controls(question_attempt $qa, question_display_options $options) {
        return $this->submit_button($qa, $options);
    }


    /**
     * Generate some HTML (which may be blank) that appears in the outcome area,
     * after the question-type generated output.
     *
     * For example, the CBM models use this to display an explanation of the score
     * adjustment that was made based on the certainty selected.
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        if ($options->feedback) {
            global $CFG;
            return html_writer::end_tag('div')
                . html_writer::end_tag('div')
                . html_writer::div(
                    $this->render_vote($qa->get_question()->id)
                    . $this->render_comment($qa->get_question()->id), 'studentquiz_behaviour')
                    . html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'baseurlmoodle'
            , 'id' => 'baseurlmoodle', 'value' => $CFG->wwwroot))
                . html_writer::start_div('none')
                . html_writer::start_div('none');
        }

        return '';
    }

    /**
     * Generate some HTML to display rating options
     *
     * @param  int $questionid Question id
     * @param  boolean $selected shows the selected vote
     * @param  boolean $readonly describes if rating is readonly
     * @return string HTML fragment
     */
    protected function vote_choices($questionid, $selected, $readonly) {
        $attributes = array(
            'type' => 'radio',
            'name' => 'q' . $questionid,
        );

        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $selected = intval($selected);

        $rateable = '';
        if (!$readonly) {
            $rateable = 'rateable ';
        }

        $choices = '';
        $votes = [5, 4, 3, 2, 1];
        foreach ($votes as $vote) {
            $class = 'star-empty';
            if ($vote <= $selected) {
                $class = 'star';
            }
            $choices .= html_writer::span('', $rateable . $class, array('data-rate' => $vote, 'data-questionid' => $questionid));
        }
        return get_string('vote_title', 'qbehaviour_studentquiz')
            . $this->output->help_icon('vote_help', 'qbehaviour_studentquiz') . ': '
            . html_writer::div($choices, 'rating')
            . html_writer::div(get_string('vote_error', 'qbehaviour_studentquiz'), 'hide error');
    }

    /**
     * Generate some HTML to display comment form for add comment
     *
     * @param  int $questionid Question id
     * @return string HTML fragment
     */
    protected function comment_form($questionid) {
        return html_writer::tag('p', get_string('add_comment', 'qbehaviour_studentquiz')
            . $this->output->help_icon('comment_help', 'qbehaviour_studentquiz') . ':')
            . html_writer::tag('p', html_writer::tag(
                'textarea', '',
                 array('class' => 'add_comment_field', 'name' => 'q' . $questionid)))
            . html_writer::tag('p', html_writer::tag(
                'button',
                get_string('add_comment', 'qbehaviour_studentquiz'),
                array('type' => 'button', 'class' => 'add_comment'))
            );
    }

    /**
     * Generate some HTML to display rating
     *
     * @param  int $questionid Question id
     * @return string HTML fragment
     */
    protected function render_vote($questionid) {
        global $DB, $USER;

        $value = -1; $readonly = false;
        $vote = $DB->get_record('studentquiz_vote', array('questionid' => $questionid, 'userid' => $USER->id));
        if ($vote !== false) {
            $value = $vote->vote;
            $readonly = true;
        }

        return html_writer::div($this->vote_choices($questionid, $value , $readonly), 'vote');
    }

    /**
     * Generate some HTML to display the complete comment fragment
     *
     * @param  int $questionid Question id
     * @return string HTML fragment
     */
    protected function render_comment($questionid) {
        return html_writer::div(
            $this->comment_form($questionid)
                . html_writer::div($this->comment_list($questionid), 'comment_list'), 'comments');
    }
}
