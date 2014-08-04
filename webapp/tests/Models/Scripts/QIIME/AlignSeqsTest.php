<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class AlignSeqsTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("AlignSeqsTest");
	}

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

	public function testInputFp_absent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --input_fasta_fp is required</li>" . $this->errorMessageOutro;
		$input = $this->emptyInput;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}
	public function testInputFp_present() {
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should not have thrown exception");
		}
	}

	public function testVersion_present() {
		$expected = $this->errorMessageIntro . "<li>The parameter --version can only be used when:</li>" . $this->errorMessageOutro;
		$input['--input_fasta_fp'] = true;
		$input['--version'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}

	public function testHelp_present() {
		$expected = $this->errorMessageIntro . "<li>The parameter --help can only be used when:</li>" . $this->errorMessageOutro;
		$input['--input_fasta_fp'] = true;
		$input['--help'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}

	public function testBlastDb_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "pynast";
		$input['--blast_db'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should have not thrown an exception");
		}
	}
	public function testBlastDb_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --blast_db can only be used when:<br/>&nbsp;- --alignment_method is set to pynast</li>" . $this->errorMessageOutro;
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--blast_db'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}

	public function testPairwiseAlignmentMethod_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "pynast";
		$input['--pairwise_alignment_method'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should have not thrown an exception");
		}
	}
	public function testPairwiseAlignmentMethod_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --pairwise_alignment_method can only be used when:<br/>&nbsp;- --alignment_method is set to pynast</li>" . $this->errorMessageOutro;
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--pairwise_alignment_method'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}

	public function testMinPercentId_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "pynast";
		$input['--min_percent_id'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should have not thrown an exception");
		}
	}
	public function testMinPercentId_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --min_percent_id can only be used when:<br/>&nbsp;- --alignment_method is set to pynast</li>" . $this->errorMessageOutro;
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--min_percent_id'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}

	public function testMuscleMaxMemory_whileAllowed() {
		$expected = "";
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "muscle";
		$input['--muscle_max_memory'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$this->fail("acceptInput should have not thrown an exception");
		}
	}
	public function testMuscleMaxMemory_whileNotAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --muscle_max_memory can only be used when:<br/>&nbsp;- --alignment_method is set to muscle</li>" . $this->errorMessageOutro;
		$input['--input_fasta_fp'] = true;
		$input['--alignment_method'] = "infernal";
		$input['--muscle_max_memory'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
			$this->assertEquals($expected, $actual);
		}
	}
}
