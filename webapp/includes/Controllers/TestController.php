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

		echo "<p>Testing dir flattening</p>";
		try {
			$os = new \Models\MacOperatingSystem();
			echo "<pre>";
			system("rm -rf ./projects/test_flatten;
				rm -rf ./projects/test_flatten*;"/*
				mkdir ./projects/test_flatten;
				mkdir ./projects/test_flatten/d1;
				mkdir ./projects/test_flatten/d1/d1;
				touch ./projects/test_flatten/d1/f2;
				mkdir ./projects/test_flatten/d2;
				touch ./projects/test_flatten/d2/f3;
				touch ./projects/test_flatten/f3;"*/);
			$os->flattenDir("test_flatten");
			echo "<pre>";
		}
		catch (\Exception $ex) {
			echo "An error occurred: {$ex->getMessage()}<br/>";
			var_dump($ex->getConsoleOutput());
		}

		echo "<p>Plus a little regex testing</p>";
		$original = "dir%FS%file";
		echo "original: {$original}<br/>";

		$replaced = preg_replace("/%FS%/", "/", $original);
		echo "replaced: {$replaced}<br/>";


		return ob_get_clean();
	}
}
