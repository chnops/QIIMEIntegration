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

		// it's distressing, but it works
		echo "<p>ls " . escapeshellarg("Run run; 'rudolph'") . "</p>";

		return ob_get_clean();
	}
}
