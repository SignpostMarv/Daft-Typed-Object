<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject\Fixtures;

use DateTimeImmutable;
use SignpostMarv\DaftTypedObject\AbstractDaftTypedObject as Base;

/**
* @template T as array{id:int, name:string, date:DateTimeImmutable|null}
* @template S as array{id:int, name:string, date:string|null}
*
* @template-extends Base<T, S>
*/
class MutableWithNullables extends Base
{
	const TYPED_PROPERTIES = ['id', 'name', 'date'];

	/**
	* @readonly
	*
	* @var int
	*/
	public $id;

	/**
	* @var string|null
	*/
	public $name;

	/**
	* @var DateTimeImmutable|null
	*/
	public $date;

	/**
	* @param T $data
	*/
	public function __construct(array $data)
	{
		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->date = $data['date'];
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	* @param T[K] $value
	*
	* @return S[K]
	*/
	public static function PropertyValueToScalarOrNull(
		string $property,
		$value
	) {
		/**
		* @var T[K]|DateTimeImmutable
		*/
		$value = $value;

		if ($value instanceof DateTimeImmutable) {
			/**
			* @var S[K]
			*/
			return (string) $value->format('Y-m-d');
		}

		/**
		* @var S[K]
		*/
		return parent::PropertyValueToScalarOrNull((string) $property, $value);
	}

	/**
	* @template K as key-of<S>
	*
	* @param K|'date' $property
	* @param S[K] $value
	*
	* @return T[K]
	*/
	public static function PropertyScalarOrNullToValue(
		string $property,
		$value
	) {
		/**
		* @var S[K]|string
		*/
		$value = $value;

		if ('date' === $property && is_string($value)) {
			$out = new DateTimeImmutable($value);
		} else {
			/**
			* @var S[K]
			*/
			$value = $value;

			/**
			* @var T[K]
			*/
			$out = parent::PropertyScalarOrNullToValue(
				(string) $property,
				$value
			);
		}

		/**
		* @var T[K]
		*/
		return $out;
	}
}
