<?php

class LoginController extends Controller {

	protected $subTitle = "Login";
	protected $content = "";
	protected $help = "<a href=\"index.php?step=select\">Go to next step</a>";
	protected $step = 'login';

	public function __construct() {
		ob_start();
		include 'LoginView.php';
		$this->content = ob_get_clean();
	}

	public function parseInput() {
		if (!isset($_POST['username'])) {
			return;
		}
		$username = $_POST['username'];
		ob_start();
		system("ls projects/");
		$users = ob_get_clean();
		$users = explode("\n", $users);

		if (in_array($username, $users)) {
			$_SESSION['username'] = $username;
			header("Location: index.php?step=select");
		}
		else {
			$this->content .= "We found no record of your username.  Would you like to create one?";
		}
	}
}
