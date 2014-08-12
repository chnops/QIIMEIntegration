<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Utils;

class HelperTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("HelperTest");
	}

	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

	}
	public function setUp() {
		\Utils\Helper::setDefaultHelper(NULL);
		$this->object = new Helper();
	}

	/**
	 * @covers \Utils\Helper::getHelper
	 */
	public function testGetHelper_basic() {
		$expected = new Helper();

		$actual = \Utils\Helper::getHelper();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Helper::getHelper
	 */
	public function testGetHelper_resetFuncitonality() {
		$expected = \Utils\Helper::getHelper();
		\Utils\Helper::setDefaultHelper(NULL);

		$actual = \Utils\Helper::getHelper();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Utils\Helper::setDefaultHelper
	 */
	public function testSetDefaultHelper_notNull() {
		$unExpected = new Helper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->getMock();

		\Utils\Helper::setDefaultHelper($mockHelper);

		$actual = \Utils\Helper::getHelper();
		$this->assertNotEquals($unExpected, $actual);
	}
	/**
	 * @covers \Utils\Helper::setDefaultHelper
	 */
	public function testSetDefaultHelper_null() {
		\Utils\Helper::setDefaultHelper(NULL);
		$expected = \Utils\Helper::getHelper();

		\Utils\Helper::setDefaultHelper();

		$actual = \Utils\Helper::getHelper();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Utils\Helper::categorizeArray
	 */
	public function testCategorizeArray_emptyArray() {
		$expected = array();
		$raw = array();

		$actual = $this->object->categorizeArray($raw, "category");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Helper::categorizeArray
	 */
	public function testCategorizeArray_fieldNotSet_allOneCategory() {
		$expected = array(
			"cat1" => array(
				array("category" => "cat1", "field" => "elem1", "other_field" => "other1"),
				array("category" => "cat1", "field" => "elem2", "other_field" => "other2"),
				array("category" => "cat1", "field" => "elem3", "other_field" => "other3"),
			),
		);
		$raw = array(
			array("category" => "cat1", "field" => "elem1", "other_field" => "other1"),
			array("category" => "cat1", "field" => "elem2", "other_field" => "other2"),
			array("category" => "cat1", "field" => "elem3", "other_field" => "other3"),
		);

		$actual = $this->object->categorizeArray($raw, "category");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Helper::categorizeArray
	 */
	public function testCategorizeArray_fieldNotSet_allDifferentCategories() {
		$expected = array(
			"cat1" => array(
				array("category" => "cat1", "field" => "elem1", "other_field" => "other1"),
			),
			"cat2" => array(
				array("category" => "cat2", "field" => "elem2", "other_field" => "other2"),
			),
			"cat3" => array(
				array("category" => "cat3", "field" => "elem3", "other_field" => "other3"),
			),
		);
		$raw = array(
			array("category" => "cat1", "field" => "elem1", "other_field" => "other1"),
			array("category" => "cat2", "field" => "elem2", "other_field" => "other2"),
			array("category" => "cat3", "field" => "elem3", "other_field" => "other3"),
		);

		$actual = $this->object->categorizeArray($raw, "category");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Helper::categorizeArray
	 */
	public function testCategorizeArray_fieldSet_allOneCategory() {
		$expected = array(
			"cat1" => array("elem1", "elem2", "elem3"),
		);
		$raw = array(
			array("category" => "cat1", "field" => "elem1", "other_field" => "other1"),
			array("category" => "cat1", "field" => "elem2", "other_field" => "other2"),
			array("category" => "cat1", "field" => "elem3", "other_field" => "other3"),
		);

		$actual = $this->object->categorizeArray($raw, "category", "field");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Helper::categorizeArray
	 */
	public function testCategorizeArray_fieldSet_allDifferentCategories() {
		$expected = array(
			"cat1" => array("elem1"),
			"cat2" => array("elem2"),
			"cat3" => array("elem3"),
		);
		$raw = array(
			array("category" => "cat1", "field" => "elem1", "other_field" => "other1"),
			array("category" => "cat2", "field" => "elem2", "other_field" => "other2"),
			array("category" => "cat3", "field" => "elem3", "other_field" => "other3"),
		);

		$actual = $this->object->categorizeArray($raw, "category", "field");

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Utils\Helper::htmlentities
	 */
	public function testHtmlentities_stringIsZero() {
		$expected = 0;
		$input = 0;

		$actual = $this->object->htmlentities($input);

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Utils\Helper::htmlentities
	 */
	public function testHtmlentities_stringIsEmpty() {
		$expected = "";
		$input = "";

		$actual = $this->object->htmlentities($input);

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Utils\Helper::htmlentities
	 */
	public function testHtmlentities_bothQuotesAreEscaped() {
		$expected = "&quot; &#039;";

		$actual = $this->object->htmlentities("\" '");

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Utils\Helper::htmlentities
	 */
	public function testHtmlentities_entSubstitueIsNotSet_invalidValueIgnored() {
		$this->markTestIncomplete();
		if (defined('ENT_SUBSTITUTE')) {
			$expected = "\uFFFFE";
			$input = "\xFF";
			
			$actual = $this->object->htmlentities($input);

			$this->assertEquals($expected, $actual);
		}
		else {
			$expected = "";
			$input = "\xFF";

			$actual = $this->object->htmlentities($input);

			$this->assertEquals($expected, $actual);
		}
	}
}
