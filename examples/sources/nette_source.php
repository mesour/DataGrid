<?php

$connection = new \Nette\Database\Connection(
	'mysql:host=127.0.0.1;dbname=mesour_editable', 'root', 'root'
);
$cacheMemoryStorage = new \Nette\Caching\Storages\FileStorage(__DIR__ . '/../temp');

$structure = new \Nette\Database\Structure($connection, $cacheMemoryStorage);
$conventions = new \Nette\Database\Conventions\DiscoveredConventions($structure);
$context = new \Nette\Database\Context($connection, $structure, $conventions, $cacheMemoryStorage);

\Tracy\Debugger::getBar()->addPanel(new \Nette\Bridges\DatabaseTracy\ConnectionPanel($connection));

$selection = $context->table('users')
	->select('users.*')
	->select('group.name group_name');

$source = new \Mesour\DataGrid\Sources\NetteDbGridSource(
	'users',
	'id',
	$selection,
	$context,
	[
		'group_name' => 'g.name',
		'group' => 'g.id',
		'wallet' => 'wallet.id',
	]
);

return $source;