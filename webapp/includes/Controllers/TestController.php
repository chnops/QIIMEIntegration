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

		echo "Testing some javascript<br/>";
		echo "<div id=\"script_help_validate\">Validate (<a onclick=\"hide('script_help_validate');\">hide</a>)</div>";
		echo "<div id=\"script_help_spilt\">Split (<a onclick=\"hide('script_help_spilt');\">hide</a>)</div>";

		$javascript = "<script type=\"text/javascript\">
			var hiddenElement = null;
			function hide(id) {
				if (hiddenElement != null) {
					hiddenElement.style.display=\"block\";
				}
				hiddenElement = document.getElementById(id);
				hiddenElement.style.display=\"None\";
			}
			</script>";

		echo $javascript;
		echo htmlentities($javascript);
		return ob_get_clean();
	}
}
