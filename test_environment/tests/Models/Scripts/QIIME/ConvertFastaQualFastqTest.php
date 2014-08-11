<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class ConvertFastaQualFastqTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ConvertFastqQualFastqTest");
	}

	private $defaultValue = 1;

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--fasta_file_path" => "",
		"--conversion_type" => "",
		"--qual_file_path" => "",
		"--full_fastq" => "",
		"--ascii_increment" => "",
		"--full_fasta_headers" => "",
		"--multiple_output_files" => "",
		"--output_dir" => "",
		"--verbose" => "",
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
		$this->object = new \Models\Scripts\QIIME\ConvertFastaQualFastq($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ConvertFastaQualFastq::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "convert_fastaqual_fastq.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ConvertFastaQualFastq::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Convert between fasta/qual and fastq";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ConvertFastaQualFastq::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "convert_fasta_qual_fastq";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testFastaFilePath_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fasta_file_path'] = true;

		$this->object->acceptInput($input);

	}
	public function testFastaFilePath_notPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --fasta_file_path is required</li>" . $this->errorMessageOutro;
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

	public function testConversionType_fastaqualToFastq_dependentsPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fasta_file_path'] = true;
		$input['--conversion_type'] = "fastaqual_to_fastq";
		$input['--qual_file_path'] = true;
		$input['--full_fastq'] = true;

		$this->object->acceptInput($input);

	}
	public function testConversionType_fastaqualToFastq_dependentsNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fasta_file_path'] = true;
		$input['--conversion_type'] = "fastaqual_to_fastq";
		unset($input['--qual_file_path']);
		unset($input['--full_fastq']);

		$this->object->acceptInput($input);

	}
	public function testConversionType_fastqToFastaqual_dependentsPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --qual_file_path can only be used when:<br/>&nbsp;- --conversion_type is set to fastaqual_to_fastq</li>" .
			"<li>The parameter --full_fastq can only be used when:<br/>&nbsp;- --conversion_type is set to fastaqual_to_fastq</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--fasta_file_path'] = true;
		$input['--conversion_type'] = "fastq_to_fastaqual";
		$input['--qual_file_path'] = true;
		$input['--full_fastq'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testConversionType_fastqToFastaqual_dependentsNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--fasta_file_path'] = true;
		$input['--conversion_type'] = "fastq_to_fastaqual";
		unset($input['--qual_file_path']);
		unset($input['--full_fastq']);

		$this->object->acceptInput($input);

	}
}
