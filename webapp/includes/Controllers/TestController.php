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
		
		$name = $this->workflow->getCurrentStep($this);
		var_dump($name);

		return ob_get_clean();
	}
}
