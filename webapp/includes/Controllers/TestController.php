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
		
		echo "Updating database schema:<br/>";
		$result = 0;
		system("sqlite3 ./data/database.sqlite < ./data/schema.sql", $result);

		if ($result) {
			echo "<hr/>Oh no! Something bad happened!";
		}

		return ob_get_clean();
	}
}
