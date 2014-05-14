<?php

namespace Models;

class QIIMEProject extends Project {
	public function createProject($username, $projectName) {
		$this->database->createProject($username, $projectName);
	}
}
