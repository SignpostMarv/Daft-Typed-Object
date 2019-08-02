Copyright 2019 SignpostMarv

# Daft-Typed-Object
[![Coverage Status](https://coveralls.io/repos/github/SignpostMarv/Daft-Typed-Object/badge.svg?branch=master)](https://coveralls.io/github/SignpostMarv/Daft-Typed-Object?branch=master)
[![Build Status](https://travis-ci.org/SignpostMarv/Daft-Typed-Object.svg?branch=master)](https://travis-ci.org/SignpostMarv/Daft-Typed-Object)
[![Type Coverage](https://shepherd.dev/github/signpostmarv/Daft-Typed-Object/coverage.svg)](https://shepherd.dev/github/signpostmarv/Daft-Typed-Object)

Typed Object, a simplified version of [signpostmarv/daft-object](https://github.com/SignpostMarv/daft-object)

## Example
### Immutable
```php
use SignpostMarv\DaftTypedObject\Immutable as Base;

/**
* @template-extends Base<array{id:int, name:string}>
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
* @template-extends Base<array{id:int, name:string}>
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
