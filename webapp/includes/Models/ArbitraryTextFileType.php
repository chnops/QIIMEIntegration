<?php

namespace Models;

class ArbitraryTextFileType extends FileType {
	public function getName() {
		return "Arbitrary Text";
	}
	public function getHtmlId() {
		return "arbitrary_text";
	}
	public function getHelp() {
		return	"<p>Sometimes, there isn't a specific file type that fits the file you want to upload.  It may be required only once, in a highly specific context, and so your workflow
			doesn't usually call for it.  In that case, you can upload an arbitrary text file.  The program won't perform any format checking on it, but if it's necessary, you
			can upload it.</p>";
	}
	public function getExample() {
		return "";
	}
}
