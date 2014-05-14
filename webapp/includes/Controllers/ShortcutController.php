<?php

namespace Controllers;

class ShortcutController extends Controller {

	protected $subTitle = "Full Analysis";

	public function parseSession() {
		$this->help = "";
	}

	public function parseInput() {
		// TODO handle ALL form data!
	}

	public function getInstructions() {
		$instructions = "<script type=\"text/javascript\">document.getElementById('navigation').style.display = \"none\";</script>";
		$instructions .= "This page will take you from start to finish on you QIIME analysis. You can find helpful tips in the box on the right.";
		return $instructions;
	}

	public function getForm() {
		ob_start();
		$project = $this->workflow->getNewProject();
?>
<form method="POST" enctype="multipart/form-data">
<h4>Name your project</h4>
<label>Project name: <input type="text"/></label>
<label>Project owner: <input type="text"/></label>
<hr class="small"/>
<h4>Input files</h4>
<label>Map file: <input type="file"/></label>
<hr class="small"/>
<?php
		foreach ($project->getScripts() as $script) {
			echo $script->renderForm();
			echo "<hr class=\"small\"/>";
		}
		echo "</form>";
		return ob_get_clean();
	}
}
