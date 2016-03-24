<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}
@mkdir(__DIR__ . "/log");
@mkdir(__DIR__ . "/tmp");

define("TEMP_DIR", __DIR__ . "/tmp/");

Tester\Helpers::purge(TEMP_DIR);

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(__DIR__ . '/../src');
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(TEMP_DIR));
$loader->register();

Tester\Environment::setup();

function pdump($val)
{
	Tracy\Debugger::$productionMode = false;
	call_user_func_array('dump', func_get_args());
}