<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use SignpostMarv\DaftTypedObject\Immutable as Base;

/**
* @psalm-type DATA = array{id:int, name:string}
*
* @template-extends Base<DATA, DATA>
*
* @property-read int $id
* @property-read string $name
*/
class Immutable extends Base
{
	const TYPED_PROPERTIES = ['id', 'name'];

	/**
	* @var int
	*/
	protected $id;

	/**
	* @var string
	*/
	protected $name;
}
