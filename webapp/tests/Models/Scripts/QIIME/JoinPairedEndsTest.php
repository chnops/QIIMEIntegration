<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class JoinPairedEndsTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("JoinPairedEndsTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--forward_reads_fp" => "",
		"--reverse_reads_fp" => "",
		"--output_dir" => "",
		"--index_reads_fp" => "",
		"--min_overlap" => "",
		"--pe_join_method" => "",
		"-perc_max_diff" => "",
		"--max_ascii_score" => "",
		"--min_frac_match" => "",
		"--max_good_mismatch" => "",
		"--phred_64" => "",
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
		$this->object = new \Models\Scripts\QIIME\JoinPairedEnds($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\JoinPairedEnds::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "join_paired_ends.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\JoinPairedEnds::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Join paired ends";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\JoinPairedEnds::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "join_paired_ends";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testRequired_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--forward_reads_fp'] = true;
		$input['--reverse_reads_fp'] = true;
		$input['--output_dir'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown an exception: {$ex->getMessage()}");
		}
	}
	public function testRequired_notPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --forward_reads_fp is required</li>" .
			"<li>The parameter --reverse_reads_fp is required</li>" .
			"<li>The parameter --output_dir is required</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		unset($input['--forward_reads_fp']);
		unset($input['--reverse_reads_fp']);
		unset($input['--output_dir']);
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}

	public function testPeJoinMethod_fastqJoin_someDependentsPresent() {
		$expected = $this->errorMessageIntro .
//			"<li>The parameter --perc_max_diff can only be used when:<br/>&nbsp;- --pe_join_method is set to fastq-join</li>" .
			"<li>The parameter --max_ascii_score can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
			"<li>The parameter --min_frac_match can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
			"<li>The parameter --max_good_mismatch can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
			"<li>The parameter --phred_64 can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--forward_reads_fp'] = true;
		$input['--reverse_reads_fp'] = true;
		$input['--output_dir'] = true;
		$input['--pe_join_method'] = "fastq-join";
		$input['--perc_max_diff'] = true;
		$input['--max_ascii_score'] = true;
		$input['--min_frac_match'] = true;
		$input['--max_good_mismatch'] = true;
		$input['--phred_64'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testPeJoinMethod_fastqJoin_noDependentsPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--forward_reads_fp'] = true;
		$input['--reverse_reads_fp'] = true;
		$input['--output_dir'] = true;
		$input['--pe_join_method'] = "fastq-join";
		unset($input['--perc_max_diff']);
		unset($input['--max_ascii_score']);
		unset($input['--min_frac_match']);
		unset($input['--max_good_mismatch']);
		unset($input['--phred_64']);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown an exception: {$ex->getMessage()}");
		}
	}
	public function testPeJoinMethod_seqPrep_someDependentsPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --perc_max_diff can only be used when:<br/>&nbsp;- --pe_join_method is set to fastq-join</li>" .
//			"<li>The parameter --max_ascii_score can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
//			"<li>The parameter --min_frac_match can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
//			"<li>The parameter --max_good_mismatch can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
//			"<li>The parameter --phred_64 can only be used when:<br/>&nbsp;- --pe_join_method is set to SeqPrep</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--forward_reads_fp'] = true;
		$input['--reverse_reads_fp'] = true;
		$input['--output_dir'] = true;
		$input['--pe_join_method'] = "SeqPrep";
		$input['--perc_max_diff'] = true;
		$input['--max_ascii_score'] = true;
		$input['--min_frac_match'] = true;
		$input['--max_good_mismatch'] = true;
		$input['--phred_64'] = true;
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
	public function testPeJoinMethod_seqPrep_noDependentsPresent() {	
		$expected = "";
		$input = $this->emptyInput;
		$input['--forward_reads_fp'] = true;
		$input['--reverse_reads_fp'] = true;
		$input['--output_dir'] = true;
		$input['--pe_join_method'] = "SeqPrep";
		unset($input['--perc_max_diff']);
		unset($input['--max_ascii_score']);
		unset($input['--min_frac_match']);
		unset($input['--max_good_mismatch']);
		unset($input['--phred_64']);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown an exception: {$ex->getMessage()}");
		}
	}
}
