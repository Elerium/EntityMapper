<?php

use Nette\Diagnostics\Debugger,
	Nette\Loaders\RobotLoader,
	Nette\Caching\Storages\FileStorage;


if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

define('LOG_DIR', __DIR__ . '/log');
define('TEMP_DIR', __DIR__ . '/temp');
define('MOCKS_DIR', __DIR__ . '/Mocks');
define('ELERIUM_DIR', __DIR__ . '/../Elerium');

Debugger::$consoleMode = TRUE;
Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT, LOG_DIR);

$loader = new RobotLoader;
$loader->addDirectory(ELERIUM_DIR);
$loader->setCacheStorage(new FileStorage(TEMP_DIR));
$loader->register();