<?php

namespace Models\Scripts;

class SplitLibraries extends DefaultScript {

	public function getInitialParameters() {
		$required = true;
		return array(
			new DefaultParameter("--version", ""),
			new DefaultParameter("--help", ""),
		);
	}
	public function getScriptName() {
		return "split_libraries.py";
	}
	public function getScriptTitle() {
		return "De-multiplex libraries";
	}
	public function getScriptShortTitle() {
		return "split";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p>
			<p>The purpose of this script is to use the barcodes you provided in your map file to separate sequences from a single run into their respective libraries.</p>";
	}

}
