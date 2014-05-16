<?php

namespace Models;

abstract class FileType {
	protected $name;
	protected $shortName;
	protected $help;
	protected $example;

	public abstract function __construct();

	public function getName() {
		return $this->name;
	}
	public function getShortName() {
		return $this->shortName;
	}

	public function renderHelp() {
		$output = "<div class=\"script_help\" id=\"script_help_{$this->shortName}\">
			<h4>{$this->name} Files</h4>
			<p>{$this->help}</p>";
		if ($this->example) {
			$output .= "<div class=\"file_example\">{$this->example}</div>";
		}
		$output .= "</div>";	
		return $output;
	}
}
