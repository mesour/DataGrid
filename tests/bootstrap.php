<?php

require __DIR__ . "/../vendor/autoload.php";

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

@mkdir(__DIR__ . "/log");
@mkdir(__DIR__ . "/tmp");

define("TEMP_DIR", __DIR__ . "/tmp/");

Tester\Helpers::purge(TEMP_DIR);

$configurator = new Nette\Configurator;

$configurator->enableDebugger(__DIR__ . "/log");
$configurator->setDebugMode(FALSE);
$configurator->setTempDirectory(TEMP_DIR);
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/../src')
    ->addDirectory(__DIR__ . '/classes')
    ->register();

$configurator->addConfig(__DIR__ . '/config.neon');

$container = $configurator->createContainer();

Tester\Environment::setup();

function id($val) {
	return $val;
}

return $container;