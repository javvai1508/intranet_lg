<?php
ini_set('session.save_path', sys_get_temp_dir());
require __DIR__ . '/../vendor/autoload.php';
$configurator = new Nette\Bootstrap\Configurator;
$configurator->setDebugMode(true);
$configurator->setTempDirectory(sys_get_temp_dir() . '/intranet-lg-tests');
$configurator->addParameters(['appDir' => realpath(__DIR__ . '/../app')]);
$configurator->addConfig(__DIR__ . '/../app/config/config.neon');
$configurator->addConfig(['database' => ['dsn' => 'sqlite::memory:']]);
return $configurator->createContainer();
