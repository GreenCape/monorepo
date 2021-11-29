<?php
$basedir = dirname(__DIR__);

require_once $basedir . '/vendor/autoload.php';

if (file_exists("$basedir/tests/repos")) {
    shell_exec("rm -rf $basedir/tests/repos");
}
