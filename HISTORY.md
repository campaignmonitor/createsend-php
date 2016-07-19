# createsend-php history

## v5.0.2 - 18 July, 2016

* Prevent definition re-declarations

## v5.0.1 - 14 December, 2015

* Changed the fatal error thrown when unable to communicate with the API into a catchable Exception

## v5.0.0 - 8 December, 2015

* Added PHP7 support

## v4.1.1 - 9 September, 2015

* Fixed the transactional smart email call for get_list() so it now passes the client id

## v4.1.0 - 5 August, 2014

* Added support for Transactional Email

## v4.0.2 - 29 October, 2014

* Updated constant definitions so PHP Notices aren't raised

## v4.0.1 - 30 May, 2014

* Updated the class_exists check to no longer call __autoload

## v4.0.0 - 6 Feb, 2014

* Updated to v3.1 API
* Added support for new segments structure
  * Segment now includes a new `RuleGroups` member, instead of a `Rules` member.

	    So for example, when you _previously_ would have created a segment like so:

	    ```php
		$result = $wrap->create('Segments List ID', array(
			'Title' => 'Segment Title',
			'Rules' => array(
				array(
					'Subject' => 'EmailAddress',
					'Clauses' => array(
						'CONTAINS example.com'
					)
				) ,
				array(
					'Subject' => '[customfield]',
					'Clauses' => array(
						'PROVIDED',
						'EQUALS 1'
					)
				)
			)
		));
	    ```

	    You would _now_ do this:

	    ```php
		$result = $wrap->create('Segments List ID', array(
			'Title' => 'Segment Title',
			'RuleGroups' => array(
				array(
					'Rules' => array(
						array(
							'RuleType' => 'EmailAddress',
							'Clause' => 'CONTAINS example.com'
						)
					)
				) ,
				array(
					'Rules' => array(
						array(
							'RuleType' => '[customfield]',
							'Clause' => 'PROVIDED'
						) ,
						array(
							'RuleType' => '[customfield]',
							'Clause' => 'EQUALS 1'
						)
					)
				)
			)
		));
    ```

  * The Add Rule call is now Add Rule Group, taking a `ruleGroup` argument instead of a `rule` argument.

    ```php
    function CS_REST_Segments->add_rulegroup($rulegroup)
    ```

    So for example, when you _previously_ would have added a rule like so:

    ```php
    $wrap = new CS_REST_Segments('Segment ID', $auth);
	$result = $wrap->add_rule(array(
	    'Subject' => 'EmailAddress',
	    'Clauses' => array('CONTAINS example.com')
	));
    ```

    You would _now_ do this:

    ```php
	$wrap = new CS_REST_Segments('Segment ID', $auth);
	$result = $wrap->add_rulegroup(array(
	    'Rules' => array(
	        array(
	            'RuleType' => 'EmailAddress',
	            'Clause' => 'CONTAINS example.com'
	        )
	    )
	));
    ```
* Removed the get_apikey method to promote usage of oAuth authentication

## v3.1.3 - 10 Dec, 2013

* Fixed a notice that was raised when unscheduling a campaign.

## v3.1.2 - 28 Oct, 2013

* Only set the ```CURLOPT_CAINFO``` option if not already set globally via the ini system.

## v3.1.1 - 3 Oct, 2013

* Made it harder to accidentally email your subscribers when using the example code

## v3.1.0 - 15 Apr, 2013

* Added support for [single sign on](http://www.campaignmonitor.com/api/account/#single_sign_on) which allows initiation of external login sessions to Campaign Monitor.

## v3.0.0 - 25 Mar, 2013

* Added support for authenticating using OAuth. See the [README](README.md#authenticating) for full usage instructions.
  * This introduces some changes to how you authenticate using this library. You now authenticate by passing an `$auth` array as the first argument when creating instances of any classes which inherit from the `CS_REST_Wrapper_Base` class.

      So in existing code, when you _previously_ would have authenticated using an API key as follows:

      ```php
      $wrap = new CS_REST_General('Your API Key');
      $result = $wrap->get_clients();
      ```

      If you want to authenticate using an API key, you should _now_ do this:

      ```php
      $wrap = new CS_REST_General(array('api_key' => 'Your API Key'));
      $result = $wrap->get_clients();
      ```

## v2.5.2 - 19 Dec, 2012

* Removed simpletest source files, and added simpletest as a dev dependency.
* Fixed autoloading with composer, so that only necessary classes are loaded.

## v2.5.1 - 14 Dec, 2012

* Use CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT constants instead of
CURLOPT_TIMEOUT_MS and CURLOPT_CONNECTTIMEOUT_MS.
* Added autoloading support when using Composer (PHP dependency management).

## v2.5.0 - 11 Dec, 2012

* Added support for including from name, from email, and reply to email in
drafts, scheduled, and sent campaigns.
* Added support for campaign text version urls.
* Added support for transferring credits to/from a client.
* Added support for getting account billing details as well as client credits.
* Made all date fields optional when getting paged results.

## v2.4.0 - 5 Nov, 2012

* Added CS_REST_Campaigns.get_email_client_usage().
* Added support for ReadsEmailWith field on subscriber objects.
* Added support for retrieving unconfirmed subscribers for a list.
* Added support for suppressing email addresses.
* Added support for retrieving spam complaints for a campaign, as well as
adding SpamComplaints field to campaign summary output.
* Added VisibleInPreferenceCenter field to custom field output.
* Added support for setting preference center visibility when creating custom
fields.
* Added the ability to update a custom field name and preference visibility.
* Added documentation explaining that TextUrl is now optional when creating a
campaign.

## v2.3.2 - 23 Oct, 2012

* Fixed timeout issue by setting CS_REST_SOCKET_TIMEOUT to 10 seconds.

## v2.3.1 - 19 Oct, 2012

* Fixed #13. Load services_json.php only if Services_JSON class doesn't already
exist.
* Fixed issue with curl calls hangs hanging on proxy failure.

## v2.3.0 - 10 Oct, 2012

* Added support for creating campaigns from templates.
* Added support for unsuppressing an email address.

## 1.1.3 - 26 Sep, 2012

* Backported fix to use Mozilla certificate bundle, as per
http://curl.haxx.se/docs/caextract.html

## v2.2.0 - 17 Sep, 2012

* Added WorldviewURL field to campaign summary response.
* Added Latitude, Longitude, City, Region, CountryCode, and CountryName
fields to campaign opens and clicks responses.

## 2.1.1 - 11 Sep, 2012

* Added 'Contributing' section to README.
* Used the Mozilla certificate bundle, as per
http://curl.haxx.se/docs/caextract.html
* Bumping to 2.1.1

## v2.1.0 - 30 Aug, 2012

* Added support for basic / unlimited pricing.

## v2.0.0 - 23 Aug, 2012

* Removing deprecated method CS_REST_Clients.set_access().
* Removed traces of calling the API in a deprecated manner.

## v1.2.0 - 22 Aug, 2012

* Added support for UnsubscribeSetting field when creating, updating and
getting list details.
* Added support for AddUnsubscribesToSuppList and ScrubActiveWithSuppList
fields when updating a list.
* Added support for finding all client lists to which a subscriber with a
specific email address belongs.

## v1.1.2 - 23 Jul, 2012

* Added support for specifying whether subscription-based autoresponders
should be restarted when adding or updating subscribers.

## v1.1.1 - 16 Jul, 2012

* Added Travis CI support.

## v1.1.0 - 11 Jul, 2012

* Added support for team management.

## 1.0.14 - 18 Mar, 2012

* Added support for new API methods.
* Fixed subscriber import sample.

## 1.0.12 - 12 Sep, 2011

* Fixed response handling code so that it can deal with HTTP responses
beginning with "HTTP/1.1 Continue".

## 1.0.11 - 25 Aug, 2011

* Fixed socket issue by added Connection:Close header for raw socket
transport.

## 1.0.10 - 12 Jul, 2011

* Fixed #5. Updated recursive check_encoding call.
* Fixed #7. Modified template create/update to not require screenshot URL.

## 1.0.9 - 18 Jun, 2011

* Fixed #4. Removed static function calls.

## 1.0.8 - 6 Jun, 2011

* Initial release which supports current Campaign Monitor API.
