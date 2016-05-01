<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;

$paths = [__DIR__ . "/../tests/Entity"];

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;

$config = Setup::createConfiguration($isDevMode);

$driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new AnnotationReader(), $paths);
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);

// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
if (!isset($conn)) {
	$conn = [
		'driver' => 'pdo_mysql',
		'user' => 'root',
		'password' => 'root',
		'dbname' => 'mesour_editable',
	];
}

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

$entityManager->getConfiguration()
	->addCustomDatetimeFunction('DATE', \Mesour\Filter\Sources\DateFunction::class);

$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
\Doctrine\DBAL\Types\Type::addType('enum', \Doctrine\DBAL\Types\StringType::class);

require_once __DIR__ . '/../../vendor/mesour/sources/tests/Entity/User.php';
require_once __DIR__ . '/../../vendor/mesour/sources/tests/Entity/Group.php';
require_once __DIR__ . '/../../vendor/mesour/sources/tests/Entity/UserAddress.php';
require_once __DIR__ . '/../../vendor/mesour/sources/tests/Entity/Company.php';
require_once __DIR__ . '/../../vendor/mesour/sources/tests/Entity/Wallet.php';

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(
	[
		'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
		'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager),
	]
);

$qb = $entityManager->createQueryBuilder();
$qb = $entityManager->createQueryBuilder()
	->select('u')
	->from(Mesour\Sources\Tests\Entity\User::class, 'u')
	->join(\Mesour\Sources\Tests\Entity\Group::class, 'g', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.group = g.id');

$source = new \Mesour\DataGrid\Sources\DoctrineGridSource(
	Mesour\Sources\Tests\Entity\User::class,
	'id',
	$qb,
	[
		'id' => 'u.id',
		'group_id' => 'u.groups',
		'last_login' => 'u.lastLogin',
		'group' => 'g',
	]
);

return $source;