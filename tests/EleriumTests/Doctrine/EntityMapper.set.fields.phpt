<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine;

use Elerium;
use EleriumTests\Mocks;
use Tester\Assert;
use Elerium\Doctrine;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Mocks/EntityManager.php';
require_once __DIR__ . '/entities.php';

$entityManager = new Mocks\EntityManager;
$mapper = new Doctrine\EntityMapper($entityManager->getMetadataFactory());

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Simple, array('field' => TRUE));
}, 'Elerium\Doctrine\MappingException', "Field 'field' in entity 'EleriumTests\\Doctrine\\Entity\\Simple' does not exist.");

Assert::equal(new Entity\Simple(2, 'Rododendron'), $mapper->setValues(new Entity\Simple(2), array('string' => 'Rododendron')));