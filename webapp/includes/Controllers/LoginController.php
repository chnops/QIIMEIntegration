<?php

namespace Controllers;

class LoginController extends Controller {

	protected $subTitle = "Login";
	private $userName = "";

	public function parseSession() {
		if (isset($_SESSION['username'])) {
			$this->hasPastResults = true;
			$this->userName = $_SESSION['username'];
		}
	}

	public function parseInput() {
		if (!isset($_POST['username'])) {
			$this->pastResults = "You are currently logged in as {$this->userName}";
			return;
		}
		$this->hasImmediateResult = true;
		$username = $_POST['username'];
		$userExists = $this->database->userExists($username);
		
		if ($_POST['create']) {
			if ($userExists) {
				$this->immediateResult = "That username is already taken.  Did you mean to log in?";
				if (isset($_SESSION['username'])) {
					$this->pastResults = "You are no longer logged in as {$_SESSION['username']}";
					unset($_SESSION['username']); 
				}
			}
			else {
				$this->createUser($username);
			}
		}
		else {
			if ($userExists) {
				$this->logIn($username);
			}
			else {
				$this->immediateResult = "We found no record of your username.  Would you like to create one?";
				$this->pastResults = "You are still logged in as {$this->userName}";
			}
		}
	}

	private function logIn($username) {
		$_SESSION['username'] = $username;
		$this->immediateResult = "You have successfully logged in.";
		$this->hasPastResults = true;
		$this->pastResults = "You are now logged in as {$username}";
	}
	private function createUser($username) {
		$project = $this->workflow->getProject($this->database);
		$project->createUser($username);

		$this->logIn($username);
		$this->immediateResult = "You have successfully created a new user.";
	}

	public function getInstructions() {
		return "<p>You don't actually need credentials to log in. By entering your name here, you are simply keeping track of your projects.
			We expect everyone on this system to play nicely, and work only on their own projects. We recognize this assumption is naive.</p>";
	}
	public function getForm() {
		$loginForm = "
			<form method=\"POST\">
			<p>Log in (existing user)<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"0\">
			<label for=\"username\">User name: <input type=\"text\" name=\"username\"></label>
			<button type=\"submit\">Log In</button></p>
			</form>";
		$createForm = "
			<form method=\"POST\">
			<p>Create new user<br/>
			<input type=\"hidden\" name=\"step\" value=\"$this->step\">
			<input type=\"hidden\" name=\"create\" value=\"1\">
			<label for=\"username\">New user name: <input type=\"text\" name=\"username\"></label>
			<button type=\"submit\">Create</button></p>
			</form>";
		return $loginForm . "<strong>-OR-</strong><br/>" . $createForm;
	}
}
