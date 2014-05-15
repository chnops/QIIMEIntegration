<?php

namespace Controllers;

class ShortcutController extends Controller {

	protected $subTitle = "Full Analysis";

	public function parseSession() {
		$this->project = $this->workflow->getNewProject();
	}

	public function parseInput() {
		if (!empty($_POST)) {
			$this->hasResult = true;
			$this->result = $this->project->processInput($_POST);
		}
	}

	public function getInstructions() {
		$this->help = $this->project->renderHelp();

		$javascript = "<script type=\"text/javascript\">document.getElementById('navigation').style.display=\"none\"</script>";
		$css = "<style>input[type=\"text\"],input[type=\"file\"],select{display:block}</style>";
		$instructions = "This page will take you from start to finish on you QIIME analysis. You can find helpful tips in the box on the right.";
		return $javascript . $css . $instructions;
	}

	public function getForm() {
		return $this->project->renderForm();
	}
	public function getSessionData() {
		return "No login required";
	}
}
