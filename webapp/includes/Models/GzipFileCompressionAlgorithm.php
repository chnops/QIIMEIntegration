<?php

namespace Models;

class GzipFileCompressionAlgorithm extends FileCompressionAlgorithm {
	public function getCompressCommand() {
		return "gzip";	
	}
	public function getUncompressCommand() {
		return "gunzip";	
	}
	public function getFileExtension() {
		return ".gz";
	}
	public function getFileExtensionRegex() {
		return "/\\.gz/";
	}
}
