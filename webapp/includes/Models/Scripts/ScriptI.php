<?php

namespace Models\Scripts;

interface ScriptI {

	public function __construct(\Models\Project $project);
	public function getScriptName();
	public function getScriptTitle();
	public function getHtmlId();
	public function renderAsForm();
	public function getParameters();
	public function renderHelp();
	public function run();
	public function processInput(array $input);
}
