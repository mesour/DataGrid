<?php

$connection = new \Nette\Database\Connection(
    'mysql:host=127.0.0.1;dbname=sources_test', 'root', 'root'
);
$cacheMemoryStorage = new \Nette\Caching\Storages\MemoryStorage;

$structure = new \Nette\Database\Structure($connection, $cacheMemoryStorage);
$conventions = new \Nette\Database\Conventions\DiscoveredConventions($structure);
$context = new \Nette\Database\Context($connection, $structure, $conventions, $cacheMemoryStorage);

$selection = $context->table('users')
	->select('users.*')
	->select('group.name group_name');

$source = new \Mesour\DataGrid\Sources\NetteDbGridSource(
	'users',
	'id',
	$selection,
	$context,
	[
		'group_name' => 'g.name'
	]
);

return $source;