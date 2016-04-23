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

require_once(dirname(__FILE__) . '/../immediatefeedback/behaviour.php');


class qbehaviour_studentquiz extends qbehaviour_immediatefeedback {
    const IS_ARCHETYPAL = true;

    public function __construct(question_attempt $qa, $preferredbehaviour)
    {
        global $PAGE;
        $PAGE->requires->js('/question/behaviour/studentquiz/jquery-1.12.3.min.js',true);
        $PAGE->requires->js('/question/behaviour/studentquiz/studentquiz.js', true);
        parent::__construct($qa, $preferredbehaviour);
    }
}
