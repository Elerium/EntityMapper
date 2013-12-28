<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace Elerium\Doctrine;

use Elerium;
use Doctrine;
use Doctrine\ORM\Mapping\ClassMetadataFactory,
	Doctrine\Common\Persistence\Mapping\ClassMetadata,
	Doctrine\Common\Collections\Collection,
	Doctrine\Common\Collections\ArrayCollection,
	Doctrine\ORM\Proxy\Proxy;

class EntityMapper
{
	/** @var \Doctrine\ORM\Mapping\ClassMetadataFactory */
	private $classMetadataFactory;

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadataFactory $classMetadataFactory
	 */
	public function __construct(ClassMetadataFactory $classMetadataFactory)
	{
		$this->classMetadataFactory = $classMetadataFactory;
	}

	/**
	 * @param object $entity
	 * @param array $values
	 * @return object
	 * @throws \Elerium\Doctrine\MappingException
	 */
	public function setValues($entity, array $values)
	{
		$metadata = $this->getMetadataFor($entity);

		foreach($values as $field => $value)
		{
			if($metadata->hasField($field))
			{
				$this->setFieldValue($metadata, $entity, $field, $value);
			}
			elseif($metadata->hasAssociation($field))
			{
				$this->setAssociationValue($metadata, $entity, $field, $value);
			}
			else
			{
				throw new MappingException("Field '$field' in entity '{$metadata->getName()}' does not exist.");
			}
		}

		return $entity;
	}

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
	 * @param object $entity
	 * @param string $field
	 * @param mixed $value
	 */
	protected function setFieldValue(ClassMetadata $metadata, $entity, $field, $value)
	{
		$metadata->setFieldValue($entity, $field, $value);
	}

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
	 * @param object $entity
	 * @param string $field
	 * @param \Doctrine\Common\Collections\Collection|array $value
	 * @throws \Elerium\Doctrine\MappingException
	 */
	protected function setAssociationValue(ClassMetadata $metadata, $entity, $field, $value)
	{
		if($value === NULL)
		{
			$this->setFieldValue($metadata, $entity, $field, NULL);
			return;
		}

		$isCollectionAssocation = $metadata->isCollectionValuedAssociation($field);

		if(is_array($value) || (!$isCollectionAssocation && $this->isEntity($value)) || ($isCollectionAssocation && $value instanceof Collection))
		{
			$associationMetadata = $this->getMetadataFor($metadata->getAssociationTargetClass($field));

			if($metadata->isSingleValuedAssociation($field))
			{
				if(is_array($value))
				{
					$value = $this->setValues($associationMetadata->newInstance(), $value);
				}
			}
			elseif($isCollectionAssocation)
			{
				if(($collection = $metadata->getFieldValue($entity, $field)) instanceof Collection)
				{
					$collection->clear();
				}
				else
				{
					$collection = new ArrayCollection;
				}

				foreach($value as $element)
				{
					if(!is_array($element) && !$this->isEntity($element))
					{
						throw new MappingException("Values for collection association '$field' can only contains entities or arrays, " . gettype($element) . " given.");
					}

					if(is_array($element))
					{
						$element = $this->setValues($associationMetadata->newInstance(), $element);
					}

					$collection->add($element);
				}

				$value = $collection;
			}

			$this->setFieldValue($metadata, $entity, $field, $value);
		}
		else
		{
			throw new MappingException("Only array or " . ($isCollectionAssocation ? 'collection' : 'entity') . " can be mapped into association '$field', " . gettype($value) . " given.");
		}
	}

	/**
	 * @param $entity
	 * @param array $ignores
	 * @return array|NULL
	 */
	public function getValues($entity, array $ignores = array())
	{
		if(in_array($entity, $ignores, TRUE))
		{
			return NULL;
		}

		$metadata = $this->getMetadataFor($entity);

		$ignores[] = $entity;

		$result = array();

		foreach($metadata->getFieldNames() as $field)
		{
			$result[$field] = $this->getFieldValue($metadata, $entity, $field);
		}

		foreach($metadata->getAssociationNames() as $field)
		{
			$result[$field] = $this->getAssociationValue($metadata, $entity, $field, $ignores);
		}

		return $result;
	}

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
	 * @param object $entity
	 * @param string $field
	 * @return mixed
	 */
	protected function getFieldValue(ClassMetadata $metadata, $entity, $field)
	{
		if($entity instanceof Proxy && !$entity->__isInitialized())
		{
			$entity->__load();
		}

		return $metadata->getFieldValue($entity, $field);
	}

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata
	 * @param object $entity
	 * @param string $field
	 * @param array $ignores
	 * @return array|NULL
	 * @throws \Elerium\Doctrine\MappingException
	 */
	protected function getAssociationValue(ClassMetadata $metadata, $entity, $field, array $ignores)
	{
		$associationValue = $metadata->getFieldValue($entity, $field);
		$isSingleAssociation = $metadata->isSingleValuedAssociation($field);

		if($associationValue === NULL)
		{
			return NULL;
		}
		elseif(($isSingleAssociation && !$this->isEntity($associationValue)) || (!$isSingleAssociation && !$associationValue instanceof Collection))
		{
			throw new MappingException("Value in association '$field' must be NULL or " . ($isSingleAssociation ? 'entity' : 'collection') . ", " . gettype($associationValue) . " given.");
		}

		if($metadata->isSingleValuedAssociation($field))
		{
			return $this->getValues($associationValue, $ignores);
		}
		elseif($metadata->isCollectionValuedAssociation($field))
		{
			$result = array();
			foreach($associationValue as $value)
			{
				$result[] = $this->getValues($value, $ignores);
			}

			return $result;
		}
	}

	/**
	 * @param object|string $entity
	 * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
	 * @throws \Elerium\Doctrine\MappingException
	 */
	private function getMetadataFor($entity)
	{
		if(!is_object($entity) && !class_exists($entity))
		{
			throw new MappingException("Class '$entity' does not exist.");
		}

		return $this->classMetadataFactory->getMetadataFor(is_object($entity) ? get_class($entity) : $entity);
	}

	/**
	 * @param object $entity
	 * @return bool
	 */
	private function isEntity($entity)
	{
		if(!is_object($entity))
		{
			return FALSE;
		}

		try
		{
			$this->classMetadataFactory->getMetadataFor(get_class($entity));
		}
		catch(Doctrine\ORM\Mapping\MappingException $e)
		{
			return FALSE;
		}

		return TRUE;
	}
}