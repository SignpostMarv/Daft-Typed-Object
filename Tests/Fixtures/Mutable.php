<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use SignpostMarv\DaftTypedObject\AbstractDaftTypedObject as Base;

/**
* @psalm-type DATA = array{id:int, name:string}
*
* @template-extends Base<DATA, DATA>
*/
class Mutable extends Base
{
	const TYPED_PROPERTIES = ['id', 'name'];

	/**
	* @readonly
	*
	* @var int
	*/
	public $id;

	/**
	* @var string
	*/
	public $name;

	/**
	* @param DATA $data
	*/
	public function __construct(array $data)
	{
		$this->id = $data['id'];
		$this->name = $data['name'];
	}
}
