<?php

namespace Controllers;

class LoginControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("LoginControllerTest");
	}
	public static function tearDownAfterClass() {
		\Utils\Helper::setDefaultHelper(NULL);
		\Utils\Roster::setDefaultRoster(NULL);
	}

	private $mockWorkflow = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getStep"))
			->getMock();
		$this->mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("login"));
	}
	public function setUp() {
		$_POST = array();
		\Utils\Helper::setDefaultHelper(NULL);
		\Utils\Roster::setDefaultRoster(NULL);
		$this->object = new LoginController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\LoginController::retrievePastResults
	 */
	public function testRetrievePastResults() {
		$expected = "";

		$actual = $this->object->retrievePastResults();

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Controllers\LoginController::parseInput
	 */
	public function testParseInput_logoutIsSet() {
		$expected = "Logout successful";
		$_POST['logout'] = 1;
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("logout", "createUser", "login"))
			->getMock();
		$this->object->expects($this->once())->method("logout");
		$this->object->expects($this->never())->method("createUser");
		$this->object->expects($this->never())->method("login");

		$this->object->parseInput();

		$actual = $this->object->getResult();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::parseInput
	 */
	public function testParseInput_usernameIsNotSet() {
		$expected = "";
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("logout", "createUser", "login"))
			->getMock();
		$this->object->expects($this->never())->method("logout");
		$this->object->expects($this->never())->method("createUser");
		$this->object->expects($this->never())->method("login");

		$this->object->parseInput();

		$actual = $this->object->getResult();
		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::parseInput
	 */
	public function testParseInput_create_userExists() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "That username is already taken.  Did you mean to log in?",
		);
		$actuals = array();
		$mockRoster = $this->getMockBuilder('\Utils\Roster')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockRoster->expects($this->once())->method("userExists")->will($this->returnValue(true));
		\Utils\Roster::setDefaultRoster($mockRoster);
		$_POST['username'] = "username";
		$_POST['create'] = true;
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("logout", "createUser", "login"))
			->getMock();
		$this->object->expects($this->never())->method("logout");
		$this->object->expects($this->never())->method("createUser");
		$this->object->expects($this->never())->method("login");

		$this->object->parseInput();

		$actuals['result'] = $this->object->getResult();
		$actuals['is_result_error'] = $this->object->isResultError();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\LoginController::parseInput
	 */
	public function testParseInput_create_userDoesNotExist() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "",
		);
		$actuals = array();
		$mockRoster = $this->getMockBuilder('\Utils\Roster')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockRoster->expects($this->once())->method("userExists")->will($this->returnValue(false));
		\Utils\Roster::setDefaultRoster($mockRoster);
		$_POST['username'] = "username";
		$_POST['create'] = true;
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("logout", "createUser", "login"))
			->getMock();
		$this->object->expects($this->never())->method("logout");
		$this->object->expects($this->once())->method("createUser");
		$this->object->expects($this->never())->method("login");

		$this->object->parseInput();

		$actuals['result'] = $this->object->getResult();
		$actuals['is_result_error'] = $this->object->isResultError();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\LoginController::parseInput
	 */
	public function testParseInput_login_userExists() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "",
		);
		$actuals = array();
		$mockRoster = $this->getMockBuilder('\Utils\Roster')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockRoster->expects($this->once())->method("userExists")->will($this->returnValue(true));
		\Utils\Roster::setDefaultRoster($mockRoster);
		$_POST['username'] = "username";
		$_POST['create'] = false;
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("logout", "createUser", "login"))
			->getMock();
		$this->object->expects($this->never())->method("logout");
		$this->object->expects($this->never())->method("createUser");
		$this->object->expects($this->once())->method("login");

		$this->object->parseInput();

		$actuals['result'] = $this->object->getResult();
		$actuals['is_result_error'] = $this->object->isResultError();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\LoginController::parseInput
	 */
	public function testParseInput_login_userDoesNotExist() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "We found no record of your username.  Would you like to create one?",
		);
		$actuals = array();
		$mockRoster = $this->getMockBuilder('\Utils\Roster')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockRoster->expects($this->once())->method("userExists")->will($this->returnValue(false));
		\Utils\Roster::setDefaultRoster($mockRoster);
		$_POST['username'] = "username";
		$_POST['create'] = false;
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("logout", "createUser", "login"))
			->getMock();
		$this->object->expects($this->never())->method("logout");
		$this->object->expects($this->never())->method("createUser");
		$this->object->expects($this->never())->method("login");

		$this->object->parseInput();

		$actuals['result'] = $this->object->getResult();
		$actuals['is_result_error'] = $this->object->isResultError();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controllers\LoginController::logout
	 */
	public function testLogout() {
		$expecteds = array(
			'session' => array(),
			'username' => NULL,
			'project' => NULL,
		);
		$actuals = array();
		$_SESSION = array(1, 2, 3);
		$this->object->setUsername("username");
		$this->object->setProject("project");

		$this->object->logout();
		
		$actuals['session'] = $_SESSION;
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\LoginController::login
	 */
	public function testLogin() {
		$expectedUsername = "username";
		$expecteds = array(
			"session" => array("username" => $expectedUsername),
			"username" => $expectedUsername,
			"result" => "You have successfully logged in.",
		);
		$actuals = array();
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->disableOriginalConstructor()
			->setMethods(array("logout"))
			->getMock();
		$this->object->expects($this->once())->method("logout");
		$this->object->setUsername("notusername");

		$this->object->login($expectedUsername);

		$actuals['session'] = $_SESSION;
		$actuals['username'] = $this->object->getUsername();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\LoginController::createUser
	 */
	public function testCreateUser_createUserFails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "We were unable to create a new user.  Please see the error log or contact your system administrator",
		);
		$actuals = array();
		$mockRoster = $this->getMockBuilder('\Utils\Roster')
			->disableOriginalConstructor()
			->setMethods(array("createUser"))
			->getMock();
		$mockRoster->expects($this->once())->method("createUser")->will($this->throwException(new \Exception("Unable to creat user")));
		\Utils\Roster::setDefaultRoster($mockRoster);
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("login"))
			->getMock();
		$this->object->expects($this->never())->method("login");

		$this->object->createUser("username");

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\LoginController::createUser
	 */
	public function testCreateUser_nothingFails() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "You have successfully created a new user.",
		);
		$actuals = array();
		$mockRoster = $this->getMockBuilder('\Utils\Roster')
			->disableOriginalConstructor()
			->setMethods(array("createUser"))
			->getMock();
		$mockRoster->expects($this->once())->method("createUser");
		\Utils\Roster::setDefaultRoster($mockRoster);
		$this->object = $this->getMockBuilder('\Controllers\LoginController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("login"))
			->getMock();
		$this->object->expects($this->once())->method("login");

		$this->object->createUser("username");

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controllers\LoginController::getSubTitle
	 */
	public function testGetSubTitle() {
		$expected = "Login";

		$actual = $this->object->getSubTitle();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::renderInstructions
	 */
	public function testRenderInstructions() {
		$expected = "";
		
		$actual = $this->object->renderInstructions();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::renderForm
	 */
	public function testRenderForm_usernameIsNotSet() {
		$expected = "
			<form method=\"POST\">
			<p>Log in (existing user)<br/>
			<input type=\"hidden\" name=\"step\" value=\"login\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<label for=\"username\">User name: <input type=\"text\" name=\"username\" value=\"\"></label>
			<button type=\"submit\">Log In</button></p>
			</form><strong>-OR-</strong><br/>
			<form method=\"POST\">
			<p>Create new user<br/>
			<input type=\"hidden\" name=\"step\" value=\"login\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"username\">New user name: <input type=\"text\" name=\"username\"></label>
			<button type=\"submit\">Create</button></p>
			</form><strong>-OR-</strong><br/>
			<form method=\"POST\">
			<p>Log out<br/>
			<input type=\"hidden\" name=\"step\" value=\"login\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<button type=\"submit\" name=\"logout\" value=\"1\">Log out</button>
			</form>";

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::renderForm
	 */
	public function testRenderForm_usernameIsSet() {
		$expectedUsername = "username";
		$expected = "
			<form method=\"POST\">
			<p>Log in (existing user)<br/>
			<input type=\"hidden\" name=\"step\" value=\"login\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<label for=\"username\">User name: <input type=\"text\" name=\"username\" value=\"{$expectedUsername}\"></label>
			<button type=\"submit\">Log In</button></p>
			</form><strong>-OR-</strong><br/>
			<form method=\"POST\">
			<p>Create new user<br/>
			<input type=\"hidden\" name=\"step\" value=\"login\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"username\">New user name: <input type=\"text\" name=\"username\"></label>
			<button type=\"submit\">Create</button></p>
			</form><strong>-OR-</strong><br/>
			<form method=\"POST\">
			<p>Log out<br/>
			<input type=\"hidden\" name=\"step\" value=\"login\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<button type=\"submit\" name=\"logout\" value=\"1\">Log out</button>
			</form>";
		$this->object->setUsername($expectedUsername);

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::renderHelp
	 */
	public function testRenderHelp() {
		$expected = "<p>You don't actually need credentials to log in. By entering your name here, you are simply keeping track of your projects.
			We expect everyone on this system to play nicely, and work only on their own projects. We recognize this assumption is naive.</p>";

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\LoginController::renderSpecificStyle
	 */
	public function testRenderSpecificStyle() {
		$expected = "";
		
		$actual = $this->object->renderSpecificStyle();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::renderSpecificScript
	 */
	public function testRenderSpecificScript() {
		$expected = "";
		
		$actual = $this->object->renderSpecificScript();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\LoginController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {
		$expected = array();
		
		$actual = $this->object->getScriptLibraries();

		$this->assertSame($expected, $actual);
	}
}
