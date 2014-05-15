<?php

namespace Models;

abstract class Project {
	protected $owner;
	protected $id;
	protected $name;

	protected $workflow;
	protected $database;
	protected $operatingSystem;

	protected $scripts;

	public function __construct(\Database\DatabaseI $database, WorkflowI $workflow, OperatingSystemI $operatingSystem) {
		$this->workflow = $workflow;
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
		$this->scripts = $this->getInitialScripts();
	}

	public function getOwner() {
		return $this->owner;
	}
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getScripts() {
		return $this->scripts;
	}
	public function renderForm() {
		$form = "<form method=\"POST\" enctype=\"multipart/form-data\">
			<h4>Name your project</h4>
			<label>Project name: <input type=\"text\"/></label>
			<label>Project owner: <input type=\"text\"/></label>
			<hr class=\"small\"/>
			<h4>Input files</h4>
			<label>Map file: <input type=\"file\"/></label>
			<hr class=\"small\"/>";

		foreach ($this->getScripts() as $script) {
			$form .= $script->renderAsForm();
			$form .= "<hr class=\"small\"/>";
		}

		$form .= "</form>";
		return $form;
	}

	public abstract function beginProject();
	public abstract function getInitialScripts();
}
