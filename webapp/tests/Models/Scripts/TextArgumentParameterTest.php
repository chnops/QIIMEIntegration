<?php

namespace Models\Scripts;

class TextArgumentParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--test_text";
	private $parameter;

	public static function setUpBeforeClass() {
		error_log("TextArgumentParameterTest");
	}

	/**
	 * @test
	 * @covers TextArgumentParameter::__construct
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid() {
		$this->parameter = new TextArgumentParameter($this->name, "asdfasdf", "/.*/");
		$this->assertEquals(true, $this->parameter->isValueValid());
		$this->parameter = new TextArgumentParameter($this->name, "123123", "/.*/");
		$this->assertEquals(true, $this->parameter->isValueValid());
		$this->parameter = new TextArgumentParameter($this->name, "!@#_.", "/.*/");
		$this->assertEquals(true, $this->parameter->isValueValid());

		$this->parameter = new TextArgumentParameter($this->name, "asdfasdf", "/[A-z]+/");
		$this->assertEquals(true, $this->parameter->isValueValid());
		$this->parameter = new TextArgumentParameter($this->name, "123123", "/\d+/");
		$this->assertEquals(true, $this->parameter->isValueValid());
		$this->parameter = new TextArgumentParameter($this->name, "!@#_.", "/[!|@|#|_|\.]/");
		$this->assertEquals(true, $this->parameter->isValueValid());

		$this->parameter = new TextArgumentParameter($this->name, "asdfasdf", "/\d+/");
		$this->assertNotEquals(true, $this->parameter->isValueValid());
		$this->parameter = new TextArgumentParameter($this->name, "asdfasdf", "/[!|@|#|_|\.]/");
		$this->assertNotEquals(true, $this->parameter->isValueValid());

		$this->parameter = new TextArgumentParameter($this->name, "123123", "/[A-z]+/");
		$this->assertNotEquals(true, $this->parameter->isValueValid());
		$this->parameter = new TextArgumentParameter($this->name, "123123", "/[!|@|#|_|\.]/");
		$this->assertNotEquals(true, $this->parameter->isValueValid());
		
		$this->parameter = new TextArgumentParameter($this->name, "", "/\d+/");
		$this->assertEquals(true, $this->parameter->isValueValid());
	}

}
