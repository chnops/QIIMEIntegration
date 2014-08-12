<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class ValidateMappingFileTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ValidateMappingFileTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--mapping_fp" => "",
		"--not_barcoded" => "",
		"--variable_len_barcodes" => "",
		"--disable_primer_check" => "",
		"--added_demultiplex_field" => "",
		"--verbose" => "",
		"--output_dir" => "",
		"--suppress_html" => "",
		"--char_replace" => "",
	);
	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\QIIME\ValidateMappingFile($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ValidateMappingFile::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "validate_mapping_file.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ValidateMappingFile::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Validate map file";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ValidateMappingFile::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "validate_mapping_file";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testRequireds_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--mapping_fp'] = true;

		$this->object->acceptInput($input);

	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --mapping_fp is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		unset($input['--mapping_fp']);
		try {

		$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
