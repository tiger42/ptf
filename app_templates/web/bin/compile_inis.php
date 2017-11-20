#!/usr/bin/php
<?php

namespace _NAMESPACE_;

define('SRC_DIR', 'src');
define('CONFIG_DIR', SRC_DIR . '/App/Config');

require_once SRC_DIR . '/Application.php';

// Use parameter "-c" or "--clear" to remove all files from config directory before compiling
$options = getopt('c', ['clear']);
if (isset($options['c']) || isset($options['clear'])) {
    array_map('unlink', glob(CONFIG_DIR . '/*.php'));
}

Application::compileConfig('config.ini.php', CONFIG_DIR, '_NAMESPACE_');
