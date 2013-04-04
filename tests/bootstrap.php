<?php

if (!($omekaDir = getenv('OMEKA_DIR'))) {
    $omekaDir = dirname(dirname(dirname(dirname(__FILE__))));
}
if (!defined('NEATLINE_FEATURES_PLUGIN_DIR')) {
    define(
        'NEATLINE_FEATURES_PLUGIN_DIR',
        '..'
    );
}

require_once $omekaDir . '/application/tests/bootstrap.php';
require_once 'NeatlineFeatures_Test.php';

