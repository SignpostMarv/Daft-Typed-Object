<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use SignpostMarv\DaftTypedObject\DaftTypedObject as Base;

/**
* @psalm-type DATA = array{id:int, name:string}
*
* @template-extends Base<DATA, DATA>
*
* @property int $id
* @property string $name
*/
class Mutable extends Base
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
