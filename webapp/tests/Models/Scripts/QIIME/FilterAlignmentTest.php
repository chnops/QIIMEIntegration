<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class FilterAlignmentTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("FilterAlignmentTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--input_fasta_file" => "",
		"--remove_outliers" => "",
		"--threshold" => "",
		"--lane_mask_fp" => "",
		"--entropy_threshold" => "",
		"--suppress_lane_mask_filter" => "",
		"--allowed_gap_frac" => "",
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
		$this->object = new \Models\Scripts\QIIME\FilterAlignment($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\FilterAlignment::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "filter_alignment.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\FilterAlignment::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Filter sequence alignment";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\FilterAlignment::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "filter_alignment";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testInputFastaFile_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}
	public function testInputFastaFile_notPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --input_fasta_file is required</li>" . $this->errorMessageOutro;
		$input = $this->emptyInput;
		unset($input['--input_fasta_file']);
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}

	public function testThreshold_removeOutliersPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		$input['--remove_outliers'] = true;
		$input['--threshold'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}
	public function testThreshold_removeOutliersNotPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --threshold can only be used when:<br/>&nbsp;- --remove_outliers is set</li>" . $this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--remove_outliers']);
		$input['--threshold'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}

	public function testSuppressLaneMaskFilter_entropyThresholdPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --suppress_lane_mask_filter cannot be used when:<br/>&nbsp;- --entropy_threshold is set</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--lane_mask_fp']);
		$input['--entropy_threshold'] = true;
		$input['--suppress_lane_mask_filter'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInpt should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testSuppressLaneMaskFilter_entropyThresholdNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--lane_mask_fp']);
		unset($input['--entropy_threshold']);
		$input['--suppress_lane_mask_filter'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInpt should not have thrown exception: {$ex->getMessage()}");
		}
	}

	public function testLaneMaskFp_entropyThresholdPresent_suppressLaneMaskNotPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --lane_mask_fp cannot be used when:<br/>&nbsp;- --entropy_threshold is set</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		$input['--entropy_threshold'] = true;
		unset($input['--suppress_lane_mask_filter']);
		$input['--lane_mask_fp'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInpt should have thrown exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testLaneMaskFp_entropyThresholdNotPresent_suppressLaneMaskPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --lane_mask_fp cannot be used when:<br/>&nbsp;- --suppress_lane_mask_filter is set</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--entropy_threshold']);
		$input['--suppress_lane_mask_filter'] = true;
		$input['--lane_mask_fp'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInpt should have thrown exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testLaneMaskFp_entropyThresholdNotPresent_suppressLaneMaskNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_file'] = true;
		unset($input['--entropy_threshold']);
		unset($input['--suppress_lane_mask_filter']);
		$input['--lane_mask_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInpt should not have thrown exception: {$ex->getMessage()}");
		}
	}
}
