<?php

namespace Controllers;

class FileUploadErrors {
	private $errors = array();
	public function __construct() {
		$this->errors = array(
			UPLOAD_ERR_INI_SIZE 
				=> "The uploaded file is too large",
            UPLOAD_ERR_FORM_SIZE
				=> "The uploaded file is too large",
            UPLOAD_ERR_PARTIAL
                => "Something wrong happened on our end.  We'll check it out",
            UPLOAD_ERR_NO_FILE
                => "No file was even uploaded",
            UPLOAD_ERR_NO_TMP_DIR
                => "Something wrong happened on our end.  We'll check it out",
            UPLOAD_ERR_CANT_WRITE
                => "Something wrong happened on our end.  We'll check it out",
            UPLOAD_ERR_EXTENSION
                => "That's the wrong file type",
			);
	}
	public function getErrorMessage($errorCode) {
		if ($this->errors[$errorCode]) {
			return $this->errors[$errorCode];
		}
		return "An unknown file-upload error occurred.";
	}
}
