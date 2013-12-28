<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine;

use Doctrine\Tests\Mocks\EntityManagerMock;
use Doctrine\Tests\ORM\Tools\Pagination\Person;
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

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('parent' => 'value'));
}, 'Elerium\Doctrine\MappingException', "Only array or entity can be mapped into association 'parent', string given.");

$mapper->setValues(new Entity\Person, array('parent' => array()));

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('parent' => new ArrayCollection));
}, 'Elerium\Doctrine\MappingException', "Only array or entity can be mapped into association 'parent', object given.");

$mapper->setValues(new Entity\Person, array('parent' => NULL));
$mapper->setValues(new Entity\Person, array('parent' => new Entity\Person));

// --------------- Etalon ---------------

$john = new Entity\Person(NULL, 'John');
$jane = new Entity\Person(NULL, 'Jane');
$bill = new Entity\Person(NULL, 'Bill');
$john->parent = $jane;
$jane->parent = $bill;

Assert::equal($john, $mapper->setValues(new Entity\Person(NULL, 'John'), array('parent' => array('name' => 'Jane', 'parent' => new Entity\Person(NULL, 'Bill')))));

// --------------- Etalon ---------------

$john = new Entity\Person(NULL, 'John');
$john->parent = new Entity\Person(NULL, 'Jane');

// ---------- Default entity -----------

$entity = new Entity\Person();
$entity->parent = new Entity\Person(NULL, 'Jane');

Assert::equal($john, $mapper->setValues($entity, array('name' => 'John')));