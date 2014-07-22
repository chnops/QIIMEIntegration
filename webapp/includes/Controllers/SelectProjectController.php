<?php

namespace Controllers;

class SelectProjectController extends Controller {
	private $projects = array();

	public function __construct(\Models\WorkflowI $workflow) {
		parent::__construct($workflow);
	}

	public function getSubtitle() {
		return "Select a Project";
	}
	public function retrievePastResults() {
		return "";
	}
	
	public function parseInput() {
		if (!$this->username) {
			$this->disabled = " disabled";
			$this->isResultError = true;
			$this->result = "You cannot choose a project if you aren't logged in.";
			return;
		}
		$this->projects = $this->workflow->getAllProjects($this->username);
		if (!isset($_POST['project'])) {
			return;
		}

		if ($_POST['create']) {
			$projectName = $_POST['project'];
			$projectExists = $this->projectNameExists($projectName);

			if ($projectExists) {
				$this->isResultError = true;
				$this->result = "A project with that name already exists. Did you mean to select it?";
			}
			else {
				$this->createProject($projectName);
			}
		}
		else {
			$projectId = $_POST['project'];
			$projectExists = $this->projectIdExists($projectId);

			if ($projectExists) { 
				$this->selectProject($projectId);
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
	private function createProject($projectName) {
		$project = $this->workflow->getNewProject();
		$project->setName($projectName);
		$project->setOwner($this->username);
		try {
			$project->beginProject();
		}
		catch (\Exception $ex) {
			$this->isResultError = true;
			$this->result = "We were unable to create a new project. Please see the error log or contact your system administrator";
			error_log($ex->getMessage());
			return;
		}
		$this->projects[] = $project;
		$this->project = $project;
		$this->result = "Successfully created project: " . $this->helper->htmlentities($projectName);
		$_SESSION['project_id'] = $project->getId();
	}
	private function selectProject($projectId) {
		$project = $this->workflow->findProject($this->username, $projectId);
		$this->result = "Project selected: " . $this->helper->htmlentities($project->getName());
		$_SESSION['project_id'] = $projectId;
		$this->project = $project;
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
				$projectName = $this->helper->htmlentities($project->getName());
				$selectForm .= "<label class=\"radio\" for=\"project\">
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
	
	public function renderSpecificStyle() {
		return "label.radio{display:block}";
	}
	public function renderSpecificScript() {
		return "";
	}
	public function getScriptLibraries() {
		return array();
	}
}
