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
	public function testMutable() : void
	{
		$a = new Fixtures\Mutable([
			'id' => 1,
			'name' => 'foo',
		]);

		$this->assertSame(1, $a->id);
		$this->assertSame('foo', $a->name);
	}

	public function testMutableWithNullables() : void
	{
		$a = new Fixtures\MutableWithNullables([
			'id' => 1,
			'name' => 'foo',
			'date' => null,
		]);

		$this->assertSame(1, $a->id);
		$this->assertSame('foo', $a->name);
		$this->assertNull($a->date);

		$b = new Fixtures\MutableWithNullables([
			'id' => 1,
			'name' => 'foo',
			'date' => new DateTimeImmutable((string) date('Y-m-d', 0)),
		]);

		$this->assertSame(1, $b->id);
		$this->assertSame('foo', $b->name);
		$this->assertNotNull($b->date);
		$this->assertSame('1970-01-01', $b->date->format('Y-m-d'));
		$this->assertSame('00:00:00', $b->date->format('H:i:s'));
	}
}
