<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class MakeOtuTableTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("MakeOtuTableTest");
	}

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--otu_map_fp" => "",
		"--output_biom_fp" => "",
		"--taxonomy" => "",
		"--exclude_otus_fp" => "",
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
		$this->object = new \Models\Scripts\QIIME\MakeOtuTable($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\MakeOtuTable::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "make_otu_table.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\MakeOtuTable::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Make OTU table";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\MakeOtuTable::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "make_otu_table";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testRequireds_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--otu_map_fp'] = true;
		$input['--output_biom_fp'] = true;

		$this->object->acceptInput($input);

	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --otu_map_fp is required</li>" .
			"<li>The parameter --output_biom_fp is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		unset($input['--otu_map_fp']);
		unset($input['--output_biom_fp']);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
