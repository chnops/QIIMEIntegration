<?php

namespace Models;

class QIIMEProject extends Project {
	public function beginProject() {
		$this->database->createProject($this->owner, $this->name);
		// TODO setup file structure
		// TODO initialize ID
	}
}
