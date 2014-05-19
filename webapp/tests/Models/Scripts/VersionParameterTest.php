<?php

namespace Models\Scripts;

class VersionParameterTest extends \PHPUnit_Framework_TestCase {

	private $parameter;

	public function setUp() {
		$this->parameter = new VersionParameter();
	}

	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->parameter->renderForOperatingSystem());
	}

	public function testRenderForForm() {
		$this->assertEquals("<a class=\"button\" onclick=\"alert('Version Info');\">Version</a>", $this->parameter->renderForForm());
	}
}
