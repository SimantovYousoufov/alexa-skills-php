<?php

namespace AlexaPHP\Tests;

use AlexaPHP\Utility\URL;
use LogicException;
use Mockery;

class URLTest extends TestCase
{
	public function testItThrowsExceptionOnInvalidUrl()
	{
		$string = 'http:///malformed.com';

		$this->setExpectedException(LogicException::class, 'Malformed URL passed to ' . URL::class);
		$url = new URL($string);
	}

	public function testItParsesURL()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string), $url->getParsedURL());
	}

	public function testItReturnsNullOnArbitraryAttributeIfNotSet()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem'; // Doesn't have port set

		$url = new URL($string);

		$this->assertNull($url->port());
	}

	public function testItCanCheckScheme()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertTrue($url->scheme('https'));
	}

	public function testItReturnsUrlScheme()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['scheme'], $url->scheme());
	}

	public function testItReturnsFalseIfCheckFailsForScheme()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertFalse($url->scheme('not it'));
	}

	public function testItCanCheckHost()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertTrue($url->host('s3.amazonaws.com'));
	}

	public function testItReturnsUrlHost()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['host'], $url->host());
	}

	public function testItReturnsFalseIfCheckFailsForHost()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertFalse($url->host('not it'));
	}

	public function testItCanCheckPath()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertTrue($url->path('/echo.api/echo-api-cert.pem'));
	}

	public function testItReturnsUrlPath()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['path'], $url->path());
	}

	public function testItReturnsFalseIfCheckFailsForPath()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertFalse($url->path('not it'));
	}

	public function testItCanCheckPort()
	{
		$string = 'https://s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertTrue($url->port(443));
	}

	public function testItReturnsUrlPort()
	{
		$string = 'https://s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['port'], $url->port());
	}

	public function testItReturnsFalseIfCheckFailsForPort()
	{
		$string = 'https://s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertFalse($url->port('not it'));
	}

	public function testItCanCheckUser()
	{
		$string = 'https://user@s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertTrue($url->user('user'));
	}

	public function testItReturnsUrlUser()
	{
		$string = 'https://user@s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['user'], $url->user());
	}

	public function testItReturnsFalseIfCheckFailsForUser()
	{
		$string = 'https://user@s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertFalse($url->user('not it'));
	}

	public function testItCanCheckPass()
	{
		$string = 'https://user:pass@s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertTrue($url->pass('pass'));
	}

	public function testItReturnsUrlPass()
	{
		$string = 'https://user:pass@s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['pass'], $url->pass());
	}

	public function testItReturnsFalseIfCheckFailsForPass()
	{
		$string = 'https://user:pass@s3.amazonaws.com:443/echo.api/echo-api-cert.pem';

		$url = new URL($string);

		$this->assertFalse($url->pass('not it'));
	}

	public function testItCanCheckQuery()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem?foo=bar';

		$url = new URL($string);

		$this->assertTrue($url->query(['foo' => 'bar']));
	}

	public function testItReturnsUrlQuery()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem?foo=bar';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['query'], $url->query());
	}

	public function testItReturnsFalseIfCheckFailsForQuery()
	{
		$string = 'https://s3.amazonaws.com/echo.api/echo-api-cert.pem?foo=bar';

		$url = new URL($string);

		$this->assertFalse($url->query('not it'));
	}

	public function testItCanCheckFragment()
	{
		$string = 'https://s3.amazonaws.com/echo.api/#/Some/Fragment';

		$url = new URL($string);

		$this->assertTrue($url->fragment('/Some/Fragment'));
	}

	public function testItReturnsUrlFragment()
	{
		$string = 'https://s3.amazonaws.com/echo.api/#/Some/Fragment';

		$url = new URL($string);

		$this->assertEquals(parse_url($string)['fragment'], $url->fragment());
	}

	public function testItReturnsFalseIfCheckFailsForFragment()
	{
		$string = 'https://s3.amazonaws.com/echo.api/#/Some/Fragment';

		$url = new URL($string);

		$this->assertFalse($url->fragment('not it'));
	}

	public function testItReturnsOriginalUrl()
	{
		$string = 'https://s3.amazonaws.com/echo.api/#/Some/Fragment';

		$url = new URL($string);

		$this->assertEquals($string, $url->originalUrl());
	}
}
