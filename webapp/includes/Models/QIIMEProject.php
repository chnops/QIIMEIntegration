<?php

namespace Models;

class QIIMEProject extends Project {
	public function beginProject() {
		$newId = $this->database->createProject($this->owner, $this->name);
		$this->setId($newId);
		// TODO setup file structure
	}
}
