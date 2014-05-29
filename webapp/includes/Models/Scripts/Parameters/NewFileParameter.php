<?php

namespace Models\Scripts\Parameters;

class NewFileParameter extends DefaultParameter { 
	public function isValueValid($value) {
		return !preg_match("/\"/", $value);
	}
}
