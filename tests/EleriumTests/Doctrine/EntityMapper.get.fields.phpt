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

Assert::equal(array('id' => 1, 'string' => 'Rododendron'), $mapper->getValues(new Entity\Simple(1, 'Rododendron')));