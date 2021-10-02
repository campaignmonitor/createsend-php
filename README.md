# createsend [![Build Status](https://app.travis-ci.com/campaignmonitor/createsend-php.svg?branch=master)](https://app.travis-ci.com/campaignmonitor/createsend-php)
A PHP library which implements the complete functionality of the [Campaign Monitor API](http://www.campaignmonitor.com/api/).

## Installation

### Composer
If you use [Composer](http://getcomposer.org/), you can run the following command from the root of your project:

```
composer require campaignmonitor/createsend-php
```

Or add [campaignmonitor/createsend-php](https://packagist.org/packages/campaignmonitor/createsend-php) to your `composer.json` file:

```json
{
    "require": {
        "campaignmonitor/createsend-php": "{version}"
    }
}
```

Followed by running:

```
composer update
```

### Manual Installation
Otherwise you can simply [download](https://github.com/campaignmonitor/createsend-php/tags) the library and include it in your project.

After you have installed the library, simply include the relevant API class, as follows:

```php
require_once 'csrest_campaigns.php'
```

## Authenticating

The Campaign Monitor API supports authentication using either OAuth or an API key.

### Using OAuth

Depending on the environment you are developing in, you may wish to use a PHP OAuth library to get access tokens for your users. If you don't use an OAuth library, you will need to get access tokens for your users by following the instructions included in the Campaign Monitor API [documentation](http://www.campaignmonitor.com/api/getting-started/#authenticating_with_oauth). This package provides functionality to help you do this, as described below. You may also wish to reference this [example application](https://gist.github.com/jdennes/4973318), which is implemented using [Slim](http://slimframework.com/) but could easily be adapted for use with any PHP framework.

The first thing your application should do is redirect your user to the Campaign Monitor authorization URL where they will have the opportunity to approve your application to access their Campaign Monitor account. You can get this authorization URL by using the `CS_REST_General::authorize_url()` method, like so:

```php
require_once 'csrest_general.php';

$authorize_url = CS_REST_General::authorize_url(
    'Client ID for your application',
    'Redirect URI for your application',
    'The permission level your application requires',
    'Optional state data to be included'
);
# Redirect your users to $authorize_url.
```

If your user approves your application, they will then be redirected to the `redirect_uri` you specified, which will include a `code` parameter, and optionally a `state` parameter in the query string. Your application should implement a handler which can exchange the code passed to it for an access token, using `CS_REST_General::exchange_token()` like so:

```php
require_once 'csrest_general.php';

$result = CS_REST_General::exchange_token(
    'Client ID for your application',
    'Client Secret for your application',
    'Redirect URI for your application',
    'A unique code for your user' # Get the code parameter from the query string
);

if ($result->was_successful()) {
    $access_token = $result->response->access_token;
    $expires_in = $result->response->expires_in;
    $refresh_token = $result->response->refresh_token;
    # Save $access_token, $expires_in, and $refresh_token.
} else {
    echo 'An error occurred:\n';
    echo $result->response->error.': '.$result->response->error_description."\n";
    # Handle error...
}
```

At this point you have an access token and refresh token for your user which you should store somewhere convenient so that your application can look up these values when your user wants to make future Campaign Monitor API calls.

Once you have an access token and refresh token for your user, you can authenticate and make further API calls like so:

```php
require_once 'csrest_general.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh_token');
$wrap = new CS_REST_General($auth);

$result = $wrap->get_clients();
var_dump($result->response);
```

All OAuth tokens have an expiry time, and can be renewed with a corresponding refresh token. If your access token expires when attempting to make an API call, you will receive an error response, so your code should handle this. Here's an example of how you could do this:

```php
require_once 'csrest_general.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token'
);
$wrap = new CS_REST_General($auth);
$result = $wrap->get_clients();
if (!$result->was_successful()) {
    # If you receive '121: Expired OAuth Token', refresh the access token
    if ($result->response->Code == 121) {
        list($new_access_token, $new_expires_in, $new_refresh_token) = 
            $wrap->refresh_token();
        # Save $new_access_token, $new_expires_in, and $new_refresh_token
    }
    # Make the call again
    $result = $wrap->get_clients();
}
var_dump($result->response);
```

### Using an API key

```php
require_once 'csrest_general.php';

$auth = array('api_key' => 'your API key');
$wrap = new CS_REST_General($auth);

$result = $wrap->get_clients();
var_dump($result->response);
```
## API Call Timeout
You can set your local API call timeout time in createsend-php\class\transport.php line 11, in the CS_REST_CALL_TIMEOUT variable. Currently the default is 120 secs.

## Examples

Samples for creating or accessing all resources can be found in the samples directory.
These samples can be used as the basis for your own application and provide an outline of 
the expected inputs for each API call. 

Further documentation of the inputs and outputs of each call can be found in the 
documentation in each of the csrest_*.php files or simply by examining the 
var_dump results in each of the provided samples.

## Contributing

Please check the [guidelines for contributing](https://github.com/campaignmonitor/createsend-php/blob/master/CONTRIBUTING.md) to this repository.

## Releasing

Please check the [instructions for releasing](https://github.com/campaignmonitor/createsend-php/blob/master/RELEASE.md) this library.
