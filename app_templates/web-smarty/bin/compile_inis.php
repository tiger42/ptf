#!/usr/bin/php
<?php

namespace _NAMESPACE_;

define('SRC_DIR', 'src');
define('CONFIG_DIR', SRC_DIR . '/App/Config');

require_once SRC_DIR . '/Application.php';

// Use parameter "-c" or "--clear" to remove all files from config directory before compiling
if (in_array('-c', $_SERVER['argv']) || in_array('--clear', $_SERVER['argv'])) {
    array_map('unlink', glob(CONFIG_DIR . '/*.php'));
}

Application::compileConfig('config.ini.php', CONFIG_DIR, '_NAMESPACE_');
