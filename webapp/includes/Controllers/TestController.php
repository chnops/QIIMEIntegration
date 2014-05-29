<?php

namespace Controllers;

class TestController extends Controller {
	public function parseSession() {
		return;
	}
	public function parseInput() {
		return;
	}
	public function getInstructions() {
		ob_start();

		echo "<p>Setting up database</p>";

		if (isset($_GET['im_super_serious']) && isset($_GET['i_know_what_im_doing'])) {
			system("rm ./data/database.sqlite; if [ $? != 0 ]; then echo 'Unable to remove old database<br/>'; else echo 'Removal complete<br/>'; fi; sqlite3 ./data/database.sqlite < ./data/schema.sql; if [ $? != 0 ]; then echo 'Unable to recreate database<br/>'; else echo 'new database created<br/>'; fi;rm -r ./projects/*; if [ $? != 0 ]; then echo 'Unable to remove projects<br/>'; else echo 'Projects removed <br/>'; fi;");
			$_SESSION = array();
		}

		return ob_get_clean();
	}
}
