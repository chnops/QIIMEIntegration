<?php

namespace Models;

abstract class FileCompressionAlgorithm {

	public static function getAlgorithmFromString($algorithmName) {
		switch($algorithmName) {
			case "gzip":
				return new GzipFileCompressionAlgorithm();
			default:
				return NULL;
		}
	}

	public abstract function getCompressCommand();
	public abstract function getUncompressCommand();
	public abstract function getFileExtension();
	public abstract function getFileExtensionRegex();
}
