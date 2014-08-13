<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;
use Models\Scripts\Parameters\DefaultParameter;

class PickRepSetTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("PickRepSetTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\QIIME\PickRepSet($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\PickRepSet::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "pick_rep_set.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\PickRepSet::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Pick representative sequences";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\PickRepSet::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "pick_rep_set";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testScriptExists() {
		$expecteds = array(
			"script_location" => "/macqiime/QIIME/bin/{$this->object->getScriptName()}",
			"which_return" => "0",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(NULL)
			->getMock();
		$sourceFile = $mockProject->getEnvironmentSource();
		$systemCommand = "source {$sourceFile}; which {$this->object->getScriptName()}; echo $?";

		exec($systemCommand, $output);

		$actuals['script_location'] = $output[0];
		$actuals['which_return'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}

	public function testRequireds_present() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --fasta_file is required when:<br/>&nbsp;- --reference_seqs_fp is not set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--input_file'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_file is required</li>" .
			"<li>The parameter --fasta_file is required when:<br/>&nbsp;- --reference_seqs_fp is not set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testReferenceSeqsFp_triggerIsPresent_valueIsPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_file is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--reference_seqs_fp'] = true;
		$input['--fasta_file'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReferenceSeqsFp_triggerIsPresent_valueIsNotPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_file is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--reference_seqs_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReferenceSeqsFp_triggerIsNotPresent_valueIsPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_file is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--fasta_file'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReferenceSeqsFp_triggerIsNotPresent_valueIsNotPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_file is required</li>" .
			"<li>The parameter --fasta_file is required when:<br/>&nbsp;- --reference_seqs_fp is not set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
