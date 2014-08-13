<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class AlignSeqsTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("AlignSeqsTest");
	}

	private $defaultValue = 1;

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--input_fasta_fp" => "",
		"--alignment_method" => "",
		"--blast_db" => "",
		"--pairwise_alignment_method" => "",
		"--min_percent_id" => "",
		"--muscle_max_memory" => "",
		"--template_fp" => "",
		"--min_length" => "",
		"--verbose" => "",
		"--output_dir" => "",
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
		$this->object = new \Models\Scripts\QIIME\AlignSeqs($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\AlignSeqs::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "align_seqs.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\AlignSeqs::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Align sequences";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\AlignSeqs::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "align_seqs";

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

	public function testInputFp_absent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --input_fasta_fp is required</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testInputFp_present() {
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;

		$this->object->acceptInput($input);

	}

	public function testVersion_present() {
		$expected = $this->errorMessageIntro . "<li>The parameter --version can only be used when:</li>" . $this->errorMessageOutro;
		$actual = "";
		$input['--input_fasta_fp'] = true;
		$input['--version'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testHelp_present() {
		$expected = $this->errorMessageIntro . "<li>The parameter --help can only be used when:</li>" . $this->errorMessageOutro;
		$actual = "";
		$input['--input_fasta_fp'] = true;
		$input['--help'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testBlastDb_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "pynast";
		$input['--blast_db'] = true;

		$this->object->acceptInput($input);

	}
	public function testBlastDb_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --blast_db can only be used when:<br/>&nbsp;- --alignment_method is set to pynast</li>" . $this->errorMessageOutro;
		$actual = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--blast_db'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testPairwiseAlignmentMethod_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "pynast";
		$input['--pairwise_alignment_method'] = "uclust";

		$this->object->acceptInput($input);

	}
	public function testPairwiseAlignmentMethod_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --pairwise_alignment_method can only be used when:<br/>&nbsp;- --alignment_method is set to pynast</li>" . $this->errorMessageOutro;
		$actual = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--pairwise_alignment_method'] = "uclust";
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testMinPercentId_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "pynast";
		$input['--min_percent_id'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testMinPercentId_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --min_percent_id can only be used when:<br/>&nbsp;- --alignment_method is set to pynast</li>" . $this->errorMessageOutro;
		$actual = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--min_percent_id'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testMuscleMaxMemory_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "muscle";
		$input['--muscle_max_memory'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testMuscleMaxMemory_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --muscle_max_memory can only be used when:<br/>&nbsp;- --alignment_method is set to muscle</li>" . $this->errorMessageOutro;
		$actual = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--muscle_max_memory'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
