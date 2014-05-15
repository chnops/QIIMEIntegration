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

		$shortNameParam = new \Models\Scripts\DefaultParameter("-n", "v");
		$longNameParam = new \Models\Scripts\DefaultParameter("--name", "value");

		echo "<p>Short name: " . $shortNameParam->renderForOperatingSystem() . "</p>";
		echo "<p>Long name: " . $longNameParam->renderForOperatingSystem() . "</p>";

		return ob_get_clean();
	}
}
