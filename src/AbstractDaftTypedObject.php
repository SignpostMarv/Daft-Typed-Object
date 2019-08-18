<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use InvalidArgumentException;

/**
* @template T as array<string, scalar|array|object|null>
* @template S as array<string, scalar|null>
*
* @template-implements DaftTypedObject<T, S>
*/
abstract class AbstractDaftTypedObject implements DaftTypedObject
{
	/**
	* @var array<int, key-of<T>>
	*/
	const TYPED_PROPERTIES = [];

	/**
	* @template K as key-of<T>
	*/
	public function __toArray() : array
	{
		/**
		* @var array<int, K>
		*/
		$properties = static::TYPED_PROPERTIES;

		/**
		* @var S
		*/
		return array_combine($properties, array_map(
			[$this, 'PropertyMapperToScalarOrNull'],
			$properties
		));
	}

	/**
	* @template K as key-of<S>
	*
	* @param S $array
	*
	* @return static
	*/
	public static function __fromArray(array $array) : DaftTypedObject
	{
		/**
		* @var array<int, K>
		*/
		$properties = array_keys($array);

		/**
		* @var array<string, scalar|array|object|null>
		*/
		$data = [];

		foreach ($properties as $property) {
			/**
			* @var S[K]
			*/
			$scalar_or_null = $array[$property];

			/**
			* @var T[K]
			*/
			$value = static::PropertyScalarOrNullToValue(
				(string) $property,
				$scalar_or_null
			);

			$data[$property] = $value;
		}

		/**
		* @var T
		*/
		$data = $data;

		/**
		* @var static
		*/
		return new static($data);
	}

	/**
	* @return S
	*/
	final public function jsonSerialize() : array
	{
		return $this->__toArray();
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $_property
	* @param T[K] $value
	*
	* @return S[K]
	*/
	public static function PropertyValueToScalarOrNull(
		string $_property,
		$value
	) {
		/**
		* @var scalar|array|object|null
		*/
		$value = $value;

		if ( ! is_scalar($value) && ! is_null($value)) {
			throw new InvalidArgumentException(sprintf(
				(
					'Unsupported value %s given:' .
					' %s() only supports scalar and NULL'
				),
				gettype($value),
				__METHOD__
			));
		}

		/**
		* @var S[K]
		*/
		return $value;
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $_property
	* @param S[K] $value
	*
	* @return T[K]
	*/
	public static function PropertyScalarOrNullToValue(
		string $_property,
		$value
	) {
		/**
		* @var T[K]
		*/
		return $value;
	}

	/**
	* @template K as key-of<T>
	*
	* @param K $property
	*
	* @return S[K]
	*/
	protected function PropertyMapperToScalarOrNull(string $property)
	{
		/**
		* @var T[K]
		*/
		$value = $this->$property;

		/**
		* @var S[K]
		*/
		return static::PropertyValueToScalarOrNull(
			(string) $property,
			$value
		);
	}
}
