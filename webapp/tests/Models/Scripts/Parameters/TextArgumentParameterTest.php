<?php

namespace Models\Scripts\Parameters;

class TextArgumentParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--test_text";
	private $object;

	public static function setUpBeforeClass() {
		error_log("TextArgumentParameterTest");
	}

	/**
	 * @test
	 * @covers TextArgumentParameter::__construct
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid() {
		$letters = "asdfasdf";
		$digits = "123123";
		$punct = "!@#_.";

		$this->object = new TextArgumentParameter($this->name, $letters, TextArgumentParameter::PATTERN_ANYTHING_GOES);
		$this->assertEquals(true, $this->object->isValueValid($letters));
		$this->object = new TextArgumentParameter($this->name, $digits, TextArgumentParameter::PATTERN_ANYTHING_GOES);
		$this->assertEquals(true, $this->object->isValueValid($digits));
		$this->object = new TextArgumentParameter($this->name, $punct, TextArgumentParameter::PATTERN_ANYTHING_GOES);
		$this->assertEquals(true, $this->object->isValueValid($punct));

		$this->object = new TextArgumentParameter($this->name, $letters, "/[A-z]+/");
		$this->assertEquals(true, $this->object->isValueValid($letters));
		$this->object = new TextArgumentParameter($this->name, $digits, TextArgumentParameter::PATTERN_DIGIT);
		$this->assertEquals(true, $this->object->isValueValid($digits));
		$this->object = new TextArgumentParameter($this->name, $punct, "/[!|@|#|_|\.]/");
		$this->assertEquals(true, $this->object->isValueValid($punct));

		$this->object = new TextArgumentParameter($this->name, $letters, TextArgumentParameter::PATTERN_DIGIT);
		$this->assertNotEquals(true, $this->object->isValueValid($letters));
		$this->object = new TextArgumentParameter($this->name, $letters, "/[!|@|#|_|\.]/");
		$this->assertNotEquals(true, $this->object->isValueValid($letters));

		$this->object = new TextArgumentParameter($this->name, $digits, "/[A-z]+/");
		$this->assertNotEquals(true, $this->object->isValueValid($digits));
		$this->object = new TextArgumentParameter($this->name, $digits, "/[!|@|#|_|\.]/");
		$this->assertNotEquals(true, $this->object->isValueValid($digits));
		
		$this->object = new TextArgumentParameter($this->name, "", TextArgumentParameter::PATTERN_DIGIT);
		$this->assertEquals(true, $this->object->isValueValid(""));
	}

}
