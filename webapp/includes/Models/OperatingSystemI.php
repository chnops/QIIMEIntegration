<?php

namespace Models;

interface OperatingSystemI {
	public function getRelativeReference();
	public function setRelativeReference($reference);
	public function createDir($name);
	public function getDirContents($name);
}
