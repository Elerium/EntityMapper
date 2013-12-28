<?php

namespace EleriumTests\Mocks;

use Doctrine\DBAL\Connection,
	Doctrine\ORM\Configuration,
	Doctrine\Common\EventManager,
	Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/EntityRepository.php';

class EntityManager extends \Doctrine\ORM\EntityManager
{

	public function __construct(Connection $conn = NULL, Configuration $config = NULL, EventManager $eventManager = NULL)
	{
		if($config == NULL)
		{
			$config = new Configuration;
			$config->setProxyDir(__DIR__ . '/Proxies');
			$config->setProxyNamespace('EleriumTests\Proxies');
			$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(array(), true));
		}

		if($eventManager == NULL)
		{
			$eventManager = new EventManager;
		}

		if($conn == NULL)
		{
			$conn = DriverManager::getConnection(array('driver' => 'pdo_mysql', 'user' => 'root', 'password' => '', 'host' => 'localhost', 'dbname' => 'db'), $config, $eventManager);
		}

		return parent::__construct($conn, $config, $eventManager);
	}

	/**
	 * @param string $entityName
	 * @return \EleriumTests\Mocks\EntityRepository
	 */
	public function getRepository($entityName)
	{
		return new EntityRepository($this, $this->getClassMetadata($entityName));
	}

	/**
	 * @param object $entity
	 * @return NULL
	 */
	public function persist($entity)
	{
		return NULL;
	}

	/**
	 * @param object|NULL $entity
	 * @return NULL
	 */
	public function flush($entity = NULL)
	{
		return NULL;
	}
}