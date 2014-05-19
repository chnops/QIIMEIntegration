<?php

namespace Models\Scripts;

class NewFileParameterTest extends \PHPUnit_Framework_TestCase {

	private $parameter;

	public function setUp() {
		$this->parameter = new NewFileParameter("name", "value");
	}

	public function testConstructor() {
		$this->assertEquals("name", $this->parameter->getName());
		$this->assertEquals("value", $this->parameter->getValue());
	}

}
