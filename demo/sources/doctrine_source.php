<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;

$paths = array(__DIR__ . "/../tests/Entity");

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
if(!isset($conn)) {
    $conn = array(
        'driver'   => 'pdo_mysql',
        'user'     => 'root',
        'password' => 'root',
        'dbname'   => 'sources_test',
    );
}

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

$entityManager->getConfiguration()
    ->addCustomDatetimeFunction('DATE', \Mesour\Filter\Sources\DateFunction::class);

require_once '../vendor/mesour/sources/tests/Entity/User.php';
require_once '../vendor/mesour/sources/tests/Entity/Groups.php';

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));

$qb = $entityManager->createQueryBuilder();
$qb
    ->select('u')
    ->from('Mesour\Sources\Tests\Entity\User', 'u')
;

$source = new \Mesour\DataGrid\Sources\DoctrineGridSource($qb, [
    'userId' => 'u.userId',
    'groupName' => 'gr.name',
]);

$source->setPrimaryKey('userId');

return $source;