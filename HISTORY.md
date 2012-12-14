# createsend-php history

## v2.5.1 - 14 Dec, 2012   (099dfd9)

* Use CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT constants instead of
CURLOPT_TIMEOUT_MS and CURLOPT_CONNECTTIMEOUT_MS.
* Added autoloading support when using Composer (PHP dependency management).

## v2.5.0 - 11 Dec, 2012   (ab270ff)

* Added support for including from name, from email, and reply to email in
drafts, scheduled, and sent campaigns.
* Added support for campaign text version urls.
* Added support for transferring credits to/from a client.
* Added support for getting account billing details as well as client credits.
* Made all date fields optional when getting paged results.

## v2.4.0 - 5 Nov, 2012   (98de84d1)

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

## v2.3.2 - 23 Oct, 2012   (2088ae69)

* Fixed timeout issue by setting CS_REST_SOCKET_TIMEOUT to 10 seconds.

## v2.3.1 - 19 Oct, 2012   (c9ca4b2b)

* Fixed #13. Load services_json.php only if Services_JSON class doesn't already
exist.
* Fixed issue with curl calls hangs hanging on proxy failure.

## v2.3.0 - 10 Oct, 2012   (a7e03d5c)

* Added support for creating campaigns from templates.
* Added support for unsuppressing an email address.

## 1.1.3 - 26 Sep, 2012   (b37ec49d)

* Backported fix to use Mozilla certificate bundle, as per
http://curl.haxx.se/docs/caextract.html

## v2.2.0 - 17 Sep, 2012   (e9bf1874)

* Added WorldviewURL field to campaign summary response.
* Added Latitude, Longitude, City, Region, CountryCode, and CountryName
fields to campaign opens and clicks responses.

## 2.1.1 - 11 Sep, 2012   (ba29e917)

* Added 'Contributing' section to README.
* Used the Mozilla certificate bundle, as per
http://curl.haxx.se/docs/caextract.html
* Bumping to 2.1.1

## v2.1.0 - 30 Aug, 2012   (c152d8d5)

* Added support for basic / unlimited pricing.

## v2.0.0 - 23 Aug, 2012   (44b5c94b)

* Removing deprecated method CS_REST_Clients.set_access().
* Removed traces of calling the API in a deprecated manner.

## v1.2.0 - 22 Aug, 2012   (6c42ce07)

* Added support for UnsubscribeSetting field when creating, updating and
getting list details.
* Added support for AddUnsubscribesToSuppList and ScrubActiveWithSuppList
fields when updating a list.
* Added support for finding all client lists to which a subscriber with a
specific email address belongs.

## v1.1.2 - 23 Jul, 2012   (baf9f78d)

* Added support for specifying whether subscription-based autoresponders
should be restarted when adding or updating subscribers.

## v1.1.1 - 16 Jul, 2012   (0bbe08d6)

* Added Travis CI support.

## v1.1.0 - 11 Jul, 2012   (b67d6c47)

* Added support for team management.

## 1.0.14 - 18 Mar, 2012   (e4252531)

* Added support for new API methods.
* Fixed subscriber import sample.

## 1.0.12 - 12 Sep, 2011   (bd47f870)

* Fixed response handling code so that it can deal with HTTP responses
beginning with "HTTP/1.1 Continue".

## 1.0.11 - 25 Aug, 2011   (a86067a8)

* Fixed socket issue by added Connection:Close header for raw socket
transport.

## 1.0.10 - 12 Jul, 2011   (2f9a6340)

* Fixed #5. Updated recursive check_encoding call.
* Fixed #7. Modified template create/update to not require screenshot URL.

## 1.0.9 - 18 Jun, 2011   (eca226a7)

* Fixed #4. Removed static function calls.

## 1.0.8 - 6 Jun, 2011   (5d728024)

* Initial release which supports current Campaign Monitor API.
