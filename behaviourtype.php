<?php

defined('MOODLE_INTERNAL') || die();

class qbehaviour_studentquiz_type extends question_behaviour_type  {
    public function is_archetypal() {
        return true;
    }

    public function can_questions_finish_during_the_attempt() {
        return true;
    }
}
