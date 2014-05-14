<?php

namespace Models;

interface WorkflowI {
	public function __construct(OperatingSystemI $operatingSystem);
	public function getSteps();
	public function getNextStep($step);
	public function getPreviousStep($step);
	public function getCurrentStep($controller);
	public function getController($step, \Database\DatabaseI $database);

	public function getDefaultProject(\Database\DatabaseI $database);
}
