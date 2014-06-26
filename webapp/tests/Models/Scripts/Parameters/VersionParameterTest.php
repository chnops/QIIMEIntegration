<?php

namespace Models\Scripts\Parameters;

class VersionParameterTest extends \PHPUnit_Framework_TestCase {

	private $parameter;

	public static function setUpBeforeClass() {
		error_log("VersionParameterTest");
	}

	public function setUp() {
		$mockProject = $this->getMock("\Models\Project", array(), array(), "", $callConstructor = false);
		$mockProject->expects($this->any())->method("getVersion")->will($this->returnValue("mock version 1.7"));
		$this->parameter = new VersionParameter($mockProject, "");
	}

	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->parameter->renderForOperatingSystem());
	}

	public function testRenderForForm() {
		$this->assertEquals("<a class=\"button\" onclick=\"alert('mock version 1.7');\">Version</a>", $this->parameter->renderForForm());
	}
}
