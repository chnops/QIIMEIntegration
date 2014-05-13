<?php

class MakePhylogenyController extends Controller {

	protected $subTitle = "Perform Phylogeny Analysis";
	protected $content = "
<h2>Optionally perform phylogeny analysis</h2>
<p>QIIME doesn't offer much in the way of phylogeny analysis, but they can create trees.</p>
<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
	<input type=\"hidden\" name=\"page\" value=\"step3.php\"/>
	<input type=\"radio\" name=\"perform_phylogeny\" value=\"yes\" checked>Yes</input>
	<input type=\"radio\" name=\"perform_phylogeny\" value=\"no\">No</input>
	<button type=\"submit\">Perform</button>
</form>";
	protected $help = "<a href=\"index.php?step=view\">Go to next step</a>";
	protected $step = 'make_phylogeny';

	public function parseInput() {
		if (!isset($_POST['page'])) {
			return;
		}
		if ($_POST['perform_phylogeny'] == "no") {
			echo "ok<br/>";
		}
		else {
			echo "Sorry, phylogeny not implemented yet.<br/>";
		}
	}
}
