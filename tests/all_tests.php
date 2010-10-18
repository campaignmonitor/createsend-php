<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';

class AllTests extends TestSuite {
	function AllTests() {
		$this->TestSuite('All Tests');
		$this->addFile('class_tests/serialisation_test.php');
		$this->addFile('csrest_test.php');
		$this->addFile('csrest_clients_test.php');
	}
}