# createsend [![Build Status](https://secure.travis-ci.org/campaignmonitor/createsend-php.png)][travis]
A PHP library which implements the complete functionality of the [Campaign Monitor API](http://www.campaignmonitor.com/api/).

[travis]: http://travis-ci.org/campaignmonitor/createsend-php

## Installation

If you use [Composer](http://getcomposer.org/), you can add [campaignmonitor/createsend-php](https://packagist.org/packages/campaignmonitor/createsend-php) to your `composer.json` file:

```json
{
    "require": {
        "campaignmonitor/createsend-php": "{version}"
    }
}
```

Otherwise you can simply [download](https://github.com/campaignmonitor/createsend-php/tags) the library and include it in your project.

After you have installed the library, simply include the relevant API class, as follows:

```php
require_once 'csrest_campaigns.php'
```

## Authentication

The Campaign Monitor API supports authentication using either OAuth or an API key.

### Using OAuth

TODO: Add instructions for getting authorize url

TODO: Add instructions for exchanging code for access token and refresh token

...

Once you have an access token and refresh token for your user, you can authenticate and make further API calls like so:

```php
require_once '../csrest_general.php';

$auth = array(
  'access_token' => 'your access token',
  'refresh_token' => 'your refresh_token');
$wrap = new CS_REST_General($auth);

$result = $wrap->get_clients();
var_dump($result->response);
```

TODO: Add instructions for refreshing access tokens

### Using an API key

```php
require_once '../csrest_general.php';

$auth = array('api_key' => 'your API key');
$wrap = new CS_REST_General($auth);

$result = $wrap->get_clients();
var_dump($result->response);
```

## Examples

Samples for creating or accessing all resources can be found in the samples directory.
These samples can be used as the basis for your own application and provide an outline of 
the expected inputs for each API call. 

Further documentation of the inputs and outputs of each call can be found in the 
documentation in each of the csrest_*.php files or simply by examining the 
var_dump results in each of the provided samples.

TODO: Add samples for authenticating using both OAuth and an API key.
TODO: Write sample applications to demonstrate both these approaches.

## Contributing
1. Fork the repository
2. Make your changes, including tests for your changes.
3. Ensure that the build passes, by running:
    
    ```
    composer install --dev
    cd tests && php all_tests.php && cd ..
    ```

    CI runs on: `5.3` and `5.4`.

4. It should go without saying, but do not increment the version number in your commits.
5. Submit a pull request.
