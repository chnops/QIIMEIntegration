<?php

namespace Models\Scripts\Parameter;

class LabelTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("LabelTest");
	}

	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);
	}
	public function setUp() {
		$this->object = new \Models\Scripts\Parameters\Label("value");
	}

	/**
	 * @covers \Models\Scripts\QIIME\
	 */
	public function testIncludes() {
		$this->assertTrue(false);
	}
}
