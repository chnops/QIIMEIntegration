<?php

namespace Models\Scripts\Parameters;

class OldFileParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("OldFileParameterTest");
	}
	public static function tearDownAfterClass() {
		\Utils\Helper::setDefaultHelper(NULL);
	}

	private $mockProject = NULL;
	private $mockScript = NULL; 
	private $name = "--old_file";
	private $value = "./path/file.ext";
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}


	public function setUp() {
		\Utils\Helper::setDefaultHelper(NULL);
		$this->object = new OldFileParameter($this->name, $this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::__construct
	 */
	public function testConstructor_noDefault() {
		$expecteds = array(
			"name" => $this->name,
			"project" => $this->mockProject,
			"value" => "",
		);
		$actuals = array();

		$this->object = new OldFileParameter($this->name, $this->mockProject);

		$actuals['name'] = $this->object->getName();
		$actuals['project'] = $this->object->getProject();
		$actuals['value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::__construct
	 */
	public function testConstructor_default() {
		$expecteds = array(
			"name" => $this->name,
			"project" => $this->mockProject,
			"value" => $this->value,
		);
		$actuals = array();

		$this->object = new OldFileParameter($this->name, $this->mockProject, $this->value);

		$actuals['name'] = $this->object->getName();
		$actuals['project'] = $this->object->getProject();
		$actuals['value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::getProject
	 */
	public function testGetProject() {
		$expected = $this->mockProject;

		$actual = $this->object->getProject();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::setProject
	 */
	public function testSetProject() {
		$expected = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->object->setProject($expected);

		$actual = $this->object->getProject();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::renderForForm
	 */
	public function testRenderForForm_disabled_allFileTypesEmpty() {
		$expectedJsVar = "js_script_param";
		$expected = "<label for=\"{$this->name}\"><em>You must <a href=\"index.php?step=upload\">upload</a>
			at least one file in order to use {$this->name}<em></label>";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllBuiltInFiles")->will($this->returnValue(array()));
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\OldFileParameter')
			->setConstructorArgs(array($this->name, $mockProject))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_uploadedEmpty_generatedEmpty_builtInNotEmpty() {
		$expectedBuiltInFiles = array(
			"not_" . $this->value,
			$this->value,
			"definitely_not_" . $this->value
		);
		$expectedJsVar = "js_script_param";
		$expected = "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><select name=\"{$this->name}\" size=\"5\" disabled>\n" .
			"<optgroup label=\"built in files\" class=\"big\">\n" . 
			"<optgroup>\n" . 
			"<option value=\"not_{$this->value}\">not_{$this->value}</option>\n" .
			"<option value=\"{$this->value}\" selected>{$this->value}</option>\n" .
			"<option value=\"definitely_not_{$this->value}\">definitely_not_{$this->value}</option>\n" .
			"</optgroup>\n" .
			"</select>\n" . 
			"</label>";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllBuiltInFiles")->will($this->returnValue($expectedBuiltInFiles));
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\OldFileParameter')
			->setConstructorArgs(array($this->name, $mockProject, $this->value))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_uploadedEmpty_generatedNotEmpty_builtInEmpty() {
		$expectedGeneratedFiles = array(
			1 => array(
				"not_" . $this->value,
			),
			2 => array(
				"definitely_not_" . $this->value,
				$this->value,
			),
		);
		$expectedBuiltInFiles = array(
		);
		$expectedJsVar = "js_script_param";
		$expected = "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><select name=\"{$this->name}\" size=\"5\" disabled>\n" .
			"<optgroup label=\"generated files\" class=\"big\">\n" .
			"<optgroup label=\"from run 1\">\n" .
				"<option value=\"../r1/not_{$this->value}\">generated/not_{$this->value}</option>\n" .
			"</optgroup>\n" .
			"<optgroup label=\"from run 2\">\n" .
				"<option value=\"../r2/{$this->value}\" selected>generated/{$this->value}</option>\n" .
				"<option value=\"../r2/definitely_not_{$this->value}\">generated/definitely_not_{$this->value}</option>\n" .
			"</optgroup>\n" .
			"</select>\n" . 
			"</label>";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->exactly(3))->method("htmlentities")->will($this->returnArgument(0));
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue($expectedGeneratedFiles));
		$mockProject->expects($this->once())->method("retrieveAllBuiltInFiles")->will($this->returnValue($expectedBuiltInFiles));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\OldFileParameter')
			->setConstructorArgs(array($this->name, $mockProject, "../r2/{$this->value}"))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_uploadedNotEmpty_generatedEmpty_builtInEmpty() {
		$expectedUploadedFiles = array(
			"type1" => array(
				"not_" . $this->value,
			),
			"type2" => array(
				"definitely_not_" . $this->value,
				$this->value,
			),
		);
		$expectedGeneratedFiles = array(
		);
		$expectedBuiltInFiles = array(
		);
		$expectedJsVar = "js_script_param";
		$expected = "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><select name=\"{$this->name}\" size=\"5\" disabled>\n" .
			"<option value=\"\">None</option>" .
			"<optgroup label=\"uploaded files\" class=\"big\">\n" .
			"<optgroup label=\"type1 files\">\n" .
				"<option value=\"../uploads/not_{$this->value}\">uploads/not_{$this->value}</option>\n" .
			"</optgroup>\n" .
			"<optgroup label=\"type2 files\">\n" .
				"<option value=\"../uploads/{$this->value}\" selected>uploads/{$this->value}</option>\n" .
				"<option value=\"../uploads/definitely_not_{$this->value}\">uploads/definitely_not_{$this->value}</option>\n" .
			"</optgroup>\n" .
			"</select>\n" .
			"</label>";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->exactly(3))->method("htmlentities")->will($this->returnArgument(0));
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($expectedUploadedFiles));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue($expectedGeneratedFiles));
		$mockProject->expects($this->once())->method("retrieveAllBuiltInFiles")->will($this->returnValue($expectedBuiltInFiles));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\OldFileParameter')
			->setConstructorArgs(array($this->name, $mockProject, "../uploads/{$this->value}"))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_noFileTypesEmpty_withDefault() {
		$expectedUploadedFiles = array(
			"type1" => array(
				$this->value,
			),
		);
		$expectedGeneratedFiles = array(
			1 => array(
				$this->value,
			),
		);
		$expectedBuiltInFiles = array(
			$this->value,
		);
		$expectedJsVar = "js_script_param";
		$expected = "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><select name=\"{$this->name}\" size=\"5\" disabled>\n" .
			"<option value=\"\">None</option>" .
			"<optgroup label=\"uploaded files\" class=\"big\">\n" .
			"<optgroup label=\"type1 files\">\n" .
				"<option value=\"../uploads/{$this->value}\">uploads/{$this->value}</option>\n" .
			"</optgroup>\n" .
			"<optgroup label=\"generated files\" class=\"big\">\n" .
			"<optgroup label=\"from run 1\">\n" .
				"<option value=\"../r1/{$this->value}\">generated/{$this->value}</option>\n" .
			"</optgroup>\n" .
			"<optgroup label=\"built in files\" class=\"big\">\n" . 
			"<optgroup>\n" . 
			"<option value=\"{$this->value}\" selected>{$this->value}</option>\n" .
			"</optgroup>\n" .
			"</select>\n" .
			"</label>";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$mockHelper->expects($this->exactly(2))->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->exactly(3))->method("htmlentities")->will($this->returnArgument(0));
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($expectedUploadedFiles));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue($expectedGeneratedFiles));
		$mockProject->expects($this->once())->method("retrieveAllBuiltInFiles")->will($this->returnValue($expectedBuiltInFiles));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\OldFileParameter')
			->setConstructorArgs(array($this->name, $mockProject, $this->value))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\OldFileParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_noFileTypesEmpty_withNoDefault() {
		$expectedUploadedFiles = array(
			"type1" => array(
				$this->value,
			),
		);
		$expectedGeneratedFiles = array(
			1 => array(
				$this->value,
			),
		);
		$expectedBuiltInFiles = array(
			$this->value,
		);
		$expectedJsVar = "js_script_param";
		$expected = "<label for=\"{$this->name}\">{$this->name} <a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><select name=\"{$this->name}\" size=\"5\">\n" .
			"<option value=\"\">None</option>" .
			"<optgroup label=\"uploaded files\" class=\"big\">\n" .
			"<optgroup label=\"type1 files\">\n" .
				"<option value=\"../uploads/{$this->value}\">uploads/{$this->value}</option>\n" .
			"</optgroup>\n" .
			"<optgroup label=\"generated files\" class=\"big\">\n" .
			"<optgroup label=\"from run 1\">\n" .
				"<option value=\"../r1/{$this->value}\">generated/{$this->value}</option>\n" .
			"</optgroup>\n" .
			"<optgroup label=\"built in files\" class=\"big\">\n" . 
			"<optgroup>\n" . 
			"<option value=\"{$this->value}\">{$this->value}</option>\n" .
			"</optgroup>\n" .
			"</select>\n" .
			"</label>";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$mockHelper->expects($this->exactly(2))->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->exactly(3))->method("htmlentities")->will($this->returnArgument(0));
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($expectedUploadedFiles));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue($expectedGeneratedFiles));
		$mockProject->expects($this->once())->method("retrieveAllBuiltInFiles")->will($this->returnValue($expectedBuiltInFiles));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\OldFileParameter')
			->setConstructorArgs(array($this->name, $mockProject))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
}
