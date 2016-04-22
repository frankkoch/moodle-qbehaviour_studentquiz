<?php
    defined('MOODLE_INTERNAL') || die();
    $plugin->component = 'qbehaviour_studentquiz';
    $plugin->version      = 2016041800;
    $plugin->release      = 'v1.0.0';
    $plugin->requires     = 2015051100; // 2.9
    $plugin->dependencies = array(
        'qbehaviour_immediatefeedback' => 2015111600,
        'mod_studentquiz' => ANY_VERSION
    );
    $plugin->maturity     = MATURITY_STABLE;



