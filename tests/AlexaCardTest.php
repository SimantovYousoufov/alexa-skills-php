<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Card\Card;
use InvalidArgumentException;
use Mockery;

class AlexaCardTest extends TestCase
{
	public function testItContructsFromAttributes()
	{
		$attributes = [
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => 'SomeContent',
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'smallURL',
				'largeImageUrl' => 'largeURL',
			],
		];

		$card = new Card($attributes);

		$this->assertEquals($attributes, $card->toArray());
	}

	public function testItCanGetAllAttributes()
	{
		$attributes = [
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => 'SomeContent',
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'smallURL',
				'largeImageUrl' => 'largeURL',
			],
		];

		$card = new Card($attributes);

		foreach ($attributes as $key => $value) {
			$method = camel_case("get_$key");
			$this->assertEquals($value, $card->$method());
		}
	}

	public function testItCanSetAllAttributes()
	{
		$attributes = [
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => 'SomeContent',
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'smallURL',
				'largeImageUrl' => 'largeURL',
			],
		];

		$card = new Card($attributes);

		$new_attributes = [
			'type' => 'new_Simple',
			'title' => 'new_SomeTitle',
			'content' => 'new_SomeContent',
			'text' => 'new_SomeText',
			'image' => [
				'smallImageUrl' => 'new_smallURL',
				'largeImageUrl' => 'new_largeURL',
			],
		];

		foreach ($new_attributes as $key => $value) {
			$method = camel_case("set_$key");
			$card->$method($value);
		}

		$this->assertEquals($new_attributes, $card->toArray());
	}

	public function testItThrowsExceptionIfSettingInvalidKey()
	{
		$attributes = [
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => 'SomeContent',
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'smallURL',
				'largeImageUrl' => 'largeURL',
			],
			'invalid_key' => 'foo',
		];

		$this->setExpectedException(InvalidArgumentException::class, 'invalid_key is not a valid attribute of ' . Card::class);
		$card = new Card($attributes);
	}

	public function testItCanMagicallyGetCardProperties()
	{
		$attributes = [
			'type' => 'Simple',
			'title' => 'SomeTitle',
			'content' => 'SomeContent',
			'text' => 'SomeText',
			'image' => [
				'smallImageUrl' => 'smallURL',
				'largeImageUrl' => 'largeURL',
			],
		];

		$card = new Card($attributes);

		foreach ($attributes as $key => $value) {
			$this->assertEquals($value, $card->$key);
		}
	}
}
