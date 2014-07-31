<?php

namespace Models\Scripts;

class DefaultScriptTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("DefaultScriptTest");
	}

	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}

	public function setUp() {
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptName", "getScriptTitle", "getHtmlId"))
			->getMock();
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::initializeParameters
	 */
	public function testInitializeParameters() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::getParameters
	 */
	public function testGetParameters() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::getJsVar
	 */
	public function testGetJsVar() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::acceptInput
	 */
	public function testAcceptInput() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderCommand
	 */
	public function testRenderCommand() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderVersionCommand
	 */
	public function testRenderVersionCommand() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderHelp
	 */
	public function testRenderHelp() {
		$this->markTestIncomplete();
	}
}
