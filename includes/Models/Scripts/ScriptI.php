<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts;

interface ScriptI {

	public function __construct(\Models\ProjectI $project);

	// Dumb accessors
	public function getParameters();
	public function getJsVar();
	// (script specific)
	public function getScriptName();
	public function getScriptTitle();
	public function getHtmlId();
	public function renderHelp();
	public function getInitialParameters();

	// output
	public function renderAsForm($disabled);
	public function renderCommand();
	public function renderVersionCommand();

	// input
	public function acceptInput(array $input);
}
