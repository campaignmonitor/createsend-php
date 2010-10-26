<?php

require_once 'simpletest/autorun.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';

@Mock::generate('CS_REST_Log');

if(@CS_REST_XmlSerialiser::is_available()):
class CS_REST_TestXmlSerialisation extends UnitTestCase {
    function testSerialisationSingleElement() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);

        $xml = $ser->serialise(array(
            'Name' => 'The The'
            ));

            $this->assertEqual('<Name>The The</Name>', $xml);
    }

    function testSerialisationNestedElements() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);

        $xml = $ser->serialise(array(
            'Campaign' => array(
                'Name' => 'Campaign Name',
                'Subject' => 'Campaign Subject',
                'Lists' => array (
                    'List' => array (
                        'Name' => 'Website Subscribers',
                        'ListID' => 'abcdef'
                        )
                        )
                        )
                        ));

                        $this->assertIdentical($xml, '<Campaign><Name>Campaign Name</Name><Subject>Campaign Subject</Subject>'.
            '<Lists><List><Name>Website Subscribers</Name><ListID>abcdef</ListID></List></Lists></Campaign>');		
    }

    function testSerialisationNestedElementsWithPrettification() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);

        $xml = $ser->serialise(array(
            'Campaign' => array(
                'Name' => 'Campaign Name',
                'Subject' => 'Campaign Subject',
                'Lists' => array (
                    'List' => array (
                        'Name' => 'Website Subscribers',
                        'ListID' => 'abcdef'
                        )
                        )
                        )
                        ), true);

                        $this->assertIdentical(trim($xml),
"<Campaign>\n\t<Name>Campaign Name</Name>\n\t<Subject>Campaign Subject</Subject>\n\t<Lists>".
"\n\t\t<List>\n\t\t\t<Name>Website Subscribers</Name>\n\t\t\t<ListID>abcdef</ListID>\n\t\t</List>".
"\n\t</Lists>\n</Campaign>");
    }

    function testDeserialisePlainText() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);
        $xml = 'id';

        $data = $ser->deserialise($xml);

        $this->assertIdentical($data, $xml, 'The returned text was not equal to the inputted text');
    }

    function testDeserialiseSingleElementWithText() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);
        $text = 'Testing Campaign';
        $element = 'CampaignName';
        $xml = '
<'.$element.'>'.$text.'</'.$element.'>
';

        $data = $ser->deserialise($xml);

        $this->assertArrayCount($data, 1);
        $this->assertElementExists($data, $element);
        $this->assertIdentical($data[$element], $text,
            'The contents of '.$element.' were not as expected. Expected '.$text.', was '.$data[$element]);
    }

    function testDeserialiseSingleElementWithSpecialChars() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);
        $text = 'Testing Campaign &stuff <script></script>';
        $element = 'CampaignName';
        $xml = '
<'.$element.'>'.htmlspecialchars($text).'</'.$element.'>
';

        $data = $ser->deserialise($xml);

        $this->assertArrayCount($data, 1);
        $this->assertElementExists($data, $element);
        $this->assertIdentical($data[$element], $text,
            'The contents of '.$element.' were not as expected. Expected '.$text.', was '.$data[$element]);
    }

    function testDeserialiseNestedElementsWithText() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);
        $xml = '
<Campaign>
    <Name>Testing Campaign</Name>
    <Subject>Campaign Subject</Subject>
    <Lists>
        <List>
            <ListID>abcdef</ListID>
            <Title>Website Subscribers</Title>
        </List>
    </Lists>
</Campaign>
';

        $data = $ser->deserialise($xml);

        $this->assertArrayCount($data, 1);
        $this->assertElementExists($data, 'Campaign');

        $this->assertArrayCount($data['Campaign'], 3);
        $this->assertElementExists($data['Campaign'], 'Name');
        $this->assertElementExists($data['Campaign'], 'Subject');
        $this->assertElementExists($data['Campaign'], 'Lists');

        $this->assertIdentical($data['Campaign']['Name'], 'Testing Campaign');
        $this->assertIdentical($data['Campaign']['Subject'], 'Campaign Subject');

        $this->assertArrayCount($data['Campaign']['Lists'], 1);
        $this->assertElementExists($data['Campaign']['Lists'], 'List');

        $this->assertArrayCount($data['Campaign']['Lists']['List'], 2);
        $this->assertElementExists($data['Campaign']['Lists']['List'], 'ListID');
        $this->assertElementExists($data['Campaign']['Lists']['List'], 'Title');

        $this->assertIdentical($data['Campaign']['Lists']['List']['ListID'], 'abcdef');
        $this->assertIdentical($data['Campaign']['Lists']['List']['Title'], 'Website Subscribers');
    }

    function testDeserialiseList() {
        $log = &new MockCS_REST_Log($this);
        $ser = &new CS_REST_XmlSerialiser($log);
        $xml = '
<Lists>
    <List>
        <ListID>abcdef</ListID>
        <Title>Website Subscribers</Title>
    </List>
    <List>
        <ListID>ghijkl</ListID>
        <Title>Conference Subscribers</Title>
    </List>
    <List>
        <ListID>mnopqrs</ListID>
        <Title>Service Agents</Title>
    </List>
</Lists>
';

        $data = $ser->deserialise($xml);

        $this->assertArrayCount($data, 3);
        $this->assertFalse(isset($data['Lists']));

        for($i = 0; $i < count($data); $i++) {
            $this->assertArrayCount($data[$i], 2);
            $this->assertElementExists($data[$i], 'ListID');
            $this->assertElementExists($data[$i], 'Title');
        }

        $this->assertIdentical($data[0]['ListID'], 'abcdef');
        $this->assertIdentical($data[0]['Title'], 'Website Subscribers');

        $this->assertIdentical($data[1]['ListID'], 'ghijkl');
        $this->assertIdentical($data[1]['Title'], 'Conference Subscribers');

        $this->assertIdentical($data[2]['ListID'], 'mnopqrs');
        $this->assertIdentical($data[2]['Title'], 'Service Agents');
    }

    function assertElementExists($array, $element) {
        $this->assertTrue(isset($array[$element]),
		    'Failed to find the expected element ('.$element.') in array.');
    }

    function assertArrayCount($array, $expectedCount) {
        $this->assertIsA($array, 'array');
        $this->assertEqual(count($array), $expectedCount,
            'The array was not of the required length. Expected '.$expectedCount.', was '.count($array));		
    }
}

endif;
