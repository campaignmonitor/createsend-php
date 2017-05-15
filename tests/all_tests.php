<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/mock_objects.php';

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All Tests');
        $this->addFile('class_tests/transport_test.php');
        $this->addFile('class_tests/response_tests.php');
        $this->addFile('csrest_test.php');
        $this->addFile('csrest_clients_test.php');
        $this->addFile('csrest_campaigns_test.php');
        $this->addFile('csrest_lists_test.php');
        $this->addFile('csrest_subscribers_test.php');
        $this->addFile('csrest_template_test.php');
        $this->addFile('csrest_segments_test.php');
        $this->addFile('csrest_people_test.php');
        $this->addFile('csrest_administrators_test.php');
        $this->addFile('csrest_events_test.php');
    }
}