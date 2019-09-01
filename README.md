Copyright 2019 SignpostMarv

# Daft-Typed-Object
[![Coverage Status](https://coveralls.io/repos/github/daft-framework/Daft-Typed-Object/badge.svg?branch=master)](https://coveralls.io/github/daft-framework/Daft-Typed-Object?branch=master)
[![Build Status](https://travis-ci.org/daft-framework/Daft-Typed-Object.svg?branch=master)](https://travis-ci.org/daft-framework/Daft-Typed-Object)
[![Type Coverage](https://shepherd.dev/github/daft-framework/Daft-Typed-Object/coverage.svg)](https://shepherd.dev/github/daft-framework/Daft-Typed-Object)

Typed Object, a simplified version of [signpostmarv/daft-object](https://github.com/SignpostMarv/daft-object)

## Example
### Immutable
```php
use SignpostMarv\DaftTypedObject\Immutable as Base;

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
```

### Mutable
```php
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

```

### Mutable with `DateTimeImmutable` & nullables
```php
use DateTimeImmutable;
use SignpostMarv\DaftTypedObject\DaftTypedObject as Base;

/**
* @template T as array{id:int, name:string, date:DateTimeImmutable|null}
* @template S as array{id:int, name:string, date:string|null}
*
* @template-extends Base<T, S>
*
* @property-read int $id
* @property-read string $name
* @property-read DateTimeImmutable $date
*/
class MutableWithNullables extends Base
{
	const TYPED_PROPERTIES = ['id', 'name', 'date'];

	const TYPED_NULLABLE_PROPERTIES = ['date'];

	/**
	* @var int
	*/
	protected $id;

	/**
	* @var string|null
	*/
	protected $name;

	/**
	* @var DateTimeImmutable|null
	*/
	protected $date;

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
```
