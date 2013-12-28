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

$entity = new Entity\Person;
$entity->parent = NULL;
Assert::equal(array('id' => NULL, 'name' => NULL, 'parent' => NULL, 'childrens' => NULL), $mapper->getValues($entity));

$entity = new Entity\Person;
$entity->parent = new ArrayCollection;
Assert::throws(function() use ($mapper, $entity)  {
	$mapper->getValues($entity);
}, 'Elerium\Doctrine\MappingException', "Value in association 'parent' must be NULL or entity, object given.");

$entity = new Entity\Person;
$entity->parent = new Entity\Person;
$mapper->getValues($entity);

$entity = new Entity\Person;
$entity->childrens = new Entity\Person;
Assert::throws(function() use ($mapper, $entity) {
	$mapper->getValues($entity);
}, 'Elerium\Doctrine\MappingException', "Value in association 'childrens' must be NULL or collection, object given.");

$entity = new Entity\Person;
$entity->childrens = new ArrayCollection;
$mapper->getValues($entity);

$entity = new Entity\Person;
$entity->parent = 'EleriumTests\Doctrine\Entity\Person';
Assert::throws(function() use ($mapper, $entity) {
	$mapper->getValues($entity);
}, 'Elerium\Doctrine\MappingException', "Value in association 'parent' must be NULL or entity, string given.");

$entity = new Entity\Person;
$entity->parent = array();
Assert::throws(function() use ($mapper, $entity) {
	$mapper->getValues($entity);
}, 'Elerium\Doctrine\MappingException', "Value in association 'parent' must be NULL or entity, array given.");