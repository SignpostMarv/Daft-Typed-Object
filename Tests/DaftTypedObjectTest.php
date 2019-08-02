<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use BadMethodCallException;
use DateTimeImmutable;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase as Base;

class DaftTypedObjectTest extends Base
{
	public function testPropertyValueToScalarOrNullFailsWithDateTimeImmutable(
	) : void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf(
			(
				'Unsupported value object given:' .
				' %s::%s() only supports scalar and NULL'
			),
			DaftTypedObject::class,
			'PropertyValueToScalarOrNull'
		));

		DaftTypedObject::PropertyValueToScalarOrNull(
			'foo',
			new DateTimeImmutable()
		);
	}

	/**
	* @return array<class-string<DaftTypedObject>, array<int, array{0:array<string, scalar|array|object|null>, 1:array<string, scalar|null>}>>
	*/
	public function dataProviderPackedImplementations() : array
	{
		return [
			Fixtures\Immutable::class => [
				[
					['id' => 1, 'name' => 'foo'],
					['id' => 1, 'name' => 'foo'],
				],
			],
			Fixtures\Mutable::class => [
				[
					['id' => 1, 'name' => 'foo'],
					['id' => 1, 'name' => 'foo'],
				],
			],
			Fixtures\MutableWithNullables::class => [
				[
					['id' => 1, 'name' => 'foo'],
					['id' => 1, 'name' => 'foo'],
				],
				[
					['id' => 1, 'name' => null],
					['id' => 1, 'name' => null],
				],
			],
		];
	}

	/**
	* @return Generator<int, array{0:class-string<DaftTypedObject>, 1:array<string, scalar|array|object|null>, 2:array<string, scalar|null>}, mixed, void>
	*/
	public function dataProviderImplementations() : Generator
	{
		foreach (
			$this->dataProviderPackedImplementations() as $type => $arg_sets
		) {
			foreach ($arg_sets as $arg_pairs) {
				[$args, $json] = $arg_pairs;

				yield [$type, $args, $json];
			}
		}
	}

	/**
	* @dataProvider dataProviderImplementations
	*
	* @param class-string<DaftTypedObject> $type
	* @param array<string, scalar|array|object|null> $args
	* @param array<string, scalar|null> $expected
	*/
	public function testJsonSerialize(
		string $type,
		array $args,
		array $expected
	) : void {
		/**
		* @var array<string, scalar|null>
		*/
		$jsonified = (new $type($args))->jsonSerialize();

		static::assertSame($expected, $jsonified);

		static::assertSame(
			$expected,
			(new $type(array_combine(array_keys($jsonified), array_map(
				/**
				* @param scalar|null $value
				*
				* @return scalar|array|object|null
				*/
				function (string $property, $value) use ($type) {
					/**
					* @var scalar|array|object|null
					*/
					return $type::PropertyScalarOrNullToValue(
						$property,
						$value
					);
				},
				array_keys($jsonified),
				$jsonified
			))))->jsonSerialize()
		);
	}

	/**
	* @return array<class-string<Immutable>, array<int, array{0:array<string, scalar|array|object|null>, 1:array<string, scalar|null>}>>
	*/
	final public function dataProviderImmutableImplementations(
	) : array {
		/**
		* @var array<class-string<Immutable>, array<int, array{0:array<string, scalar|array|object|null>, 1:array<string, scalar|null>}>>
		*/
		return array_filter(
			$this->dataProviderPackedImplementations(),
			/**
			* @param class-string<DaftTypedObject> $type
			*/
			function (string $type) : bool {
				return is_a($type, Immutable::class, true);
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	* @return array<class-string<DaftTypedObject>, array<int, array{0:array<string, scalar|array|object|null>, 1:array<string, scalar|null>}>>
	*/
	final public function dataProviderMutableImplementations(
	) : array {
		/**
		* @var array<class-string<DaftTypedObject>, array<int, array{0:array<string, scalar|array|object|null>, 1:array<string, scalar|null>}>>
		*/
		return array_filter(
			$this->dataProviderPackedImplementations(),
			/**
			* @param class-string<DaftTypedObject> $type
			*/
			function (string $type) : bool {
				return ! is_a($type, Immutable::class, true);
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	* @return Generator<int, array{0:class-string<DaftTypedObject>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}, mixed, void>
	*/
	final public function dataProviderMutableImplementationsWithNonNullableProperty(
	) : Generator {
		foreach (
			$this->dataProviderMutableImplementations() as $type => $arg_sets
		) {
			/**
			* @var array<int, string>
			*/
			$non_nullable_properties = $type::TYPED_NULLABLE_PROPERTIES;

			/**
			* @var array<int, string>
			*/
			$properties = $type::TYPED_PROPERTIES;

			foreach ($properties as $property) {
				if ( ! in_array($property, $non_nullable_properties, true)) {
					foreach ($arg_sets as $arg_pairs) {
						[$args, $json] = $arg_pairs;

						/**
						* @var array{0:class-string<DaftTypedObject>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}
						*/
						$out = [$type, $property, $args, $json];

						yield $out;
					}
				}
			}
		}
	}

	/**
	* @dataProvider dataProviderMutableImplementationsWithNonNullableProperty
	*
	* @param class-string<DaftTypedObject> $type
	* @param array<string, scalar|array|object|null> $args
	*/
	public function testMutableUnsetFails(
		string $type,
		string $property,
		array $args
	) : void {
		$object = new $type($args);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf(
			'%1$s::$%2$s is not nullable!',
			$type,
			$property
		));

		unset($object->$property);
	}

	/**
	* @return Generator<int, array{0:class-string<Immutable>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}, mixed, void>
	*/
	final public function dataProviderImmutableImplementationsWithProperty(
	) : Generator {
		foreach (
			$this->dataProviderImmutableImplementations() as $type => $arg_sets
		) {
			foreach ($arg_sets as $arg_pairs) {
				[$args, $json] = $arg_pairs;

				foreach (array_keys($args) as $property) {
					/**
					* @var array{0:class-string<Immutable>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}
					*/
					$out = [$type, $property, $args, $json];

					yield $out;
				}
			}
		}
	}

	/**
	* @dataProvider dataProviderImmutableImplementationsWithProperty
	*
	* @param class-string<Immutable> $type
	* @param array<string, scalar|array|object|null> $args
	*/
	public function testImmutableSetFails(
		string $type,
		string $property,
		array $args
	) : void {
		$object = new $type($args);

		$this->expectException(BadMethodCallException::class);
		$this->expectExceptionMessage(sprintf(
			'%1$s::$%2$s cannot be set to %3$s, instances of %1$s are immutable.',
			$type,
			$property,
			var_export($object->$property, true)
		));

		$object->$property = $args[$property];
	}

	/**
	* @dataProvider dataProviderImmutableImplementationsWithProperty
	*
	* @param class-string<Immutable> $type
	*/
	public function testImmutableUnsetFails(
		string $type,
		string $property
	) : void {
		$object = new $type([]);

		$this->expectException(BadMethodCallException::class);
		$this->expectExceptionMessage(sprintf(
			'%1$s::$%2$s cannot be set to NULL, instances of %1$s are immutable.',
			$type,
			$property
		));

		unset($object->$property);
	}

	/**
	* @return Generator<int, array{0:class-string<Immutable>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}, mixed, void>
	*/
	final public function dataProviderMutableImplementationsWithNonNullProperty(
	) : Generator {
		foreach (
			$this->dataProviderMutableImplementations() as $type => $arg_sets
		) {
			foreach ($arg_sets as $arg_pairs) {
				[$args, $json] = $arg_pairs;

				foreach (array_keys($args) as $property) {
					if (is_null($args[$property])) {
						continue;
					}

					/**
					* @var array{0:class-string<Immutable>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}
					*/
					$out = [
						$type,
						$property,
						$args,
						$json,
					];

					yield $out;
				}
			}
		}
	}

	/**
	* @dataProvider dataProviderMutableImplementationsWithNonNullProperty
	*
	* @param class-string<DaftTypedObject> $type
	* @param array<string, scalar|array|object|null> $args
	*/
	public function testMutableSetSucceeds(
		string $type,
		string $property,
		array $args
	) : void {
		$object = new $type($args);

		/**
		* @var scalar|array|object|null
		*/
		$was = $value = $args[$property];

		if (is_numeric($value)) {
			++$value;
		} elseif (is_string($value)) {
			$value = strrev($value);
		}

		$object->$property = $value;

		$this->assertNotSame($was, $object->$property);
		$this->assertSame($value, $object->$property);

		/**
		* @var array<int, string>
		*/
		$nullable_properties = $type::TYPED_NULLABLE_PROPERTIES;

		if (
			in_array($property, $nullable_properties, true) &&
			! is_null($object->$property)
		) {
			unset($object->$property);
			$this->assertNull($object->$property);
		}
	}

	/**
	* @return Generator<int, array{0:class-string<Immutable>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}, mixed, void>
	*/
	final public function dataProviderImplementationsWithNonNullableProperty(
	) : Generator {
		foreach (
			$this->dataProviderImplementations() as $arg_set
		) {
			[$type, $args, $json] = $arg_set;

			foreach (array_keys($args) as $property) {
				if (is_null($args[$property])) {
					continue;
				}

				/**
				* @var array{0:class-string<Immutable>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}
				*/
				$out = [
					$type,
					$property,
					$args,
					$json,
				];

				yield $out;
			}
		}
	}

	/**
	* @dataProvider dataProviderImplementationsWithNonNullableProperty
	*
	* @param class-string<Immutable> $type
	* @param array<string, scalar|array|object|null> $args
	*/
	public function testIsset(
		string $type,
		string $property,
		array $args
	) : void {
		$object = new $type($args);

		static::assertTrue(isset($object->$property));
	}
}
