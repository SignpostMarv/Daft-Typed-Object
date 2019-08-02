<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use BadMethodCallException;

/**
* @template T as array<string, scalar|array|object|null>
*
* @template-extends DaftTypedObject<T>
*/
abstract class Immutable extends DaftTypedObject
{
	/**
	* @template K as key-of<T>
	*
	* @param K $k
	* @param T[K] $v
	*/
	final public function __set(string $k, $v) : void
	{
		throw new BadMethodCallException(sprintf(
			'%1$s::$%2$s cannot be set to %3$s, instances of %1$s are immutable.',
			static::class,
			$k,
			var_export($v, true)
		));
	}

	/**
	* @param key-of<T> $k
	*/
	final public function __unset(string $k) : void
	{
		throw new BadMethodCallException(sprintf(
			'%1$s::$%2$s cannot be set to %3$s, instances of %1$s are immutable.',
			static::class,
			$k,
			var_export(null, true)
		));
	}
}
