<?php

namespace Models\Scripts;

interface ScriptI {

	public function __construct(\Database\DatabaseI $database, \Models\OperatingSystemI $operatingSystem);
	public function getScriptName();
	public function getScriptTitle();
	public function getScriptShortTitle();
	public function renderAsForm();
	public function getParameters();
	public function renderHelp();
	public function run();
	public function processInput(array $input);
}
