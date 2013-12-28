<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine;

use Elerium;
use EleriumTests\Mocks;
use Tester\Assert;
use Elerium\Doctrine,
	Doctrine\Common\Collections\ArrayCollection;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../Mocks/EntityManager.php';
require_once __DIR__ . '/entities.php';

$entityManager = new Mocks\EntityManager;
$mapper = new Doctrine\EntityMapper($entityManager->getMetadataFactory());

// --------------- Etalon ---------------

$expected = array(
	'id' => NULL,
	'name' => 'John',
	'parent' => NULL,
	'childrens' => array(
		array(
			'id' => NULL,
			'name' => 'Jane',
			'parent' => NULL,
			'childrens' => array(
				array(
					'id' => NULL,
					'name' => 'James',
					'parent' => NULL,
					'childrens' => NULL
				),
				array(
					'id' => NULL,
					'name' => 'Jeff',
					'parent' => NULL,
					'childrens' => NULL
				)
			)
		)
	)
);

// --------------- Entity ---------------

$john = new Entity\Person(NULL, 'John');
$jane = new Entity\Person(NULL, 'Jane');
$john->childrens = new ArrayCollection(array(
	$jane
));
$jane->childrens = new ArrayCollection(array(
	new Entity\Person(NULL, 'James'),
	new Entity\Person(NULL, 'Jeff')
));

Assert::equal($expected, $mapper->getValues($john));
