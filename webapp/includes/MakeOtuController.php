<?php
class MakeOTUController extends Controller {

	protected $subTitle = "Make OTU Table";
	protected $content = "
<h2>Create OTU table</h2>
<p>The OTU table is pretty much the end result of QIIME's analysis pipeline.  After that, you can import it into R. 
Assigning to real-world taxonomies is optional, and time-intensive.</p>
<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
	<input type=\"hidden\" name=\"page\" value=\"step5.php\"/>
	<button type=\"submit\">Perform</button>
	</form>";
	protected $help = "<a href=\"index.php?step=make_phylogeny\">Go to next step</a>";
	protected $step = 'make_otu';

	public function parseInput() {
		if (!isset($_POST['page'])) {
			return;
		}
	}
}
