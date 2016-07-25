<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Session\EphemeralSession;
use AlexaPHP\Session\SessionInterface;
use Mockery;

class EphemeralSessionTest extends TestCase
{
	public function testItCanGetSessionForIdWithData()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo' => 'bar',
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = EphemeralSession::getSessionForId('SomeId', $session_data);

		$this->assertTrue($session instanceof SessionInterface);
		$this->assertTrue($session instanceof EphemeralSession);

		$this->assertEquals($session_data['attributes'], $session->getAttributes());
		$this->assertEquals($session_data['user'], $session->user());
	}

	public function testItSetsAttributes()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo' => 'bar',
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$new_attributes = ['baz' => 'bar'];

		$session = new EphemeralSession('SomeId', $session_data);
		$session->setAttributes($new_attributes);

		$this->assertEquals($new_attributes, $session->getAttributes());
	}

	public function testItSetsUser()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo' => 'bar',
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$new_user = ['userId' => 47,];

		$session = new EphemeralSession('SomeId', $session_data);
		$session->setUser($new_user);

		$this->assertEquals($new_user, $session->user());
	}

	public function testItSetsSessionShouldExpireFlag()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo' => 'bar',
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = new EphemeralSession('SomeId', $session_data);

		$this->assertFalse($session->expiring());

		$session->end();

		$this->assertTrue($session->expiring());
	}

	public function testItGetsAttributes()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo' => 'bar',
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = EphemeralSession::getSessionForId('SomeId', $session_data);

		$this->assertEquals($session_data['attributes'], $session->getAttributes());
	}

	public function testItGetsAttributeByKey()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo'    => 'bar',
				'nested' => [
					'very' => [
						'deeply' => 'Value1',
					],
				],
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = EphemeralSession::getSessionForId('SomeId', $session_data);

		$this->assertEquals('Value1', $session->getAttribute('nested.very.deeply'));
	}

	public function testItSetsAttributeByKeyAndValue()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo'    => 'bar',
				'nested' => [
					'very' => [
						'deeply' => 'Value1',
					],
				],
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = EphemeralSession::getSessionForId('SomeId', $session_data);

		$session->setAttribute('nested.very.deeply', 'Value2');

		$this->assertEquals('Value2', $session->getAttribute('nested.very.deeply'));
	}

	public function testItReturnsIsNew()
	{
		$session_data = [
			'new' => true,
			'attributes' => [
				'foo'    => 'bar',
				'nested' => [
					'very' => [
						'deeply' => 'Value1',
					],
				],
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = EphemeralSession::getSessionForId('SomeId', $session_data);

		$this->assertTrue($session->isNew());
	}

	public function testItSetsIsNew()
	{
		$session_data = [
			'new' => false,
			'attributes' => [
				'foo'    => 'bar',
				'nested' => [
					'very' => [
						'deeply' => 'Value1',
					],
				],
			],
			'user'       => [
				'userId' => 45,
			],
		];

		$session = EphemeralSession::getSessionForId('SomeId', $session_data);

		$this->assertFalse($session->isNew());

		$session->setIsNew();

		$this->assertTrue($session->isNew());
	}
}
