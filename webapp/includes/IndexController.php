<?php

class IndexController extends Controller {

	protected $subTitle = "Introduction";
	protected $content = "Welcome to QIIME!";
	protected $help = "";
	protected $step = 0;

	protected $subController = NULL;

	public function run() {
		$this->parseInput();
		$this->renderOutput();
	}

	public function parseInput() {
		if (isset($_REQUEST['step'])) {
			switch($_REQUEST['step']) {
			case "login":
				$this->subController = new LoginController();
				break;
			case "select":
				$this->subController = new SelectProjectController();
				break;
			case "upload":
				$this->subController = new UploadController();
				break;
			case "make_otu":
				$this->subController = new MakeOTUController();
				break;
			case "make_phylogeny":
				$this->subController = new MakePhylogenyController();
				break;
			case "view":
				$this->subController = new ViewResultsController();
				break;
			default:
				$this->subController = new LoginController();
				break;
			}
		}
		else {
			$this->subController = new LoginController();
		}
	}

	public function renderOutput() {
		if ($this->subController) {
			$this->subController->run();
		}
		else {
			error_log("Attempted to render output before setting subcontroller");
		}
	}
}
