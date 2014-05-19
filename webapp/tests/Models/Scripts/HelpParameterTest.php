<?php

namespace Models\Scripts;

class HelpParameterTest extends \PHPUnit_Framework_TestCase {

	private $pararmeter;

	public function setUp() {
		$this->parameter = new HelpParameter();
	}

	public function testAll() {
		$this->assertEmpty($this->parameter->getName());
		$this->parameter = new HelpParameter("invalid", "unused", "arguments");
		$this->assertEmpty($this->parameter->getName());
		$this->assertEmpty($this->parameter->renderForOperatingSystem());

		// TODO get actual doc page
		$expectedLink = "<a href=\"http://wikipedia.org\" target=\"_blank\" class=\"button\">See man page</a>";
		$this->assertEquals($expectedLink, $this->parameter->renderForForm());
	}

}
