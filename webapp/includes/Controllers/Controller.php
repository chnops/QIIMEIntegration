<?php

namespace Controllers;

abstract class Controller {

	protected $database = NULL;
	protected $workflow = NULL;

	protected $title = "QIIME";
	protected $subTitle = "";
	protected $help = "";
	protected $step;
	private $content = "";

	protected $hasImmediateResult = false;
	protected $immediateResult = "<div class=\"error\">Immediate result not yet implemented!</div>";
	protected $hasPastResults = false;
	protected $pastResults = "<div class=\"error\">Past results not yet implemented!</div>";

	public function __construct(\Database\DatabaseI $database, \Models\WorkflowI $workflow) {
		$this->database = $database;
		$this->workflow = $workflow;
		$this->step = $this->workflow->getCurrentStep($this);
		$this->help = "<a href=\"index.php?step=" . $this->workflow->getNextStep($this->step) . "\">Go to next step</a>";
	}

	public abstract function parseInput(); 
	public abstract function parseSession();

	public function hasImmediateResult() {
		return $this->hasImmediateResult;
	}
	public function getImmediateResult() {
		return $this->immediateResult;
	}
	public function hasPastResults() {
		return $this->hasPastResults;
	}
	public function getPastResults() {
		return $this->pastResults;
	}
	public function getInstructions() {
		return "<div class=\"error\">Instructions not yet implemented!</div>";
	}
	public function getForm() {
		return "<div class=\"error\">Form not yet implemented!</div>";
	}
	public function getWorkflow() {
		return $this->workflow;
	}
	public function getSubTitle() {
		return $this->subTitle;
	}
	public function renderOutput() {
		ob_start();
		include 'views/content.php';
		$this->content = ob_get_clean();
		include 'views/template.php';
	}
	public function run() {
		$this->parseSession();
		$this->parseInput();
		$this->renderOutput();
	}
}
