<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;
use Models\Scripts\Parameters\DefaultParameter;

class SplitLibrariesFastqTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("SplitLibrarariesFastqTest");
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
		$this->object = new \Models\Scripts\QIIME\SplitLibrariesFastq($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\SplitLibrariesFastq::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "split_libraries_fastq.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\SplitLibrariesFastq::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "De-multiplex fastq";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\SplitLibrariesFastq::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "split_libraries_fastq";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testRequireds_present() {
		$input = array();
		$input['--sequence_read_fps'] = true;
		$input['--mapping_fps'] = true;
		$input['--output_dir'] = true;

		$this->object->acceptInput($input);

	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --sequence_read_fps is required</li>" .
			"<li>The parameter --mapping_fps is required</li>" .
			"<li>The parameter --output_dir is required</li>" .
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
