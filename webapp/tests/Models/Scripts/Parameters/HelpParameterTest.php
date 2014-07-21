<?php

namespace Models\Scripts\Parameters;

class HelpParameterTest extends \PHPUnit_Framework_TestCase {

	private $pararmeter;

	public static function setUpBeforeClass() {
		error_log("HelpParameterTest");
	}

	public function setUp() {
		$mockScript = $this->getMock("\\Models\\Scripts\\ScriptI");
		$mockScript->expects($this->any())->method("getHtmlId")->will($this->returnValue("mock"));
		$this->parameter = new HelpParameter($mockScript);
	}

	public function testAll() {
		$this->assertEmpty($this->parameter->getName());
		$this->assertEmpty($this->parameter->renderForOperatingSystem());
		$expectedLink = "<a href=\"public/manual/mock.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
		$this->assertEquals($expectedLink, $this->parameter->renderForForm());
	}

}
