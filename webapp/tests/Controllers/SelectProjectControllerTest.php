<?php

namespace Controllers;

class SelectProjectControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("SelectProjectController");
	}

	public $mockWorkflow = NULL;
	public $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getStep"))
			->getMock();
		$this->mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("select"));
	}
	public function setUp() {
		$_SESSION = array();
		$_POST = array();
		$this->object = new SelectProjectController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\SelectProjectController::getSubtitles
	 */
	public function testGetSubtitle() {
		$expected = "Select a Project";
		$actual = $this->object->getSubtitle();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::retrievePastResults
	 */
	public function testRetrievePastResults() {

		$actual = $this->object->retrievePastResults();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::getProjects
	 */
	public function testGetProjects() {

		$actual = $this->object->getProjects();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::setProjects
	 */
	public function testSetProjects() {
		$expected = array(1, 2, 3);

		$this->object->setProjects($expected);

		$actual = $this->object->getProjects();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\SelectProjectController::parseInput
	 */
	public function testParseInput_notLoggedIn() {
		$username = null;
		$_POST = array(
			"project" => "name",
			"create" => false,
		);
		$expecteds = array(
			"disabled" => " disabled",
			"is_result_error" => true,
			"result" => "You cannot choose a project if you aren't logged in.",
		);
		$actuals = array();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockWorkflow->expects($this->never())->method("getAllProjects");
		$this->object = $this->getMockBuilder('\Controllers\SelectProjectController')
			->setConstructorArgs(array($mockWorkflow))
			->setMethods(array("projectNameExists", "createProject", "projectIdExists", "selectProject"))
			->getMock();
		$this->object->expects($this->never())->method("projectNameExists");
		$this->object->expects($this->never())->method("createProject");
		$this->object->expects($this->never())->method("projectIdExists");
		$this->object->expects($this->never())->method("selectProject");
		$this->object->setUsername($username);

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::parseInput
	 */
	public function testParseInput_projectNotSet() {
		$username = "username";
		$_POST = array(
			//"project" => "name",
			"create" => false,
		);
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => false,
			"result" => "",
		);
		$actuals = array();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getAllProjects");
		$this->object = $this->getMockBuilder('\Controllers\SelectProjectController')
			->setConstructorArgs(array($mockWorkflow))
			->setMethods(array("projectNameExists", "createProject", "projectIdExists", "selectProject"))
			->getMock();
		$this->object->expects($this->never())->method("projectNameExists");
		$this->object->expects($this->never())->method("createProject");
		$this->object->expects($this->never())->method("projectIdExists");
		$this->object->expects($this->never())->method("selectProject");
		$this->object->setUsername($username);

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::parseInput
	 */
	public function testParseInput_create_projectExists() {
		$username = "username";
		$_POST = array(
			"project" => "name",
			"create" => true,
		);
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "A project with that name already exists. Did you mean to select it?",
		);
		$actuals = array();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getAllProjects");
		$this->object = $this->getMockBuilder('\Controllers\SelectProjectController')
			->setConstructorArgs(array($mockWorkflow))
			->setMethods(array("projectNameExists", "createProject", "projectIdExists", "selectProject"))
			->getMock();
		$this->object->expects($this->once())->method("projectNameExists")->will($this->returnValue(true));
		$this->object->expects($this->never())->method("createProject");
		$this->object->expects($this->never())->method("projectIdExists");
		$this->object->expects($this->never())->method("selectProject");
		$this->object->setUsername($username);

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::parseInput
	 */
	public function testParseInput_create_projectDoesNotExist() {
		$username = "username";
		$_POST = array(
			"project" => "name",
			"create" => true,
		);
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => false,
			"result" => "",
		);
		$actuals = array();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getAllProjects");
		$this->object = $this->getMockBuilder('\Controllers\SelectProjectController')
			->setConstructorArgs(array($mockWorkflow))
			->setMethods(array("projectNameExists", "createProject", "projectIdExists", "selectProject"))
			->getMock();
		$this->object->expects($this->once())->method("projectNameExists")->will($this->returnValue(false));
		$this->object->expects($this->once())->method("createProject");
		$this->object->expects($this->never())->method("projectIdExists");
		$this->object->expects($this->never())->method("selectProject");
		$this->object->setUsername($username);

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::parseInput
	 */
	public function testParseInput_select_projectExists() {
		$username = "username";
		$_POST = array(
			"project" => 1,
			"create" => false,
		);
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => false,
			"result" => "",
		);
		$actuals = array();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getAllProjects");
		$this->object = $this->getMockBuilder('\Controllers\SelectProjectController')
			->setConstructorArgs(array($mockWorkflow))
			->setMethods(array("projectNameExists", "createProject", "projectIdExists", "selectProject"))
			->getMock();
		$this->object->expects($this->never())->method("projectNameExists");
		$this->object->expects($this->never())->method("createProject");
		$this->object->expects($this->once())->method("projectIdExists")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("selectProject");
		$this->object->setUsername($username);

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::parseInput
	 */
	public function testParseInput_select_projectDoesNotExist() {
		$username = "username";
		$_POST = array(
			"project" => 1,
			"create" => false,
		);
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "No project with that name exists. Did you mean to create it?",
		);
		$actuals = array();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getAllProjects");
		$this->object = $this->getMockBuilder('\Controllers\SelectProjectController')
			->setConstructorArgs(array($mockWorkflow))
			->setMethods(array("projectNameExists", "createProject", "projectIdExists", "selectProject"))
			->getMock();
		$this->object->expects($this->never())->method("projectNameExists");
		$this->object->expects($this->never())->method("createProject");
		$this->object->expects($this->once())->method("projectIdExists")->will($this->returnValue(false));
		$this->object->expects($this->never())->method("selectProject");
		$this->object->setUsername($username);

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controllers\SelectProjectController::projectNameExists
	 */
	public function testProjectNameExists_noProjects() {
		$projectName = "projectName";
		$projects = array();
		$this->object->setProjects($projects);

		$actual = $this->object->projectNameExists($projectName);

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::projectNameExists
	 */
	public function testProjectNameExists_doesNotExist() {
		$projectName = "projectName";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName"))
			->getMockForAbstractClass();
		$mockProject->expects($this->any())->method("getName")->will($this->returnValue("not" . $projectName));
		$projects = array($mockProject, $mockProject);
		$this->object->setProjects($projects);

		$actual = $this->object->projectNameExists($projectName);

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::projectNameExists
	 */
	public function testProjectNameExists_doesExist() {
		$projectName = "projectName";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName"))
			->getMockForAbstractClass();
		$mockProject->expects($this->any())->method("getName")->will($this->returnValue($projectName));
		$projects = array($mockProject, $mockProject);
		$this->object->setProjects($projects);

		$actual = $this->object->projectNameExists($projectName);

		$this->assertTrue($actual);
	}

	/**
	 * @covers \Controllers\SelectProjectController::projectIdExists
	 */
	public function testProjectIdExists_noProjects() {
		$projectId = "projectId";
		$projects = array();
		$this->object->setProjects($projects);

		$actual = $this->object->projectIdExists($projectId);

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::projectIdExists
	 */
	public function testProjectIdExists_doesNotExist() {
		$projectId = "projectId";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getId"))
			->getMockForAbstractClass();
		$mockProject->expects($this->any())->method("getId")->will($this->returnValue("not" . $projectId));
		$projects = array($mockProject, $mockProject);
		$this->object->setProjects($projects);

		$actual = $this->object->projectIdExists($projectId);

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::projectIdExists
	 */
	public function testProjectIdExists_doesExist() {
		$projectId = "projectId";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getId"))
			->getMockForAbstractClass();
		$mockProject->expects($this->any())->method("getId")->will($this->returnValue($projectId));
		$projects = array($mockProject, $mockProject);
		$this->object->setProjects($projects);

		$actual = $this->object->projectIdExists($projectId);

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::createProject
	 */
	public function testCreateProject_beginProjectFails() {
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("beginProject"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("beginProject")->will($this->throwException(new \Exception()));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new SelectProjectController($mockWorkflow);
		$projectName = "projectName";
		$mockProject->setName($projectName);
		$expecteds = array(
			"is_result_error" => true,
			"result" => "We were unable to create a new project. Please see the error log or contact your system administrator",
			"projects" => array(),
			"project" => NULL,
			"session" => array(),
		);
		$actuals = array();

		$this->object->createProject($projectName);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$actuals['projects'] = $this->object->getProjects();
		$actuals['project'] = $this->object->getProject();
		$actuals['result'] = $this->object->getResult();
		$actuals['session'] = $_SESSION;
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::createProject
	 */
	public function testCreateProject_nothingFails() {
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("beginProject"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("beginProject");
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new SelectProjectController($mockWorkflow);
		$projectName = "projectName";
		$mockProject->setName($projectName);
		$expecteds = array(
			"is_result_error" => false,
			"result" => "We were unable to create a new project. Please see the error log or contact your system administrator",
			"projects" => array($mockProject),
			"project" => $mockProject,
			"result" => "Successfully created project: $projectName",
			"session" => array("project_id" => NULL),
		);
		$actuals = array();

		$this->object->createProject($projectName);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$actuals['projects'] = $this->object->getProjects();
		$actuals['project'] = $this->object->getProject();
		$actuals['result'] = $this->object->getResult();
		$actuals['session'] = $_SESSION;
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::createProject
	 */
	public function testCreateProject_htmlentities() {
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')->setMethods(array("htmlentities"))->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnValue(""));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("beginProject"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("beginProject");
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new SelectProjectController($mockWorkflow);
		$projectName = "projectName";
		$mockProject->setName($projectName);
		$expecteds = array(
			"is_result_error" => false,
			"result" => "We were unable to create a new project. Please see the error log or contact your system administrator",
			"projects" => array($mockProject),
			"project" => $mockProject,
			"result" => "Successfully created project: ",
			"session" => array("project_id" => NULL),
		);
		$actuals = array();

		$this->object->createProject($projectName);

		\Utils\Helper::setDefaultHelper($oldHelper);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$actuals['projects'] = $this->object->getProjects();
		$actuals['project'] = $this->object->getProject();
		$actuals['result'] = $this->object->getResult();
		$actuals['session'] = $_SESSION;
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::selectProject
	 */
	public function testSelectProject() {
		$projectName = "projectName";
		$projectId = 1;
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getName")->will($this->returnValue($projectName));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("findProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("findProject")->will($this->returnValue($mockProject));
		$this->object = new SelectProjectController($mockWorkflow);
		$expecteds = array(
			"result" => "Project selected: {$projectName}",
			"session" => array("project_id" => $projectId),
			"project" => $mockProject,
		);
		$actuals = array();

		$this->object->selectProject($projectId);

		$actuals['result'] = $this->object->getResult();
		$actuals['session'] = $_SESSION;
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::selectProject
	 */
	public function testSelectProject_htmlentities() {
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->disableOriginalConstructor()
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnValue(""));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$projectName = "projectName";
		$projectId = 1;
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getName")->will($this->returnValue($projectName));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("findProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("findProject")->will($this->returnValue($mockProject));
		$this->object = new SelectProjectController($mockWorkflow);
		$expecteds = array(
			"result" => "Project selected: ",
			"session" => array("project_id" => $projectId),
			"project" => $mockProject,
		);
		$actuals = array();

		$this->object->selectProject($projectId);

		\Utils\Helper::setDefaultHelper($oldHelper);
		$actuals['result'] = $this->object->getResult();
		$actuals['session'] = $_SESSION;
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderInstructions
	 */
	public function testRenderInstructions() {

		$actual = $this->object->renderInstructions();

		$this->assertEmpty($actual);
	}

	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_projectsEmpty_notDisabled() {
		$this->object->setProjects(array());
		$expected = "
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\"/></label>
			<button type=\"submit\">Create</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_projectsEmpty_disabled() {
		$this->object->setProjects(array());
		$this->object->setDisabled(" disabled");
		$expected = "
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\" disabled>
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\" disabled/></label>
			<button type=\"submit\" disabled>Create</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_projectsNotEmpty_notDisabled_firstProjectSelected() {
		$mockProject1 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject1->expects($this->exactly(4))->method("getName")->will($this->returnValue("project1"));
		$mockProject1->expects($this->once())->method("getId")->will($this->returnValue(1));
		$mockProject2 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject2->expects($this->exactly(2))->method("getName")->will($this->returnValue("project2"));
		$mockProject2->expects($this->once())->method("getId")->will($this->returnValue(2));
		$projects = array($mockProject1, $mockProject2);
		$selectedProject = $mockProject1;
		$this->object->setProjects($projects);
		$this->object->setProject($selectedProject);
		$expected = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"select\">
				<input type=\"hidden\" name=\"create\" value=\"0\"><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"1\" checked>project1</label><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"2\">project2</label><button type=\"submit\">Select</button>
				</p></form><strong>-OR-</strong><br/>
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\"/></label>
			<button type=\"submit\">Create</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_projectsNotEmpty_notDisabled_secondProjectSelected() {
		$mockProject1 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject1->expects($this->exactly(2))->method("getName")->will($this->returnValue("project1"));
		$mockProject1->expects($this->once())->method("getId")->will($this->returnValue(1));
		$mockProject2 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject2->expects($this->exactly(4))->method("getName")->will($this->returnValue("project2"));
		$mockProject2->expects($this->once())->method("getId")->will($this->returnValue(2));
		$projects = array($mockProject1, $mockProject2);
		$selectedProject = $mockProject2;
		$this->object->setProjects($projects);
		$this->object->setProject($selectedProject);
		$expected = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"select\">
				<input type=\"hidden\" name=\"create\" value=\"0\"><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"1\">project1</label><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"2\" checked>project2</label><button type=\"submit\">Select</button>
				</p></form><strong>-OR-</strong><br/>
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\"/></label>
			<button type=\"submit\">Create</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_projectsNotEmpty_notDisabled_noProjectSelected() {
		$mockProject1 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject1->expects($this->exactly(2))->method("getName")->will($this->returnValue("project1"));
		$mockProject1->expects($this->once())->method("getId")->will($this->returnValue(1));
		$mockProject2 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject2->expects($this->exactly(2))->method("getName")->will($this->returnValue("project2"));
		$mockProject2->expects($this->once())->method("getId")->will($this->returnValue(2));
		$projects = array($mockProject1, $mockProject2);
		$selectedProject = NULL;
		$this->object->setProjects($projects);
		$this->object->setProject($selectedProject);
		$expected = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"select\">
				<input type=\"hidden\" name=\"create\" value=\"0\"><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"1\">project1</label><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"2\">project2</label><button type=\"submit\">Select</button>
				</p></form><strong>-OR-</strong><br/>
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\"/></label>
			<button type=\"submit\">Create</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_projectsNotEmpty_disabled() {
		$mockProject1 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject1->expects($this->exactly(2))->method("getName")->will($this->returnValue("project1"));
		$mockProject1->expects($this->once())->method("getId")->will($this->returnValue(1));
		$mockProject2 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject2->expects($this->exactly(2))->method("getName")->will($this->returnValue("project2"));
		$mockProject2->expects($this->once())->method("getId")->will($this->returnValue(2));
		$projects = array($mockProject1, $mockProject2);
		$selectedProject = NULL;
		$this->object->setProjects($projects);
		$this->object->setProject($selectedProject);
		$this->object->setDisabled(" disabled");
		$expected = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"select\">
				<input type=\"hidden\" name=\"create\" value=\"0\" disabled><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"1\" disabled>project1</label><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"2\" disabled>project2</label><button type=\"submit\" disabled>Select</button>
				</p></form><strong>-OR-</strong><br/>
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\" disabled>
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\" disabled/></label>
			<button type=\"submit\" disabled>Create</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderForm
	 */
	public function testRenderForm_htmlentitiesCalled() {
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')->setMethods(array("htmlentities"))->getMock();
		$mockHelper->expects($this->exactly(2))->method("htmlentities")->will($this->returnValue(""));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject1 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject1->expects($this->exactly(2))->method("getName")->will($this->returnValue("project1"));
		$mockProject1->expects($this->once())->method("getId")->will($this->returnValue(1));
		$mockProject2 = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getId"))
			->getMockForAbstractClass();
		$mockProject2->expects($this->exactly(2))->method("getName")->will($this->returnValue("project2"));
		$mockProject2->expects($this->once())->method("getId")->will($this->returnValue(2));
		$projects = array($mockProject1, $mockProject2);
		$selectedProject = NULL;
		$this->object = new SelectProjectController($this->mockWorkflow);
		$this->object->setProjects($projects);
		$this->object->setProject($selectedProject);
		$this->object->setDisabled(" disabled");
		$expected = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"select\">
				<input type=\"hidden\" name=\"create\" value=\"0\" disabled><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"1\" disabled></label><label class=\"radio\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"2\" disabled></label><button type=\"submit\" disabled>Select</button>
				</p></form><strong>-OR-</strong><br/>
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"select\">
			<input type=\"hidden\" name=\"create\" value=\"1\" disabled>
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\" disabled/></label>
			<button type=\"submit\" disabled>Create</button>
			</form>";

		$actual = $this->object->renderForm();

		\Utils\Helper::setDefaultHelper($oldHelper);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\SelectProjectController::renderHelp
	 */
	public function testRenderHelp() {
		$expected = "It is helpful to organize your files into projects. Each project starts with uploaded input files, for example, a .fasta sequence file, or a map file.
			When you run analyses on your data, the result files are stored, along with metadata concerning all the scripts and command line arguments you used.
			Any work you do on a project is saved, and can be accessed at a later date. Usually there is no harm in walking away from or even logging off your computer while
			longer analysis are running. No need to sit around and wait for your program to run. We'll take care of it.";

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderSpecificStyle
	 */
	public function testRenderSpecificStyle() {
		$expected = "label.radio{display:block}";

		$actual = $this->object->renderSpecificStyle();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::renderSpecificScript
	 */
	public function testRenderSpecificScript() {

		$actual = $this->object->renderSpecificScript();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\SelectProjectController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {

		$actual = $this->object->getScriptLibraries();

		$this->assertEmpty($actual);
	}
}
