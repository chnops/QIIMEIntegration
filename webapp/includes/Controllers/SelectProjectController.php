<?php

namespace Controllers;

class SelectProjectController extends Controller {

	protected $subTitle = "Select a Project";

	private $username = "";
	private $projects = array();
	private $pastProjectName = "";
	private $project = NULL;
	
	public function parseSession() {
		if (!isset($_SESSION['username'])) {
			$this->hasImmediateResult = true;
			$this->immediateResult = "You cannot choose a project if you aren't logged in.";
			return;
		}
		$this->username = $_SESSION['username'];

		if (isset($_SESSION['project_id'])) {
			$pastProjectId = $_SESSION['project_id'];
			$pastProject = $this->workflow->findProject($this->username, $pastProjectId);
			$this->pastProjectName = $pastProject->getName();
			$this->hasPastResults = true;
			$this->pastResults = htmlentities("You are currently working on the project: {$this->pastProjectName}");
		}

		$this->projects = $this->workflow->getAllProjects($this->username);
	}

	public function parseInput() {
		if (!$this->username) {
			return;
		}
		if (!isset($_POST['project'])) {
			return;
		}
		$this->hasImmediateResult = true;

		if ($_POST['create']) {
			$projectName = $_POST['project'];
			$projectExists = $this->projectNameExists($projectName);

			if ($projectExists) {
				$this->immediateResult = "A project with that name already exists. Did you mean to select it?";
				$this->pastResults = "You are no longer working on the project: {$this->pastProjectName}";
				unset($_SESSION['project_id']); 
			}
			else {
				$project = $this->workflow->getNewProject();
				$project->setName($projectName);
				$project->setOwner($this->username);
				$project->beginProject();
				$this->immediateResult = "Successfully created project: {$projectName}";
				$_SESSION['project_id'] = $project->getId();
				$this->hasPastResults = true;
				$this->pastResults = "You are now working on the project: {$projectName}";
			}
		}
		else {
			$projectId = $_POST['project'];
			$projectExists = $this->projectIdExists($projectId);
			if ($projectExists) { 
				$project = $this->workflow->findProject($this->username, $projectId);
				$this->immediateResult = "Project selected: {$project->getName()}";
				$_SESSION['project_id'] = $projectId;
				$this->hasPastResults = true;
				$this->pastResults = "You are now working on the project: {$project->getName()}";
			}
			else {
				$this->immediateResult = "No project with that name exists. Did you mean to create it?";
				$this->pastResults = "You are no longer working on the project: {$this->pastProjectName}";
				unset($_SESSION['project_id']); 
			}
		}
		$this->immediateResult = htmlentities($this->immediateResult);
		$this->pastResults = htmlentities($this->pastResults);
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

	public function getInstructions() {
		return "It is helpful to organize your files into projects. Each project starts with uploaded input files, for example, a .fasta sequence file, or a map file.
			When you run analyses on your data, the result files are stored, along with metadata concerning all the scripts and command line arguments you used.
			Any work you do on a project is saved, and can be accessed at a later date. Usually there is no harm in walking away from or even logging off your computer while
			longer analysis are running. No need to sit around and wait for your program to run. We'll take care of it.";
	}

	public function getForm() {
		$disabled = ($this->username) ? "" : " disabled";
		$selectForm = "";
		if (!empty($this->projects)) {
			$selectForm = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
				<input type=\"hidden\" name=\"create\" value=\"0\"{$disabled}>";

			foreach ($this->projects as $project) {
				$projectName = htmlentities($project->getName());
				$selectForm .= "<label style=\"display:block;\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"{$project->getId()}\"{$disabled}>{$projectName}</label>";
			}

			$selectForm .=	"<button type=\"submit\"{$disabled}>Select</button>
				</p></form><strong>-OR-</strong><br/>";
		}
		$createForm = "
			<form method=\"POST\"><p>Create a project<br/>
			<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
			<input type=\"hidden\" name=\"create\" value=\"1\"{$disabled}>
			<label for=\"project\">Project name: <input type=\"text\" name=\"project\"{$disabled}/></label>
			<button type=\"submit\"{$disabled}>Create</button>
			</form>";
		return $selectForm . $createForm;
	}
}
