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

		echo "<p>Testing OperatingSystem's file-validation regex</p>";
		$os = new \Models\MacOperatingSystem();

		$input = 'uploads';
		echo "<ul><li>Input({$input}): Output({$os->isValidFileName($input)})</li></ul>";
		$input = 'u1';
		echo "<ul><li>Input({$input}): Output({$os->isValidFileName($input)})</li></ul>";
		$input = 'p2';
		echo "<ul><li>Input({$input}): Output({$os->isValidFileName($input)})</li></ul>";
		$input = 'u111111111111';
		echo "<ul><li>Input({$input}): Output({$os->isValidFileName($input)})</li></ul>";
		$input = 'u';
		echo "<ul><li>Input({$input}): Output({$os->isValidFileName($input)})</li></ul>";
		$input = '1';
		echo "<ul><li>Input({$input}): Output({$os->isValidFileName($input)})</li></ul>";

		return ob_get_clean();
	}
}
