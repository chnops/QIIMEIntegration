<?php

namespace Models\Scripts\Parameters;

class NewFileParameterTest extends \PHPUnit_Framework_TestCase {

	private $parameter;

	public static function setUpBeforeClass() {
		error_log("NewFileParameterTest");
	}

	public function setUp() {
		$this->parameter = new NewFileParameter("name", "value");
	}

	public function testConstructor() {
		$this->assertEquals("name", $this->parameter->getName());
		$this->assertEquals("value", $this->parameter->getValue());
	}

}
