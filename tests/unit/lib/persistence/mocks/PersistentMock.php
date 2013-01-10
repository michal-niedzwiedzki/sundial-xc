<?php

/**
 * @TableName "persistence_mocks"
 * @PrimaryKey "id"
 */
class PersistentMock {

	/**
	 * @Column "id"
	 * @ReadOnly
	 */
	public $id;

	/**
	 * @Column "full_name"
	 * @NotNull
	 */
	public $name;

	/**
	 * @Column "age"
	 */
	public $age;

	/**
	 * @Column "wtf"
	 * @Transformation "TransformationMock"
	 */
	public $wtf;

}