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
			new \Models\Scripts\QIIME\ValidateMappingFile($this),
			new \Models\Scripts\QIIME\SplitLibraries($this),
		);
	}
	public function getInitialFileTypes() {
		return array(
			new MapFileType(),
			new SequenceFileType(),
			new SequenceQualityFileType(),
		);
	}
	public function processInput(array $allInput) {
		ob_start();

		$this->setName($allInput['project_name']);
		unset($allInput['project_name']);
		$this->setOwner($allInput['project_owner']);
		unset($allInput['project_owner']);
		// TODO process file {$_FILES['project_input_file']}
		unset($_FILES['project_input_file']);

		foreach ($this->scripts as $script) {
			echo $script->processInput($allInput);
		}

		return ob_get_clean();
	}

}
