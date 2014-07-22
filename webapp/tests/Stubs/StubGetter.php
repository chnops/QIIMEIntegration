<?php

namespace Stubs;

class StubGetter extends \PHPUnit_Framework_TestCase {
	public function getScript() {
		$mockBuilder = $this->getMockBuilder("\\Models\\Scripts\\DefaultScript");
		$mockBuilder->disableOriginalConstructor();
		return $mockBuilder->getMock();
	}
}
