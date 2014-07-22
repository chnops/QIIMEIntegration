<?php

namespace Models;

abstract class FileType implements HideableI {
	protected $name;
	protected $htmlId;
	protected $help;
	protected $example;

	public abstract function __construct();

	public function getName() {
		return $this->name;
	}
	public function getHtmlId() {
		return $this->htmlId;
	}

	public function renderHelp() {
		$output = "<h4>{$this->name} Files</h4>
			<p>{$this->help}</p>";
		if ($this->example) {
			$output .= "<div class=\"file_example\">{$this->example}</div>";
		}
		return $output;
	}
}
