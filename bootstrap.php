<?php
namespace Engine;
use Engine;

define('IN_APP', true);
define('APP_ROOT', str_replace("\\", '/', dirname(__FILE__)));
$config = require APP_ROOT . '/config/Config.php';
date_default_timezone_set($config['setting']['master']['timezone']);
require 'vendor/autoload.php';
require 'vendor/bin/resque';