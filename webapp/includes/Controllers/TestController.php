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

		echo "<p>Testing for appropriate namespace loading</p";

		$parameter = new \Models\Scripts\Parameters\ChoiceParameter("--name", "default", array("default", "non-default"));
		var_dump($parameter);

		try {
			$parameter->setValue("notInArray");
		}
		catch (\Exception $ex) {
			var_dump($ex);
		}

		return ob_get_clean();
	}
}
