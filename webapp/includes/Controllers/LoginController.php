<?php

namespace Controllers;

class LoginController extends Controller {

	private $roster = NULL;

	public function __construct(\Models\WorkflowI $workflow) {
		parent::__construct($workflow);
		$this->roster = \Utils\Roster::getRoster();
	}

	public function retrievePastResults() {
		return "";
	}
	public function parseInput() {
		if (isset($_POST['logout'])) {
			$this->logout();
			$this->result = "Logout successful";
			return;
		}
		if (!isset($_POST['username'])) {
			return;
		}
		
		$username = $_POST['username'];
		$userExists = $this->roster->userExists($username);
		
		if ($_POST['create']) {
			if ($userExists) {
				$this->isResultError = true;
				$this->result = "That username is already taken.  Did you mean to log in?";
			}
			else {
				$this->createUser($username);
			}
		}
		else {
			if ($userExists) {
				$this->login($username);
			}
			else {
				$this->isResultError = true;
				$this->result = "We found no record of your username.  Would you like to create one?";
			}
		}
	}

	private function logout() {
		$_SESSION = array();
		$this->username = NULL;
		$this->project = NULL;
	}
	private function login($username) {
		$this->logout();
		$_SESSION['username'] = $username;
		$this->username = $username;
		$this->result = "You have successfully logged in.";
	}
	private function createUser($username) {
		try {
			$this->roster->createUser($username);
		}
		catch (\Exception $ex) {
			error_log($ex->getMessage());
			$this->isResultError = true;
			$this->result = "We were unable to create a new user.  Please see the error log or contact your system administrator";
			return;
		}
		$this->result = "You have successfully created a new user.";
		$this->login($username);
	}

	public function getSubTitle() {
		return "Login";
	}
	public function renderInstructions() {
		return "";
	}
	public function renderForm() {
		$loginForm = "
			<form method=\"POST\">
			<p>Log in (existing user)<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<label for=\"username\">User name: <input type=\"text\" name=\"username\" value=\"{$this->username}\"></label>
			<button type=\"submit\">Log In</button></p>
			</form>";
		$createForm = "
			<form method=\"POST\">
			<p>Create new user<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"username\">New user name: <input type=\"text\" name=\"username\"></label>
			<button type=\"submit\">Create</button></p>
			</form>";
		$logoutForm = "
			<form method=\"POST\">
			<p>Log out<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<button type=\"submit\" name=\"logout\" value=\"1\">Log out</button>
			</form>";
		return $loginForm . "<strong>-OR-</strong><br/>" . $createForm . "<strong>-OR-</strong><br/>" . $logoutForm;
	}
	public function renderHelp() {
		return "<p>You don't actually need credentials to log in. By entering your name here, you are simply keeping track of your projects.
			We expect everyone on this system to play nicely, and work only on their own projects. We recognize this assumption is naive.</p>";
	}

	public function renderSpecificStyle() {
		return "";
	}
	public function renderSpecificScript() {
		return "";
	}
	public function getScriptLibraries() {
		return array();
	}
}
