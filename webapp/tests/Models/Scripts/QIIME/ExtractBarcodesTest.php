<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class ExtractBarcodesTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ExtractBarcodesTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--fastq1" => "",
		"--input_type" => "",
		"--fastq2" => "",
		"--bc2_len" => "",
		"--char_delineator" => "",
		"--switch_bc_order" => "",
		"--mapping_fp" => "",
		"--attempt_read_reorientation" => "",
		"--disable_header_match" => "",
		"--rev_comp_bc2" => "",
		"--bc1_len" => "",
		"--verbose" => "",
		"--output_dir" => "",
		"--rev_comp_bc1" => "",
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
		$this->object = new \Models\Scripts\QIIME\ExtractBarcodes($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ExtractBarcodes::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "extract_barcodes.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ExtractBarcodes::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Extract barcodes";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ExtractBarcodes::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "extract_barcodes";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testFastq1_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown this exception: {$ex->getMessage()}");
		}		
	}
	public function testfastq1_notPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --fastq1 is required</li>" . $this->errorMessageOutro;
		$input = $this->emptyInput;
		unset($input['--fastq1']);
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}

	public function testInputType_barcodeSingleEnd_someDependentsPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --fastq2 can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_end<br/>&nbsp;- --input_type is set to barcode_in_label</li>" .
			"<li>The parameter --char_delineator can only be used when:<br/>&nbsp;- --input_type is set to barcode_in_label</li>" . 
			"<li>The parameter --switch_bc_order can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_stitched</li>" . 
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_single_end";
		$input['--fastq2'] = true;
		$input['--char_delineator'] = true;
		$input['--switch_bc_order'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testInputType_barcodeSingleEnd_zeroDependentsPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_single_end";
		unset($input['--fastq2']);
		unset($input['--bc2_len']);
		unset($input['--rev_comp_bc2']);
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}

	public function testInputType_barcodePairedEnd_someDependentsPresent() {
		$expected = $this->errorMessageIntro .
//			"<li>The parameter --fastq2 can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_end<br/>&nbsp;- --input_type is set to barcode_in_label</li>" .
			"<li>The parameter --char_delineator can only be used when:<br/>&nbsp;- --input_type is set to barcode_in_label</li>" . 
			"<li>The parameter --switch_bc_order can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_stitched</li>" . 
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_end";
		$input['--fastq2'] = true;
		$input['--char_delineator'] = true;
		$input['--switch_bc_order'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}

	public function testInputType_barcodePairedEnd_zeroDependentsPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_end";
		unset($input['--fastq2']);
		unset($input['--bc2_len']);
		unset($input['--rev_comp_bc2']);
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}

	public function testInputType_barcodePairedStitched_someDependentsPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --fastq2 can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_end<br/>&nbsp;- --input_type is set to barcode_in_label</li>" .
			"<li>The parameter --char_delineator can only be used when:<br/>&nbsp;- --input_type is set to barcode_in_label</li>" . 
//			"<li>The parameter --switch_bc_order can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_stitched</li>" . 
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_stitched";
		$input['--fastq2'] = true;
		$input['--char_delineator'] = true;
		$input['--switch_bc_order'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}

	public function testInputType_barcodePairedStitched_zeroDependentsPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_stitched";
		unset($input['--fastq2']);
		unset($input['--bc2_len']);
		unset($input['--rev_comp_bc2']);
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}

	public function testInputType_barcodeInLabel_someDependentsPresent() {
		$expected = $this->errorMessageIntro .
//			"<li>The parameter --fastq2 can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_end<br/>&nbsp;- --input_type is set to barcode_in_label</li>" .
//			"<li>The parameter --char_delineator can only be used when:<br/>&nbsp;- --input_type is set to barcode_in_label</li>" . 
			"<li>The parameter --switch_bc_order can only be used when:<br/>&nbsp;- --input_type is set to barcode_paired_stitched</li>" . 
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_in_label";
		$input['--fastq2'] = true;
		$input['--char_delineator'] = true;
		$input['--switch_bc_order'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testInputType_barcodeInLabel_zeroDependentsPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_in_label";
		unset($input['--fastq2']);
		unset($input['--bc2_len']);
		unset($input['--rev_comp_bc2']);
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}

	public function testBc2LenAndRevCompBc2_fastq2NotPresent_inputTypeNotStitched() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --bc2_len can only be used when:<br/>&nbsp;- --fastq2 is set<br/>&nbsp;- --input_type is set to barcode_paired_stitched</li>" .
			"<li>The parameter --rev_comp_bc2 can only be used when:<br/>&nbsp;- --fastq2 is set<br/>&nbsp;- --input_type is set to barcode_paired_stitched</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_single_end";
		unset($input['--fastq2']);
		$input['--bc2_len'] = true;
		$input['--rev_comp_bc2'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testBc2LenAndRevCompBc2_fastq2Present_inputTypeNotStitched() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_end";
		$input['--fastq2'] = true;
		$input['--bc2_len'] = true;
		$input['--rev_comp_bc2'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}
	public function testBc2LenAndRevCompBc2_fastq2NotPresent_inputTypeStitched() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_stitched";
		unset($input['--fastq2']);
		$input['--bc2_len'] = true;
		$input['--rev_comp_bc2'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}

	public function testDisableHeaderMatch_fastq2Present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_end";
		$input['--fastq2'] = true;
		$input['--disable_header_match'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}
	public function testDisableHeaderMatch_fastq2NotPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --disable_header_match can only be used when:<br/>&nbsp;- --fastq2 is set</li>" . $this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--input_type'] = "barcode_paired_end";
		unset($input['--fastq2']);
		$input['--disable_header_match'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testAttemptReadReorientation_mappingFpPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		$input['--mapping_fp'] = true;
		$input['--attempt_read_reorientation'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception: {$ex->getMessage()}");
		}
	}
	public function testAttemptReadReorientation_mappingFpNotPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --attempt_read_reorientation can only be used when:<br/>&nbsp;- --mapping_fp is set</li>" . $this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--fastq1'] = true;
		unset($input['--mapping_fp']);
		$input['--attempt_read_reorientation'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown exception");
		}
		catch (ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
}
