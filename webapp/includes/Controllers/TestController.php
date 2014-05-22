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
		
		echo "Attempting to use jquery:<br/>";
		$script = "<script type=\"text/javascript\">
			function myFunction() { $('div#help').css('display', 'none');}
			$(myFunction);
</script>";
		echo "Here's the script:<br/><pre>";
		echo htmlentities($script);
		echo $script . "</pre>";
		return ob_get_clean();
	}
}
