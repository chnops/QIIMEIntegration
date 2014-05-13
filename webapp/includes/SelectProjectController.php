<?php

class SelectProjectController extends Controller {

	protected $subTitle = "Select a Project";
	protected $content = "
		<h2>Select or create a project</h2>
		<form method=\"POST\"><input type=\"hidden\" name=\"step\" value=\"select\"><button type=\"submit\">Select</button>
		</form>";
	protected $help = "<a href=\"index.php?step=upload\">Go to next step</a>";
	protected $step = 'select';

	public function parseInput() {
		if (!isset($_POST['username'])) {
			return;
		}
	}
}
