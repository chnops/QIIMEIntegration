<?php

namespace Models\Scripts\Parameters;

class NewFileParameterTest extends \PHPUnit_Framework_TestCase {

	private $object;

	public static function setUpBeforeClass() {
		error_log("NewFileParameterTest");
	}

	public function setUp() {
		$this->object = new NewFileParameter("name", "value");
	}

	public function testConstructor() {
		$this->assertEquals("name", $this->object->getName());
		$this->assertEquals("value", $this->object->getValue());
	}

}
