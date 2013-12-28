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
	$mapper->setValues(NULL, array());
}, 'Elerium\Doctrine\MappingException', "Class '' does not exist.");

Assert::throws(function() use ($mapper) {
	$mapper->setValues('entity', array());
}, 'Elerium\Doctrine\MappingException', "Class 'entity' does not exist.");

Assert::throws(function() use ($mapper) {
	$mapper->getValues(NULL);
}, 'Elerium\Doctrine\MappingException', "Class '' does not exist.");

Assert::throws(function() use ($mapper) {
	$mapper->getValues('entity');
}, 'Elerium\Doctrine\MappingException', "Class 'entity' does not exist.");