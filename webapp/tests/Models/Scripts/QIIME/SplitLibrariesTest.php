<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;
use Models\Scripts\Parameters\DefaultParameter;

class SplitLibrariresTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("SplitLibrariesTest");
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
		$this->object = new \Models\Scripts\QIIME\SplitLibraries($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\SplitLibraries::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "split_libraries.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\SplitLibraries::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "De-multiplex libraries";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\SplitLibraries::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "split_libraries";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testRequireds_present() {
		$expected = $this->errorMessageIntro . 
			"<li>Since __--barcode-type__-b__ is set to -b, that parameter must be specified.</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--map'] = true;
		$input['--fasta'] = true;
		$input['__--barcode-type__-b__'] = "-b";
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
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
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

	public function testReversePrimers_truncateOnly_triggerIsOnValue() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--reverse_primers'] = "truncate_only";
		$input['--reverse_primer_mismatches'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReversePrimers_truncateRemove_triggerIsOnValue() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--reverse_primers'] = "truncate_remove";
		$input['--reverse_primer_mismatches'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReversePrimers_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			"<li>An invalid value was provided for the parameter: --reverse_primers</li>" .
			"<li>The parameter --reverse_primer_mismatches can only be used when:<br/>&nbsp;- --reverse_primers is set to truncate_only<br/>&nbsp;- --reverse_primers is set to truncate_remove</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--reverse_primers'] = "invalide_value";
		$input['--reverse_primer_mismatches'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testQual_triggerIsPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--qual'] = true;
		$input['--min-qual-score'] = true;
		$input['--record_qual_scores'] = true;
		$input['--qual_score_window'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testQual_triggerIsNotPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			"<li>The parameter --min-qual-score can only be used when:<br/>&nbsp;- --qual is set</li>" .
			"<li>The parameter --record_qual_scores can only be used when:<br/>&nbsp;- --qual is set</li>" .
			"<li>The parameter --qual_score_window can only be used when:<br/>&nbsp;- --qual is set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--min-qual-score'] = true;
		$input['--record_qual_scores'] = true;
		$input['--qual_score_window'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testQualScoreWindow_0_triggerIsOnValue() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			"<li>The parameter --discard_bad_windows cannot be used when:<br/>&nbsp;- --qual_score_window is set to 0</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--qual'] = true;
		$input['--qual_score_window'] = 0;
		$input['--discard_bad_windows'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testQualScoreWindow_0_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --map is required</li>" .
			"<li>The parameter --fasta is required</li>" .
			"<li>The parameter __--barcode-type__-b__ is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--qual'] = true;
		$input['--qual_score_window'] = 1;
		$input['--discard_bad_windows'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
