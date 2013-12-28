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

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('childrens' => 'value'));
}, 'Elerium\Doctrine\MappingException', "Only array or collection can be mapped into association 'childrens', string given.");

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('childrens' => new Entity\Person));
}, 'Elerium\Doctrine\MappingException', "Only array or collection can be mapped into association 'childrens', object given.");

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('childrens' => array('value')));
}, 'Elerium\Doctrine\MappingException', "Values for collection association 'childrens' can only contains entities or arrays, string given.");

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('childrens' => array(NULL)));
}, 'Elerium\Doctrine\MappingException', "Values for collection association 'childrens' can only contains entities or arrays, NULL given.");

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('childrens' => new ArrayCollection(array('value'))));
}, 'Elerium\Doctrine\MappingException', "Values for collection association 'childrens' can only contains entities or arrays, string given.");

Assert::throws(function() use ($mapper) {
	$mapper->setValues(new Entity\Person, array('childrens' => new ArrayCollection(array(NULL))));
}, 'Elerium\Doctrine\MappingException', "Values for collection association 'childrens' can only contains entities or arrays, NULL given.");

$mapper->setValues(new Entity\Person, array('childrens' => NULL));
$mapper->setValues(new Entity\Person, array('childrens' => array()));
$mapper->setValues(new Entity\Person, array('childrens' => new ArrayCollection));

// --------------- Etalon ---------------

$john = new Entity\Person(NULL, 'John');
$jane = new Entity\Person(NULL, 'Jane');
$kevin = new Entity\Person(NULL, 'Kevin');
$steve = new Entity\Person(NULL, 'Steve');
$john->childrens = new ArrayCollection(array(
	$jane,
	$steve
));
$jane->childrens = new ArrayCollection(array(
	new Entity\Person(NULL, 'James'),
	new Entity\Person(NULL, 'Jeff')
));
$steve->childrens = new ArrayCollection(array(
new Entity\Person(NULL, 'John')
));

// -------------- Changes --------------

$values = array(
	'childrens' => array(
		array(
			'name' => 'Jane',
			'childrens' => new ArrayCollection(
				array(
					new Entity\Person(NULL, 'James'),
					new Entity\Person(NULL, 'Jeff')
				)
			)
		),
		array(
			'name' => 'Steve',
			'childrens' => array(
				new Entity\Person(NULL, 'John')
			)
		)
	)
);


Assert::equal($john, $mapper->setValues(new Entity\Person(NULL, 'John'), $values));