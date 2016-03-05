<?php

$connection = new \Nette\Database\Connection(
    'mysql:host=127.0.0.1;dbname=demo.mesour.com', 'root', 'root'
);
$cacheMemoryStorage = new \Nette\Caching\Storages\MemoryStorage;

$structure = new \Nette\Database\Structure($this->connection, $cacheMemoryStorage);
$conventions = new \Nette\Database\Conventions\DiscoveredConventions($structure);
$context = new \Nette\Database\Context($this->connection, $structure, $conventions, $cacheMemoryStorage);

$source = new \Mesour\DataGrid\Sources\NetteDbGridSource($context->table('user'));

return $source;