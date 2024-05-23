<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/mock_objects.php';

// Running simpletest, you would need to run this using PHP version 7.3 or lower

class AllTests extends TestSuite {
    function __construct() {
        parent::__construct('All Tests');
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
        $this->addFile('csrest_journeys_test.php');
        $this->addFile('csrest_journey_emails_test.php');
    }
}