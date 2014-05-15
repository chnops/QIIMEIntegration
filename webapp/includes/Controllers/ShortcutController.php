<?php

namespace Controllers;

class ShortcutController extends Controller {

	protected $subTitle = "Full Analysis";

	public function parseSession() {
		$this->help = "";
	}

	public function parseInput() {
		$this->hasPastResult = false;
		if (!empty($_POST)) {
			$this->hasImmediateResult = true;
			$project = $this->workflow->getNewProject();
			$this->immediateResult = $project->processInput($_POST);
		}
	}

	public function getInstructions() {
		$project = $this->workflow->getNewProject();
		$this->help = $project->renderHelp();

		$javascript = "<script type=\"text/javascript\">document.getElementById('navigation').style.display=\"none\"</script>";
		$css = "<style>input[type=\"text\"],input[type=\"file\"],select{display:block}</style>";
		$instructions = "This page will take you from start to finish on you QIIME analysis. You can find helpful tips in the box on the right.";
		return $javascript . $css . $instructions;
	}

	public function getForm() {
		$project = $this->workflow->getNewProject();
		return $project->renderForm();
	}
}
