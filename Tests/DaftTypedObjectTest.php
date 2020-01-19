<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use DateTimeImmutable;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase as Base;
use ReflectionProperty;

class DaftTypedObjectTest extends Base
{
	public function test_property_value_to_scalar_or_null_fails_with_date_time_immutable(
	) : void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf(
			(
				'Unsupported value object given:' .
				' %s::%s() only supports scalar and NULL'
			),
			AbstractDaftTypedObject::class,
			'PropertyValueToScalarOrNull'
		));

		AbstractDaftTypedObject::PropertyValueToScalarOrNull(
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
			Fixtures\Mutable::class => [
				[
					['id' => 1, 'name' => 'foo'],
					['id' => 1, 'name' => 'foo'],
				],
			],
			Fixtures\MutableWithNullables::class => [
				[
					[
						'id' => 1,
						'name' => 'foo',
						'date' => new DateTimeImmutable('1970-01-01'),
					],
					['id' => 1, 'name' => 'foo', 'date' => '1970-01-01'],
				],
				[
					['id' => 1, 'name' => 'foo', 'date' => null],
					['id' => 1, 'name' => 'foo', 'date' => null],
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
			if ( ! class_exists($type)) {
				continue;
			}

			foreach ($arg_sets as $arg_pairs) {
				[$args, $json] = $arg_pairs;

				yield [$type, $args, $json];
			}
		}
	}

	/**
	* @dataProvider dataProviderImplementations
	*
	* @template T as array<string, scalar|array|object|null>
	* @template S as array<string, scalar|null>
	*
	* @param class-string<DaftTypedObject> $type
	* @param T $args
	* @param S $expected
	*/
	public function test_json_serialize(
		string $type,
		array $args,
		array $expected
	) : void {
		/**
		* @var S
		*/
		$jsonified = (new $type($args))->jsonSerialize();

		static::assertSame($expected, $jsonified);

		/**
		* @var array<string, scalar|null>
		*/
		$jsonified = $jsonified;

		static::assertSame(
			$expected,
			$type::__fromArray($jsonified)->jsonSerialize()
		);
	}

	/**
	* @return Generator<int, array{0:class-string<DaftTypedObject>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}, mixed, void>
	*/
	final public function dataProviderMutableImplementationsWithNonNullProperty(
	) : Generator {
		foreach (
			$this->dataProviderPackedImplementations() as $type => $arg_sets
		) {
			foreach ($arg_sets as $arg_pairs) {
				[$args, $json] = $arg_pairs;

				foreach (array_keys($args) as $property) {
					if (is_null($args[$property])) {
						continue;
					}

					$property_comment = (
						new ReflectionProperty($type, $property)
					)->getDocComment();

					if (
						is_string($property_comment) &&
						1 === preg_match(
							'/\s+\* @readonly\s/',
							$property_comment
						)
					) {
						continue;
					}

					yield [
						$type,
						$property,
						$args,
						$json,
					];
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
	public function test_mutable_set_succeeds(
		string $type,
		string $property,
		array $args
	) : void {
		$object = new $type($args);

		$was = $value = $args[$property];

		if (is_numeric($value)) {
			++$value;
		} elseif (is_string($value)) {
			$value = strrev($value);
		} elseif ($value instanceof DateTimeImmutable) {
			$value = $value->modify('+1 second');
		}

		$object->$property = $value;

		static::assertNotSame($was, $object->$property);
		static::assertSame($value, $object->$property);
	}

	/**
	* @return Generator<int, array{0:class-string<DaftTypedObject>, 1:string, 2:array<string, scalar|array|object|null>, 3:array<string, scalar|null>}, mixed, void>
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

				yield [
					$type,
					$property,
					$args,
					$json,
				];
			}
		}
	}

	/**
	* @dataProvider dataProviderImplementationsWithNonNullableProperty
	*
	* @param class-string<DaftTypedObject> $type
	* @param array<string, scalar|array|object|null> $args
	*/
	public function test_isset(
		string $type,
		string $property,
		array $args
	) : void {
		$object = new $type($args);

		static::assertTrue(isset($object->$property));
	}
}
