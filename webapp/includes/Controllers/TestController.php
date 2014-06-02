<?php

namespace Controllers;

class TestController extends Controller {
	public function parseSession() {
		return;
	}
	public function parseInput() {
		if (isset($_GET['clean'])) {
			$this->clean();
		}
		return;
	}
	private function clean() {
		system("rm -rf ./projects/*;
			rm ./data/database.sqlite;
			sqlite3 ./data/database.sqlite < ./data/schema.sql;");
		$_SESSION = array();
	}
	public function getInstructions() {
		ob_start();

		echo "<p>Testing built in files<p>";
		$project = $this->workflow->getNewProject();		
		$files = $project->retrieveAllBuiltInFiles();

		echo "<pre>";
		print_r($files);
		echo "</pre>";

		return ob_get_clean();
	}
}
