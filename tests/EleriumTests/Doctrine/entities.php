<?php

/**
 * Part of Elerium Framework
 * Copyright (c) 2013
 */
 
namespace EleriumTests\Doctrine\Entity;

/**
 * @Entity
 */
class Simple
{
	/**
	 * @param int $id
	 * @param string $string
	 */
	public function __construct($id = NULL, $string = NULL)
	{
		$this->id = $id;
		$this->string = $string;
	}

	/**
	 * @Id
	 * @Column(type="integer")
	 */
	public $id;

	/**
	 * @Column(type="string")
	 */
	public $string;
}

/**
 * @Entity
 */
class Person
{

	/**
	 * @Id
	 * @Column(type="integer")
	 */
	public $id;

	/**
	 * @Column(type="string")
	 */
	public $name;

	/**
	 * @ManyToOne(targetEntity="Person", inversedBy="childrens")
	 * @JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	public $parent;

	/**
	 * @OneToMany(targetEntity="Person", mappedBy="parent")
	 */
	public $childrens;

	/**
	 * @param int $id
	 * @param string $name
	 */
	public function __construct($id = NULL, $name = NULL)
	{
		$this->id = $id;
		$this->name = $name;
	}
}