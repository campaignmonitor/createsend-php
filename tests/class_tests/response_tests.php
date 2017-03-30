<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/simpletest/simpletest/autorun.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';

@Mock::generate('CS_REST_Log');

class CS_REST_TestResponseDeserialisation extends UnitTestCase {
    var $responses;
    var $deserialiser;

    function setUp() {
    	$util_responses = array(
    			'clients' => array(
    					array(
    							'ClientID' => '4a397ccaaa55eb4e6aa1221e1e2d7122',
    							'Name' => 'Client One'
    					),
    					array(
    							'ClientID' => 'a206def0582eec7dae47d937a4109cb2',
    							'Name' => 'Client Two'
    					)
    			),
    			'apikey' => array(
    					'ApiKey' => '981298u298ue98u219e8u2e98u2'
    			),
    			'systemdate' => array(
    					'SystemDate' => '2010-10-15 09:27:00'
    			),
    			'custom_api_error' => array(
    					'Code' => 98798,
    					'Message' => 'A crazy API error'
    			),
    			'countries' => array(
    					"Afghanistan",
    					"Albania",
    					"Algeria",
    					"American Samoa",
    					"Andorra",
    					"Angola",
    					"Anguilla",
    					"Antigua & Barbuda",
    					"Argentina"
    			),
    			'timezones' => array(
    					"(GMT) Casablanca",
    					"(GMT) Coordinated Universal Time",
    					"(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London",
    					"(GMT) Monrovia, Reykjavik",
    					"(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
    					"(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
    					"(GMT+01:00) Brussels, Copenhagen, Madrid, Paris"
    			)
    	);

    	$client_responses = array(
    			'client_details' => array(
    					'ApiKey' => '7c86c29e930f4a1c3836eb57e9e3f4b283b06857489a750e',
    					'BasicDetails' => array(
    							'ClientID' => '4a397ccaaa55eb4e6aa1221e1e2d7122',
    							'CompanyName' => 'Client One',
    							'Country' => 'Australia',
    							'TimeZone' => '(GMT+10:00) Canberra, Melbourne, Sydney'
    					),
    					'BillingDetails' => array(
    							'CanPurchaseCredits' => true,
    							'MarkupOnDesignSpamTest' => 0,
    							'ClientPays' => true,
    							'BaseRatePerRecipient' => 1,
    							'MarkupPerRecipient' => 0,
    							'MarkupOnDelivery' => 0,
    							'BaseDeliveryRate' => 5,
    							'Currency' => 'USD',
    							'BaseDesignSpamTestRate' => 5
    					)
    			),
    			'create_client' => '32a381c49a2df99f1d0c6f3c112352b9',
    			'campaigns' => array(
    					array(
    							'WebVersionURL' => 'http://hello.createsend.com/t/ViewEmail/r/765E86829575EE2C/C67FD2F38AC4859C/',
  							  'WebVersionTextURL' => 'http://createsend.com/t/r-765E86829575EE2C/t',
    							'CampaignID' => 'fc0ce7105baeaf97f47c99be31d02a91',
    							'Subject' => 'Campaign One',
    							'Name' => 'Campaign One',
                  'FromName' => 'My Name',
                  'FromEmail' => 'myemail@example.com',
                  'ReplyTo' => 'myemail@example.com',
    							'SentDate' => '2010-10-12 12:58:00',
    							'TotalRecipients' => 2245
    					),
    					array(
    							'WebVersionURL' => 'http://hello.createsend.com/t/ViewEmail/r/DD543566A87C9B8B/C67FD2F38AC4859C/',
    							'WebVersionTextURL' => 'http://createsend.com/t/r-DD543566A87C9B8B/t',
    							'CampaignID' => '072472b88c853ae5dedaeaf549a8d607',
    							'Subject' => 'Campaign Two',
    							'Name' => 'Campaign Two',
                  'FromName' => 'My Name',
                  'FromEmail' => 'myemail@example.com',
                  'ReplyTo' => 'myemail@example.com',
    							'SentDate' => '2010-10-06 16:20:00',
    							'TotalRecipients' => 11222
    					)
    			),
    			'scheduled' => array(
    					array(
    							"DateScheduled" => "2011-05-25 10:40:00",
    							"ScheduledTimeZone" => "(GMT+10:00) Canberra, Melbourne, Sydney",
    							"CampaignID" => "827dbbd2161ea9989fa11ad562c66937",
    							"Name" => "Magic Issue One",
    							"Subject" => "Magic Issue One",
                  'FromName' => 'My Name',
                  'FromEmail' => 'myemail@example.com',
                  'ReplyTo' => 'myemail@example.com',
    							"DateCreated" => "2011-05-24 10:37:00",
    							"PreviewURL" => "http://createsend.com/t/r-DD543521A87C9B8B",
  							  "PreviewTextURL" => "http://createsend.com/t/r-DD543521A87C9B8B/t"
    					),
    					array(
    							"DateScheduled" => "2011-05-29 11:20:00",
    							"ScheduledTimeZone" => "(GMT+10:00) Canberra, Melbourne, Sydney",
    							"CampaignID" => "4f54bbd2161e65789fa11ad562c66937",
    							"Name" => "Magic Issue Two",
    							"Subject" => "Magic Issue Two",
                  'FromName' => 'My Name',
                  'FromEmail' => 'myemail@example.com',
                  'ReplyTo' => 'myemail@example.com',
    							"DateCreated" => "2011-05-24 10:39:00",
    							"PreviewURL" => "http://createsend.com/t/r-DD913521A87C9B8B",
    							"PreviewTextURL" => "http://createsend.com/t/r-DD913521A87C9B8B/t"
    					)
    			),
    			'drafts' => array(
    					array(
    							"CampaignID" => "7c7424792065d92627139208c8c01db1",
    							"Name" => "Draft One",
    							"Subject" => "Draft One",
                  'FromName' => 'My Name',
                  'FromEmail' => 'myemail@example.com',
                  'ReplyTo' => 'myemail@example.com',
    							"DateCreated" => "2010-08-19 16:08:00",
    							"PreviewURL" => "http://hello.createsend.com/t/ViewEmail/r/E97A7BB2E6983DA1/C67FD2F38AC4859C/",
    							"PreviewTextURL" => "http://createsend.com/t/r-E97A7BB2E6983DA1/t"
    					),
    					array(
    							"CampaignID" => "2e928e982065d92627139208c8c01db1",
    							"Name" => "Draft Two",
    							"Subject" => "Draft Two",
                  'FromName' => 'My Name',
                  'FromEmail' => 'myemail@example.com',
                  'ReplyTo' => 'myemail@example.com',
    							"DateCreated" => "2010-08-19 16:08:00",
    							"PreviewURL" => "http://hello.createsend.com/t/ViewEmail/r/E97A7BB2E6983DA1/C67FD2F38AC4859C/",
    							"PreviewTextURL" => "http://createsend.com/t/r-E97A7BB2E6983DA1/t"
    					)
    			),
    			'lists' => array(
    					array(
    							"ListID" => "a58ee1d3039b8bec838e6d1482a8a965",
    							"Name" => "List One"
    					),
    					array(
    							"ListID" => "99bc35084a5739127a8ab81eae5bd305",
    							"Name" => "List Two"
    					)
    			),
    			'segments' => array(
    					array(
    							'ListID' => 'a58ee1d3039b8bec838e6d1482a8a965',
    							'SegmentID' => '46aa5e01fd43381863d4e42cf277d3a9',
    							'Title' => 'Segment One'
    					),
    					array(
    							'ListID' => '8dffb94c60c5faa3d40f496f2aa58a8a',
    							'SegmentID' => 'dhw9q8jd9q8wd09quw0d909wid9i09iq',
    							'Title' => 'Segment Two'
    					)
    			),
    			'suppressionlist' => array(
    					"Results" => array(
    							array(
    									"SuppressionReason" => "Unsubscribed",
    									"EmailAddress" => "example+1@example.com",
    									"Date" => "2010-10-26 10:55:31",
    									"State" => "Suppressed"
    							),
    							array(
    									"SuppressionReason" => "Unsubscribed",
    									"EmailAddress" => "example+2@example.com",
    									"Date" => "2010-10-26 10:55:31",
    									"State" => "Suppressed"
    							),
    							array(
    									"SuppressionReason" => "Unsubscribed",
    									"EmailAddress" => "example+3@example.com",
    									"Date" => "2010-10-26 10:55:31",
    									"State" => "Suppressed"
    							),
    							array(
    									"SuppressionReason" => "Unsubscribed",
    									"EmailAddress" => "subscriber@example.com",
    									"Date" => "2010-10-25 13:11:04",
    									"State" => "Suppressed"
    							),
    							array(
    									"SuppressionReason" => "Unsubscribed",
    									"EmailAddress" => "subscriberone@example.com",
    									"Date" => "2010-10-25 13:04:15",
    									"State" => "Suppressed"
    							)
    					),
    					"ResultsOrderedBy" => "email",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 5,
    					"TotalNumberOfRecords" => 5,
    					"NumberOfPages" => 1
    			)
    	);

    	$subscriber_responses = array(
    			'active_subscribers' => array(
    					"Results" => array(
    							array(
    									"EmailAddress" => "subs+7t8787Y@example.com",
    									"Name" => "Person One",
    									"Date" => "2010-10-25 10:28:00",
    									"State" => "Active",
    									"CustomFields" => array(
    											array(
    													"Key" => "website",
    													"Value" => "http://example.com"
    											),
    											array(
    													"Key" => "age",
    													"Value" => "24"
    											),
    											array(
    													"Key" => "subscription date",
    													"Value" => "2010-03-09"
    											)
    									)
    							),
    							array(
    									"EmailAddress" => "subs+7878787y8ggg@example.com",
    									"Name" => "Person Two",
    									"Date" => "2010-10-25 12:17:00",
    									"State" => "Active",
    									"CustomFields" => array(
    											array(
    													"Key" => "website",
    													"Value" => "http://subdomain.example.com"
    											)
    									)
    							),
    							array(
    									"EmailAddress" => "subs+7890909i0ggg@example.com",
    									"Name" => "Person Three",
    									"Date" => "2010-10-25 12:52:00",
    									"State" => "Active",
    									"CustomFields" => array(
    											array(
    													"Key" => "website",
    													"Value" => "http://subdomain.example.com"
    											)
    									)
    							),
    							array(
    									"EmailAddress" => "subs@example.com",
    									"Name" => "Person Four",
    									"Date" => "2010-10-27 13:13:00",
    									"State" => "Active",
    									"CustomFields" => array()
    							),
    							array(
    									"EmailAddress" => "joey@example.com",
    									"Name" => "Person Five",
    									"Date" => "2010-10-27 13:13:00",
    									"State" => "Active",
    									"CustomFields" => array()
    							)
    					),
    					"ResultsOrderedBy" => "email",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 5,
    					"TotalNumberOfRecords" => 5,
    					"NumberOfPages" => 1
    			),
    			'add_subscriber' => 'subscriber@example.com',
    			'import_subscribers' => array(
    					'FailureDetails' => array(),
    					'TotalUniqueEmailsSubmitted' => 3,
    					'TotalExistingSubscribers' => 0,
    					'TotalNewSubscribers' => 3,
    					'DuplicateEmailsInSubmission' => array()
    			),
    			'import_subscribers_partial_success' => array(
    					"ResultData" => array(
    							"TotalUniqueEmailsSubmitted" => 3,
    							"TotalExistingSubscribers" => 2,
    							"TotalNewSubscribers" => 0,
    							"DuplicateEmailsInSubmission" => array(),
    							"FailureDetails" => array(
    									array(
    											"EmailAddress" => "example+1@example",
    											"Code" => 1,
    											"Message" => "Invalid Email Address"
    									)
    							)
    					),
    					"Code" => 210,
    					"Message" => "Subscriber Import had some failures"
    			),
    			'subscriber_details' => array(
    					'EmailAddress' => 'subscriber@example.com',
    					'Name' => 'Subscriber One',
    					'Date' => '2010-10-25 10:28:00',
    					'State' => 'Active',
    					'CustomFields' => array(
    							array(
    									'Key' => 'website',
    									'Value' => 'http://example.com'
    							),
    							array(
    									'Key' => 'age',
    									'Value' => '24'
    							),
    							array(
    									'Key' => 'subscription date',
    									'Value' => '2010-03-09'
    							)
    					)
    			),
    			'subscriber_history' => array(
    					array(
    							'ID' => 'fc0ce7105baeaf97f47c99be31d02a91',
    							'Type' => 'Campaign',
    							'Name' => 'Campaign One',
    							'Actions' => array(
    									array(
    											'Event' => 'Open',
    											'Date' => '2010-10-12 13:18:00',
    											'IPAddress' => '192.168.126.87',
    											'Detail' => ''
    									),
    									array(
    											'Event' => 'Click',
    											'Date' => '2010-10-12 13:16:00',
    											'IPAddress' => '192.168.126.87',
    											'Detail' => 'http://example.com/post/12323/'
    									),
    									array(
    											'Event' => 'Click',
    											'Date' => '2010-10-12 13:15:00',
    											'IPAddress' => '192.168.126.87',
    											'Detail' => 'http://example.com/post/29889/'
    									),
    									array(
    											'Event' => 'Open',
    											'Date' => '2010-10-12 13:15:00',
    											'IPAddress' => '192.168.126.87',
    											'Detail' => ''
    									),
    									array(
    											'Event' => 'Click',
    											'Date' => '2010-10-12 13:01:00',
    											'IPAddress' => '192.168.126.87',
    											'Detail' => 'http://example.com/post/82211/'
    									),
    									array(
    											'Event' => 'Open',
    											'Date' => '2010-10-12 13:01:00',
    											'IPAddress' => '192.168.126.87',
    											'Detail' => ''
    									)
    							)
    					)
    			)
    	);

    	$list_responses = array (
    			'custom_fields' => array(
    					array(
    							"FieldName" => "website",
    							"Key" => "[website]",
    							"DataType" => "Text",
    							"FieldOptions" => array()
    					),
    					array(
    							"FieldName" => "age",
    							"Key" => "[age]",
    							"DataType" => "Number",
    							"FieldOptions" => array()
    					),
    					array(
    							"FieldName" => "subscription date",
    							"Key" => "[subscriptiondate]",
    							"DataType" => "Date",
    							"FieldOptions" => array()
    					)
    			),
    			'create_list' => 'e3c5f034d68744f7881fdccf13c2daee',
    			'create_custom_field' => '[newdatefield]',
    			'list_details' => array(
    					'ConfirmedOptIn' => false,
    					'Title' => 'a non-basic list :)',
    					'UnsubscribePage' => '',
    					'ListID' => '2fe4c8f0373ce320e2200596d7ef168f',
    					'ConfirmationSuccessPage' => ''
    			),
    			'list_stats' => array(
    					"TotalActiveSubscribers" => 6,
    					"NewActiveSubscribersToday" => 0,
    					"NewActiveSubscribersYesterday" => 8,
    					"NewActiveSubscribersThisWeek" => 8,
    					"NewActiveSubscribersThisMonth" => 8,
    					"NewActiveSubscribersThisYear" => 8,
    					"TotalUnsubscribes" => 2,
    					"UnsubscribesToday" => 0,
    					"UnsubscribesYesterday" => 2,
    					"UnsubscribesThisWeek" => 2,
    					"UnsubscribesThisMonth" => 2,
    					"UnsubscribesThisYear" => 2,
    					"TotalDeleted" => 0,
    					"DeletedToday" => 0,
    					"DeletedYesterday" => 0,
    					"DeletedThisWeek" => 0,
    					"DeletedThisMonth" => 0,
    					"DeletedThisYear" => 0,
    					"TotalBounces" => 0,
    					"BouncesToday" => 0,
    					"BouncesYesterday" => 0,
    					"BouncesThisWeek" => 0,
    					"BouncesThisMonth" => 0,
    					"BouncesThisYear" => 0
    			),
    			'bounced_subscribers' => array(
    					'Results' => array(
    							array(
    									'EmailAddress' => 'bouncedsubscriber@example.com',
    									'Name' => 'Bounced One',
    									'Date' => '2010-10-25 13:11:00',
    									'State' => 'Bounced',
    									'CustomFields' => array()
    							)
    					),
    					'ResultsOrderedBy' => 'email',
    					'OrderDirection' => 'asc',
    					'PageNumber' => 1,
    					'PageSize' => 1000,
    					'RecordsOnThisPage' => 1,
    					'TotalNumberOfRecords' => 1,
    					'NumberOfPages' => 1
    			),
    			'unsubscribed_subscribers' => array(
    					"Results" => array(
    							array(
    									"EmailAddress" => "subscriber@example.com",
    									"Name" => "Unsub One",
    									"Date" => "2010-10-25 13:11:00",
    									"State" => "Unsubscribed",
    									"CustomFields" => array()
    							),
    							array(
    									"EmailAddress" => "subscriberone@example.com",
    									"Name" => "Subscriber",
    									"Date" => "2010-10-25 13:04:00",
    									"State" => "Unsubscribed",
    									"CustomFields" => array(
    											array(
    													"Key" => "website",
    													"Value" => "http://google.com"
    											)
    									)
    							),
    							array(
    									"EmailAddress" => "example+1@example.com",
    									"Name" => "Example One",
    									"Date" => "2010-10-26 10:56:00",
    									"State" => "Unsubscribed",
    									"CustomFields" => array()
    							),
    							array(
    									"EmailAddress" => "example+2@example.com",
    									"Name" => "Example Two",
    									"Date" => "2010-10-26 10:56:00",
    									"State" => "Unsubscribed",
    									"CustomFields" => array()
    							),
    							array(
    									"EmailAddress" => "example+3@example.com",
    									"Name" => "Example Three",
    									"Date" => "2010-10-26 10:56:00",
    									"State" => "Unsubscribed",
    									"CustomFields" => array()
    							)
    					),
    					"ResultsOrderedBy" => "email",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 5,
    					"TotalNumberOfRecords" => 5,
    					"NumberOfPages" => 1
    			),
    			'list_webhooks' => array(
    					array(
    							"WebhookID" => "943678317049bc13",
    							"Events" => array(
    									"Bounce",
    									"Spam"
    							),
    							"Url" => "http://www.postbin.org/d9w8ud9wud9w",
    							"Status" => "Active",
    							"PayloadFormat" => "Json"
    					),
    					array(
    							"WebhookID" => "ee1b3864e5ca6161",
    							"Events" => array(
    									"Subscribe"
    							),
    							"Url" => "http://www.postbin.org/hiuhiu2h2u",
    							"Status" => "Active",
    							"PayloadFormat" => "Xml"
    					)
    			),
    			'create_list_webhook' => '6a783d359bd44ef62c6ca0d3eda4412a'
    	);

    	$campaign_responses = array(
    			'create_campaign' => '787y87y87y87y87y87y87',
    			'campaign_unsubscribes' => array(
    					'Results' => array(
    							array(
    									'EmailAddress' => 'subs+6576576576@example.com',
    									'ListID' => '512a3bc577a58fdf689c654329b50fa0',
    									'Date' => '2010-10-11 08:29:00',
    									'IPAddress' => '192.168.126.87'
    							)
    					),
    					'ResultsOrderedBy' => 'date',
    					'OrderDirection' => 'asc',
    					'PageNumber' => 1,
    					'PageSize' => 1000,
    					'RecordsOnThisPage' => 1,
    					'TotalNumberOfRecords' => 1,
    					'NumberOfPages' => 1
    			),
    			'campaign_summary' => array(
    					'Recipients' => 5,
    					'TotalOpened' => 10,
    					'Clicks' => 0,
    					'Unsubscribed' => 0,
    					'Bounced' => 0,
    					'UniqueOpened' => 5,
    					'WebVersionURL' => 'http://clientone.createsend.com/t/ViewEmail/r/3A433FC72FFE3B8B/C67FD2F38AC4859C/',
    					'WebVersionTextURL' => 'http://createsend.com/t/r-3A433FC72FFE3B8B/t',
    					'WorldviewURL' => 'http://clientone.createsend.com/reports/wv/r/3A433FC72FFE3B8B',
    					'ForwardToAFriends' => 18,
    					'FacebookLikes' => 25,
    					'TwitterTweets' => 11
    			),
    			'campaign_listsandsegments' => array(
    					'Lists' => array(
    							array(
    									'ListID' => 'a58ee1d3039b8bec838e6d1482a8a965',
    									'Name' => 'List One'
    							)
    					),
    					'Segments' => array(
    							array(
    									'ListID' => '2bea949d0bf96148c3e6a209d2e82060',
    									'SegmentID' => 'dba84a225d5ce3d19105d7257baac46f',
    									'Title' => 'Segment for campaign'
    							)
    					)
    			),
    			'campaign_opens' => array(
    					"Results" => array(
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-11 08:29:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							),
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-08 14:24:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							),
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-07 10:20:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							),
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-07 07:15:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							),
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-07 06:58:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							)
    					),
    					"ResultsOrderedBy" => "date",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 5,
    					"TotalNumberOfRecords" => 5,
    					"NumberOfPages" => 1
    			),
    			'campaign_recipients' => array(
    					'Results' => array(
    							array(
    									"EmailAddress" => "subs+6g76t7t0@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t10@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t100@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1000@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1001@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1002@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1003@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1004@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1005@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1006@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1007@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1008@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1009@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t101@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1010@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1011@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1012@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1013@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							),
    							array(
    									"EmailAddress" => "subs+6g76t7t1014@example.com",
    									"ListID" => "a994a3caf1328a16af9a69a730eaa706"
    							)
    					),
    					'ResultsOrderedBy' => 'email',
    					'OrderDirection' => 'asc',
    					'PageNumber' => 1,
    					'PageSize' => 20,
    					'RecordsOnThisPage' => 20,
    					'TotalNumberOfRecords' => 2200,
    					'NumberOfPages' => 110
    			),
    			'campaign_clicks' => array(
    					"Results" => array(
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"URL" => "http://video.google.com.au/?hl=en&tab=wv",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-11 08:29:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							),
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"URL" => "http://mail.google.com/mail/?hl=en&tab=wm",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-11 08:29:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							),
    							array(
    									"EmailAddress" => "subs+6576576576@example.com",
    									"URL" => "http://mail.google.com/mail/?hl=en&tab=wm",
    									"ListID" => "512a3bc577a58fdf689c654329b50fa0",
    									"Date" => "2010-10-06 17:24:00",
    									"IPAddress" => "192.168.126.87",
                      "Latitude" => -33.8683,
                      "Longitude" => 151.2086,
                      "City" => "Sydney",
                      "Region" => "New South Wales",
                      "CountryCode" => "AU",
                      "CountryName" => "Australia"
    							)
    					),
    					"ResultsOrderedBy" => "date",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 3,
    					"TotalNumberOfRecords" => 3,
    					"NumberOfPages" => 1
    			),
    			'campaign_bounces' => array(
    					"Results" => array(
    							array(
    									"EmailAddress" => "asdf@softbouncemyemail.com",
    									"ListID" => "654523a5855b4a440bae3fb295641546",
    									"BounceType" => "Soft",
    									"Date" => "2010-07-02 16:46:00",
    									"Reason" => "Bounce - But No Email Address Returned "
    							),
    							array(
    									"EmailAddress" => "asdf@hardbouncemyemail.com",
    									"ListID" => "654523a5855b4a440bae3fb295641546",
    									"BounceType" => "Soft",
    									"Date" => "2010-07-02 16:46:00",
    									"Reason" => "Soft Bounce - General"
    							)
    					),
    					"ResultsOrderedBy" => "date",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 2,
    					"TotalNumberOfRecords" => 2,
    					"NumberOfPages" => 1
    			)
    	);

    	$segment_responses = array(
    			'segment_subscribers' => array(
    					"Results" => array(
    							array(
    									"EmailAddress" => "personone@example.com",
    									"Name" => "Person One",
    									"Date" => "2010-10-27 13:13:00",
    									"State" => "Active",
    									"CustomFields" => array()
    							),
    							array(
    									"EmailAddress" => "persontwo@example.com",
    									"Name" => "Person Two",
    									"Date" => "2010-10-27 13:13:00",
    									"State" => "Active",
    									"CustomFields" => array()
    							)
    					),
    					"ResultsOrderedBy" => "email",
    					"OrderDirection" => "asc",
    					"PageNumber" => 1,
    					"PageSize" => 1000,
    					"RecordsOnThisPage" => 2,
    					"TotalNumberOfRecords" => 2,
    					"NumberOfPages" => 1
    			),
    			'create_segment' => '0246c2aea610a3545d9780bf6ab89006'
    	);

    	$template_responses = array(
    			'create_template' => '98y2e98y289dh89h938389',
    			'template_details' => array(
    					'TemplateID' => '98y2e98y289dh89h938389',
    					'Name' => 'Template One',
    					'PreviewURL' => 'http://preview.createsend.com/createsend/templates/previewTemplate.aspx?ID=01AF532CD8889B33&d=r&c=E816F55BFAD1A753',
    					'ScreenshotURL' => 'http://preview.createsend.com/ts/r/14/833/263/14833263.jpg?0318092600'
    			),
    			'templates' => array(
    					array(
    							"TemplateID" => "5cac213cf061dd4e008de5a82b7a3621",
    							"Name" => "Template One",
    							"PreviewURL" => "http://preview.createsend.com/createsend/templates/previewTemplate.aspx?ID=01AF532CD8889B33&d=r&c=E816F55BFAD1A753",
    							"ScreenshotURL" => "http://preview.createsend.com/ts/r/14/833/263/14833263.jpg?0318092541"
    					),
    					array(
    							"TemplateID" => "da645c271bc85fb6550acff937c2ab2e",
    							"Name" => "Template Two",
    							"PreviewURL" => "http://preview.createsend.com/createsend/templates/previewTemplate.aspx?ID=C8A180629495E798&d=r&c=E816F55BFAD1A753",
    							"ScreenshotURL" => "http://preview.createsend.com/ts/r/18/7B3/552/187B3552.jpg?0705043527"
    					)
    			)
    	);

    	$this->responses = array_merge(
    			array_merge(
    					array_merge(
    							array_merge(
    									array_merge(
    											array_merge(
    													$util_responses,
    													$client_responses
    											),
    											$subscriber_responses
    									),
    									$list_responses
    							),
    							$campaign_responses
    					),
    					$segment_responses
    			),
    			$template_responses
    	);
    }

    
    function do_test_response_deserialisation() {
    	if(!is_null($this->deserialiser)) {
    		$response_dir = 'responses/';
    		foreach ($this->responses as $k => $v) {
    			$filename = $response_dir.$k.'.json';
    			if(file_exists($filename)) {
    				$response = file_get_contents($filename);
    				$result = $this->deserialiser->deserialise($response);
    				$this->assert_identical_ignoring_type($result, $v,
    						'Failed to deserialise response for '.$k);
    			}
    		}
    	}
    }

    function test_services_json_serializer() {
        $log = new MockCS_REST_Log($this);
        $this->deserialiser = new CS_REST_ServicesJsonSerialiser($log);
        $this->do_test_response_deserialisation();
    }

    function test_services_native_serializer() {
        if(function_exists('json_decode') && function_exists('json_encode')):
            $log = new MockCS_REST_Log($this);
            $this->deserialiser = new CS_REST_NativeJsonSerialiser($log);
            $this->do_test_response_deserialisation();
        endif;
    }
    
    function assert_identical_ignoring_type($object, $expected, $message) {
        if(is_array($expected)) {
            if(isset($expected[0])) {
                $this->assertIsA($object, 'array', $message.' Item is not an array');
                $this->assertIdentical(count($expected), count($object), $message.' Invalid array length');
                for($i = 0; $i < count($expected); $i++) {
                    $this->assert_identical_ignoring_type($object[$i], $expected[$i], 
                        $message.' Checking #'.$i);
                }
            } else {
                foreach($expected as $k => $v) {
                    $this->assert_identical_ignoring_type($object->$k, $v, $message.' Checking '.$k);
                }
            }
        } else {
            $this->assertEqual($expected, $object, $message.' Items are not equal');
        }
    }
}
