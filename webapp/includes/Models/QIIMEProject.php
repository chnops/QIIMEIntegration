<?php

namespace Models;

class QIIMEProject extends Project {

	public function beginProject() {
		$newId = $this->database->createProject($this->owner, $this->name);
		$this->setId($newId);
		// TODO setup file structure
	}
	public function getInitialScripts() {
		return array(
			new \Models\Scripts\ValidateMappingFile($this->database, $this->operatingSystem),
//			new \Models\Scripts\SplitLibraries($this->database, $this->operatingSystem),
		);
	}

}
