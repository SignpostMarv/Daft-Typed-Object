<?php
/**
* @author SignpostMarv
*/
declare(strict_types=1);

namespace SignpostMarv\DaftTypedObject;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase as Base;

class FixturesTest extends Base
{
	public function test_mutable() : void
	{
		$a = new Fixtures\Mutable([
			'id' => 1,
			'name' => 'foo',
		]);

		static::assertSame(1, $a->id);
		static::assertSame('foo', $a->name);
	}

	public function test_mutable_with_nullables() : void
	{
		$a = new Fixtures\MutableWithNullables([
			'id' => 1,
			'name' => 'foo',
			'date' => null,
		]);

		static::assertSame(1, $a->id);
		static::assertSame('foo', $a->name);
		static::assertNull($a->date);

		$b = new Fixtures\MutableWithNullables([
			'id' => 1,
			'name' => 'foo',
			'date' => new DateTimeImmutable((string) date('Y-m-d', 0)),
		]);

		static::assertSame(1, $b->id);
		static::assertSame('foo', $b->name);
		static::assertNotNull($b->date);
		static::assertSame('1970-01-01', $b->date->format('Y-m-d'));
		static::assertSame('00:00:00', $b->date->format('H:i:s'));
	}
}
