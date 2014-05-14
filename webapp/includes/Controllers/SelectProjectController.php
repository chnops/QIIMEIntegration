<?php

namespace Controllers;

class SelectProjectController extends Controller {

	protected $subTitle = "Select a Project";

	private $userName = "";
	private $projects = array();
	private $pastProject = "";
	
	public function parseSession() {
		if (!isset($_SESSION['username'])) {
			$this->hasImmediateResult = true;
			$this->immediateResult = "You cannot choose a project if you aren't logged in.";
			return;
		}
		$this->userName = $_SESSION['username'];

		if (isset($_SESSION['project_name'])) {
			$this->pastProject = $_SESSION['project_name'];
			$this->hasPastResults = true;
			$this->pastResults = "You are currently working on the project: {$this->pastProject}";
		}

		$this->projects = $this->database->getAllProjects($this->userName);
	}

	public function parseInput() {
		if (!$this->userName) {
			return;
		}
		if (!isset($_POST['project'])) {
			return;
		}
		$this->hasImmediateResult = true;

		$projectName = $_POST['project'];
		$projectExists = $this->projectExists($projectName);

		if ($_POST['create']) {
			if ($projectExists) {
				$this->immediateResult = "A project with that name already exists. Did you mean to select it?";
				$this->pastResults = "You are no longer working on the project: {$this->projectName}";
				unset($_SESSION['project_name']); 
			}
			else {
				$this->workflow->getDefaultProject($this->database)->createProject($this->userName, $projectName);
				$this->immediateResult = "Successfully created project: {$projectName}";
				$_SESSION['project_name'] = $projectName;
				$this->hasPastResults = true;
				$this->pastResults = "You are now working on the project: {$projectName}";
			}
		}
		else {
			if ($projectExists) {
				$this->immediateResult = "Project selected: {$projectName}";
				$_SESSION['project_name'] = $projectName;
				$this->hasPastResults = true;
				$this->pastResults = "You are now working on the project: {$projectName}";
			}
			else {
				$this->immediateResult = "No project with that name exists. Did you mean to create it?";
				$this->pastResults = "You are no longer working on the project: {$this->projectName}";
				unset($_SESSION['project_name']); 
			}
		}

	}

	private function projectExists($projectName) {
		return in_array($projectName, $this->projects);
	}

	public function getInstructions() {
		return "It is helpful to organize your files into projects. Each project starts with uploaded input files, for example, a .fasta sequence file, or a map file.
			When you run analyses on your data, the result files are stored, along with metadata concerning all the scripts and command line arguments you used.
			Any work you do on a project is saved, and can be accessed at a later date. Usually there is no harm in walking away from or even logging off your computer while
			longer analysis are running. No need to sit around and wait for your program to run. We'll take care of it.";
	}

	public function getForm() {
		$disabled = ($this->userName) ? "" : " disabled";
		$selectForm = "";
		if (!empty($this->projects)) {
			$selectForm = "
				<form method=\"POST\"><p>Select a project<br/>
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\">
				<input type=\"hidden\" name=\"create\" value=\"0\"{$disabled}>";

			foreach ($this->projects as $project) {
				$selectForm .= "<label style=\"display:block;\" for=\"project\">
					<input type=\"radio\" name=\"project\" value=\"{$project['id']}\"{$disabled}>{$project['name']}</label>";
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
