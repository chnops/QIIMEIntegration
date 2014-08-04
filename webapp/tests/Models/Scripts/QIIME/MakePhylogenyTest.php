<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class MakePhylogenyTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("MakePhylogenyTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--input_fp" => "",
		"--tree_method" => "",
		"--root_method" => "",
		"--verbose" => "",
		"--result_fp" => "",
		"--log_fp" => "",
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
		$this->object = new \Models\Scripts\QIIME\MakePhylogeny($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\MakePhylogeny::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "make_phylogeny.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\MakePhylogeny::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Make phylogenetic tree";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\MakePhylogeny::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "make_phylogeny";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testRequireds_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown an exception");
		}
	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_fp is required</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		unset($input['--input_fp']);
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$this->assertEquals($expected, $ex->getMessage());
		}
	}
}
