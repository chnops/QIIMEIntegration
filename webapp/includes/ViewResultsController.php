<?php

class ViewResultsController extends Controller {

	protected $subTitle = "View Results";
	protected $content = "
<h1>Step 6: Results!</h1>
<p>Here is the moment you've been waiting for... your results!</p>
<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
	<input type=\"hidden\" name=\"page\" value=\"step6.php\"/>
	<button type=\"submit\">Perform</button>
	</form>";
	protected $step = 'view';

	public function parseInput() {
		if (!isset($_POST['page'])) {
			return;
		}
	}
}
