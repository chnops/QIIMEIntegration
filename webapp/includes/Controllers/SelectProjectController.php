<?php

namespace Controllers;

class SelectProjectController extends Controller {

	public function getSubtitle() {
		return "Select a Project";
	}
	public function retrievePastResults() {
		return "";
	}
	private $projects = array();
	
	public function parseSession() {
		parent::parseSession();
		if ($this->username) {
			$this->projects = $this->workflow->getAllProjects($this->username);
		}
		if (!$this->username) { // could be an else, but this makes more sense conceptually
			$this->disabled = " disabled";
		}
	}

	public function parseInput() {
		if (!$this->username) {
			$this->hasResult = true;
			$this->isResultError = true;
			$this->result = "You cannot choose a project if you aren't logged in.";
			return;
		}
		if (!isset($_POST['project'])) {
			return;
		}
		$this->hasResult = true;

		if ($_POST['create']) {
			$projectName = $_POST['project'];
			$projectExists = $this->projectNameExists($projectName);

			if ($projectExists) {
				$this->isResultError = true;
				$this->result = "A project with that name already exists. Did you mean to select it?";
			}
			else {
				$project = $this->workflow->getNewProject();
				$project->setName($projectName);
				$project->setOwner($this->username);
				try {
					$project->beginProject();
					$this->projects[] = $project;
					$this->result = "Successfully created project: " . htmlentities($projectName);
					$_SESSION['project_id'] = $project->getId();
					$this->project = $project;
				}
				catch (\Exception $ex) {
					$this->isResultError = true;
					error_log($ex->getMessage());
					$this->result = "We were unable to create a new project. Please see the error log or contact your system administrator";
				}
			}
		}
		else {
			$projectId = $_POST['project'];
			$projectExists = $this->projectIdExists($projectId);
			if ($projectExists) { 
				$project = $this->workflow->findProject($this->username, $projectId);
				$this->result = "Project selected: " . htmlentities($project->getName());
				$_SESSION['project_id'] = $projectId;
				$this->project = $project;
			}
			else {
				$this->isResultError = true;
				$this->result = "No project with that name exists. Did you mean to create it?";
			}
		}
	}

	private function projectNameExists($projectName) {
		foreach ($this->projects as $project) {
			if ($project->getName() == $projectName) {
				return true;
			}
		}
		return false;
	}
	private function projectIdExists($projectId) {
		foreach ($this->projects as $project) {
			if ($project->getId() == $projectId) {
				return true;
			}
		}
		return false;
	}

	public function renderInstructions() {
		return "";
	}

	public function renderForm() {
		$selectForm = "";
		if (!empty($this->projects)) {
			$selectForm = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
				<input type=\"hidden\" name=\"create\" value=\"0\"{$this->disabled}>";

			foreach ($this->projects as $project) {
				$checkedName = ($this->project) ? $this->project->getName() : "";
				$checked = ($checkedName == $project->getName()) ? " checked" : "";
				$projectName = htmlentities($project->getName());
				$selectForm .= "<label style=\"display:block;\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"{$project->getId()}\"{$this->disabled}{$checked}>{$projectName}</label>";
			}

			$selectForm .=	"<button type=\"submit\"{$this->disabled}>Select</button>
				</p></form><strong>-OR-</strong><br/>";
		}
		$createForm = "
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"1\"{$this->disabled}>
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\"{$this->disabled}/></label>
			<button type=\"submit\"{$this->disabled}>Create</button>
			</form>";
		return $selectForm . $createForm;
	}
	public function renderHelp() {
		return "It is helpful to organize your files into projects. Each project starts with uploaded input files, for example, a .fasta sequence file, or a map file.
			When you run analyses on your data, the result files are stored, along with metadata concerning all the scripts and command line arguments you used.
			Any work you do on a project is saved, and can be accessed at a later date. Usually there is no harm in walking away from or even logging off your computer while
			longer analysis are running. No need to sit around and wait for your program to run. We'll take care of it.";
	}
}
