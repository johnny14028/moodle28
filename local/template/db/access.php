<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/template:templatecapability' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'local/template:create' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'teacher' => CAP_ALLOW
        ),
    ),
    'local/template:control' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'teacher' => CAP_ALLOW
        ),
    )    
);
