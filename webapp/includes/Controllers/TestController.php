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

		echo "<p>Testing new getDirContents</p>";
		system ("
			rm -rf ./projects/test_get*;
			mkdir ./projects/test_get/;
			mkdir ./projects/test_get/d1;
			mkdir ./projects/test_get/d1/d2;
			mkdir ./projects/test_get/d3;
			touch ./projects/test_get/f1;
			touch ./projects/test_get/d1/f2;
			touch ./projects/test_get/d1/d2/f3;
			touch ./projects/test_get/d3/f4;
			mkdir ./projects/test_get/d99");

		$os = new \Models\MacOperatingSystem();
		$contents = $os->getDirContents("test_get");
		echo "<pre>";
		print_r($contents);
		echo "</pre>";

		system("rm -rf ./projects/test_get*");

		return ob_get_clean();
	}
}
