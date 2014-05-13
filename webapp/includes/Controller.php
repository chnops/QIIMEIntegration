<?php

abstract class Controller {

	protected $title = "QIIME";
	protected $subTitle = "";
	protected $help = "";
	protected $content = "";
	protected $step = 0;

	public abstract function parseInput(); 
	public function renderOutput() {
		include 'Template.php';
	}
	public function run() {
		$this->parseInput();
		$this->renderOutput();
	}
}
