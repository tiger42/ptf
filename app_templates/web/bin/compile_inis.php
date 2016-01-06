#!/usr/bin/php
<?php

namespace _NAMESPACE_;

define('SRC_DIR', 'src');
define('CONFIG_DIR', SRC_DIR . '/App/Config');

require_once SRC_DIR . '/Application.php';

Application::compileConfig('config.ini.php', CONFIG_DIR, '_NAMESPACE_');
