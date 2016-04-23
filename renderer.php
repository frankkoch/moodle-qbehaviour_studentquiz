<?php

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/comment_renderer.php');

class qbehaviour_studentquiz_renderer extends qbehaviour_renderer {

    protected function comment_form($question_id) {
        return html_writer::tag('p', get_string('add_comment', 'qbehaviour_studentquiz') . $this->output->help_icon('comment_help', 'qbehaviour_studentquiz') . ':')
            . html_writer::tag('p', html_writer::tag(
                'textarea', '',
                 array('class' => 'add_comment_field', 'name' => 'q' . $question_id)))
            . html_writer::tag('p', html_writer::tag(
                'button',
                get_string('add_comment', 'qbehaviour_studentquiz'),
                array('type' => 'button', 'class' => 'add_comment'))
        );
    }

    public function comment_list($question_id) {
        return comment_renderer($question_id);
    }

    protected function vote_choices($question_id, $selected, $readonly) {
        $attributes = array(
            'type' => 'radio',
            'name' => 'q' . $question_id,
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
        $votes = [5,4,3,2,1];
        foreach ($votes as $vote) {
            $class = 'star-empty';
            if ($vote <= $selected) {
                $class = 'star';
            }
            $choices .= html_writer::span('', $rateable . $class, array('data-rate' => $vote, 'data-questionid' => $question_id));
        }
        return get_string('vote_title', 'qbehaviour_studentquiz')
            . $this->output->help_icon('vote_help', 'qbehaviour_studentquiz') . ': ' 
            . html_writer::div($choices, 'rating');
    }

    public function controls(question_attempt $qa, question_display_options $options) {
       $this->submit_button($qa, $options);

        return $this->submit_button($qa, $options);
    }

    public function mark_summary(question_attempt $qa, core_question_renderer $qoutput,
                                 question_display_options $options) {

        $output = parent::mark_summary($qa, $qoutput, $options);
        return $output;
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
            return html_writer::end_tag('div')
                . html_writer::end_tag('div')
                . html_writer::div(
                    $this->render_vote($qa->get_question()->id)
                        . $this->render_comment($qa->get_question()->id), 'studentquiz_behaviour')
                . html_writer::start_div('none')
                . html_writer::start_div('none');
        }

        return '';
    }

    protected function render_vote($question_id) {
        global $DB, $USER;

        $value = -1; $readonly = false;
        $vote = $DB->get_record('studentquiz_vote', array('question_id' => $question_id, 'user_userid' => $USER->id));
        if ($vote !== false) {
            $value = $vote->studentquiz_vote_point;
            $readonly = true;
        }

        return html_writer::div($this->vote_choices($question_id, $value , $readonly), 'vote');
    }

    protected function render_comment($question_id) {
        return html_writer::div(
            $this->comment_form($question_id)
                . html_writer::div($this->comment_list($question_id), 'comment_list'), 'comments');
    }
}