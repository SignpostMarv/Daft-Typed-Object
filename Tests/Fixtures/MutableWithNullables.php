<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use SignpostMarv\DaftTypedObject\DaftTypedObject as Base;

/**
* @template-extends Base<array{id:int, name:string|null}>
*
* @property-read int $id
* @property-read string|null $name
*/
class MutableWithNullables extends Base
{
	const TYPED_PROPERTIES = ['id', 'name'];

	const TYPED_NULLABLE_PROPERTIES = ['name'];

	/**
	* @var int
	*/
	protected $id;

	/**
	* @var string|null
	*/
	protected $name;
}
