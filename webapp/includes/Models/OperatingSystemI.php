<?php

namespace Models;

interface OperatingSystemI {
	public function getHome();
	public function createDir($name);
	public function executeArbitraryScript($environmentSource, $projectDirectory, $script);
	public function isValidFileName($name);
}
