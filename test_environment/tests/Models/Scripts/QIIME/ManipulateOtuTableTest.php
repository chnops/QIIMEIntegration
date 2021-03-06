<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;
use Models\Scripts\Parameters\DefaultParameter;

class ManiuplateOtuTableTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ManipulateOtuTableTest");
	}

	private $defaultValue = 1;

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--input-fp" => "",
		"--output-fp" => "",
		"--qualitative" => "",
		"--suppress-md5" => "",
		"--sparse-biom-to-dense-biom" => "",
		"--dense-biom-to-sparse-biom" => "",
		"--biom-to-classic-table" => "",
		"--sample-metadata-fp" => "",
		"--matrix-type" => "",
		"--header-key" => "",
		"--output-metadata-id" => "",
		"--process-obs-metadata" => "",
		"--table-type" => "",
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
		$this->object = new \Models\Scripts\QIIME\ManipulateOtuTable($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "biom";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Manipulate OTU table";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "manipulate_table";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testScriptExists() {
		$expecteds = array(
			"script_location" => "/macqiime/bin/{$this->object->getScriptName()}",
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

	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::renderCommand
	 */
	public function testRenderCommand_noParameters() {
		$expected = "biom ";
		$this->object = $this->getMockBuilder('\Models\Scripts\QIIME\ManipulateOtuTable')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMock();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array()));

		$actual = $this->object->renderCommand();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::renderCommand
	 */
	public function testRenderCommand_oneAction() {
		$expected = "biom doSomething --name='value' ";
		$parameter = new DefaultParameter("--name", "value");
		$action = new DefaultParameter("action", "doSomething");
		$this->object = $this->getMockBuilder('\Models\Scripts\QIIME\ManipulateOtuTable')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMock();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array($parameter, $action)));

		$actual = $this->object->renderCommand();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::renderCommand
	 */
	public function testRenderCommand_twoActions() {
		$expected = "biom value doSomething ";
		$parameter = new DefaultParameter("action", "value");
		$action = new DefaultParameter("action", "doSomething");
		$this->object = $this->getMockBuilder('\Models\Scripts\QIIME\ManipulateOtuTable')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMock();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array($parameter, $action)));

		$actual = $this->object->renderCommand();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::renderVersionCommand
	 */
	public function testRenderVersionCommand_noParameters() {
		$expected = "true";
		$this->object = $this->getMockBuilder('\Models\Scripts\QIIME\ManipulateOtuTable')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMock();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array()));

		$actual = $this->object->renderVersionCommand();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::renderVersionCommand
	 */
	public function testRenderVersionCommand_oneAction() {
		$expected = "biom doSomething --version";
		$parameter = new DefaultParameter("--name", "value");
		$action = new DefaultParameter("action", "doSomething");
		$this->object = $this->getMockBuilder('\Models\Scripts\QIIME\ManipulateOtuTable')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMock();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array($parameter, $action)));

		$actual = $this->object->renderVersionCommand();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\QIIME\ManipulateOtuTable::renderVersionCommand
	 */
	public function testRenderVersionCommand_twoActions() {
		$expected = "biom value --version";
		$parameter = new DefaultParameter("action", "value");
		$action = new DefaultParameter("action", "doSomething");
		$this->object = $this->getMockBuilder('\Models\Scripts\QIIME\ManipulateOtuTable')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMock();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array($parameter, $action)));

		$actual = $this->object->renderVersionCommand();

		$this->assertEquals($expected, $actual);
	}

	public function testRequireds_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['action'] = "convert";
		$input['--table-type'] = "otu table";

		$this->object->acceptInput($input);

	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input-fp is required</li>" . 
			"<li>The parameter --output-fp is required</li>" . 
			"<li>The parameter --table-type is required when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is not set</li>" . 
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		unset($input['--input-fp']);
		unset($input['--output-fp']);
		unset($input['--table-type']);
		try {

			$this->object->acceptInput($input);

		}
		catch(scriptexception $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testActionSummarize_dependents() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
//		$input['--table-type'] = true;
		$input['action'] = "summarize-table";
		$input['--qualitative'] = true;
		$input['--suppress-md5'] = true;

		$this->object->acceptInput($input);

	}
	public function testActionSummarize_nonDependents() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ can only be used when:<br/>&nbsp;- action is set to convert</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
//		$input['--table-type'] = true;
		$input['action'] = "summarize-table";
		unset($input['--qualitative']);
		unset($input['--suppress-md5']);
		$input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____'] = "--biom-to-classic-table";
		try{

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();		
		}
		$this->assertEquals($expected, $actual);
	}

	public function testActionConvert_nonDependents() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --qualitative can only be used when:<br/>&nbsp;- action is set to summarize-table</li>" .
			"<li>The parameter --suppress-md5 can only be used when:<br/>&nbsp;- action is set to summarize-table</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['--table-type'] = "otu table";
		$input['action'] = "convert";
		$input['--qualitative'] = true;
		$input['--suppress-md5'] = true;
		unset($input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____']);
		try{

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();		
		}
		$this->assertEquals($expected, $actual);
	}

	public function testConversionType_noConversionType_dependents() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['action'] = "convert";
		unset($input['--qualitative']);
		unset($input['--suppress-md5']);
		unset($input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____']);
		$input['--sample-metadata-fp'] = true;
		$input['--matrix-type'] = "sparse";
		$input['--process-obs-metadata'] = "naive";
		$input['--table-type'] = "otu table";
		
		$this->object->acceptInput($input);

	}
	public function testConversionType_noConversionType_nonDependents() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --header-key can only be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set to --biom-to-classic-table</li>" .
			"<li>The parameter --output-metadata-id can only be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set to --biom-to-classic-table</li>" .
			"<li>The parameter --table-type is required when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is not set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['action'] = "convert";
		unset($input['--qualitative']);
		unset($input['--suppress-md5']);
		unset($input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____']);
		unset($input['--sample-metadata-fp']);
		unset($input['--matrix-type']);
		unset($input['--process-obs-metadata']);
		unset($input['--table-type']);
		$input['--header-key'] = $this->defaultValue;
		$input['--output-metadata-id'] = $this->defaultValue;
		try {
		
			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	
	public function testConversionType_biomToClassicConversionType_dependents() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['action'] = "convert";
		unset($input['--qualitative']);
		unset($input['--suppress-md5']);
		$input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____'] = "--biom-to-classic-table";
		$input['--biom-to-classic-table'] = true;
		$input['--header-key'] = $this->defaultValue;
		$input['--output-metadata-id'] = $this->defaultValue;
		unset($input['--sample-metadata-fp']);
		unset($input['--matrix-type']);
		unset($input['--process-obs-metadata']);
		unset($input['--table-type']);
		
		$this->object->acceptInput($input);

	}
	public function testConversionType_biomToClassicConversionType_nonDependents() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --matrix-type cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			"<li>The parameter --sample-metadata-fp cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			"<li>The parameter --process-obs-metadata cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			"<li>The parameter --table-type cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['action'] = "convert";
		unset($input['--qualitative']);
		unset($input['--suppress-md5']);
		$input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____'] = "--biom-to-classic-table";
		$input['--biom-to-classic-table'] = true;
		unset($input['--header-key']);
		unset($input['--output-metadata-id']);
		$input['--sample-metadata-fp'] = $this->defaultValue;
		$input['--matrix-type'] = "sparse";
		$input['--process-obs-metadata'] = "naive";
		$input['--table-type'] = "otu table";
		try {
		
			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testConversionType_sparseToDenseConversionType_nonDependents() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --matrix-type cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			"<li>The parameter --sample-metadata-fp cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			"<li>The parameter --header-key can only be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set to --biom-to-classic-table</li>" .
			"<li>The parameter --output-metadata-id can only be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set to --biom-to-classic-table</li>" .
			"<li>The parameter --process-obs-metadata cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			"<li>The parameter --table-type cannot be used when:<br/>&nbsp;- __--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____ is set</li>" .
			$this->errorMessageOutro;
		$input = $this->emptyInput;
		$input['--input-fp'] = true;
		$input['--output-fp'] = true;
		$input['action'] = "convert";
		unset($input['--qualitative']);
		unset($input['--suppress-md5']);
		$input['__--biom-to-classic-table____--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom____'] = "__--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom__";
		$input['__--sparse-biom-to-dense-biom__--dense-biom-to-sparse-biom__'] = "--sparse-biom-to-dense-biom";
		$input['--sparse-biom-to-dense-biom'] = true;
		unset($input['--biom-to-classic-table']);
		unset($input['--dense-biom-to-sparse-biom']);
		$input['--header-key'] = $this->defaultValue;
		$input['--output-metadata-id'] = $this->defaultValue;
		$input['--sample-metadata-fp'] = true;
		$input['--matrix-type'] = "sparse";
		$input['--process-obs-metadata'] = "naive";
		$input['--table-type'] = "otu table";
		try {
		
			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
