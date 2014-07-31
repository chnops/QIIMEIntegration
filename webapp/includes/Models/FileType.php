<?php

namespace Models;

abstract class FileType implements HideableI {
	public abstract function getName();
	public abstract function getHtmlId();
	public abstract function getHelp();
	public abstract function getExample();

	public function renderHelp() {
		$output = "<h4>{$this->getName()} Files</h4>
			<p>{$this->getHelp()}</p>";
		if ($this->getExample()) {
			$output .= "<div class=\"file_example\">{$this->getExample()}</div>";
		}
		return $output;
	}
}
