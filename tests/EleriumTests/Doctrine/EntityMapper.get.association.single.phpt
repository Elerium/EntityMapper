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

// --------------- Etalon ---------------

$expected = array(
	'id' => NULL,
	'name' => 'John',
	'parent' => array(
		'id' => NULL,
		'name' => 'Jane',
		'parent' => array(
			'id' => NULL,
			'name' => 'Bill',
			'parent' => NULL,
			'childrens' => NULL
		),
		'childrens' => NULL
	),
	'childrens' => NULL
);

// --------------- Entity ---------------

$john = new Entity\Person(NULL, 'John');
$jane = new Entity\Person(NULL, 'Jane');
$john->parent = $jane;
$bill = new Entity\Person(NULL, 'Bill');
$jane->parent = $bill;

Assert::equal($expected, $mapper->getValues($john));

