<?php

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../immediatefeedback/behaviour.php');


class qbehaviour_studentquiz extends qbehaviour_immediatefeedback {
    const IS_ARCHETYPAL = true;

    public function __construct(question_attempt $qa, $preferredbehaviour)
    {
        global $PAGE;
        $PAGE->requires->jquery();
        $PAGE->requires->js('/question/behaviour/studentquiz/studentquiz.js', true);
        parent::__construct($qa, $preferredbehaviour);
    }
}
