# createsend [![Build Status](https://secure.travis-ci.org/campaignmonitor/createsend-php.png)][travis]
A php library which implements the complete functionality of the Campaign Monitor API.

[travis]: http://travis-ci.org/campaignmonitor/createsend-php

## Installation

If you use [Composer](http://getcomposer.org/), you can add [campaignmonitor/createsend-php](https://packagist.org/packages/campaignmonitor/createsend-php) to your `composer.json` file:

    {
        "require": {
            "campaignmonitor/createsend-php": "{version}"
        }
    }

Otherwise you can simply [download](https://github.com/campaignmonitor/createsend-php/tags) the library and include it in your project.

After you have installed the library, simply include the relevant API class e.g

    require_once 'csrest_campaigns.php'

## Examples

Samples for creating or accessing all resources can be found in the samples directory.
These samples can be used as the basis for your own application and provide an outline of 
the expected inputs for each API call. 

Further documentation of the inputs and outputs of each call can be found in the 
documentation in each of the csrest_*.php files or simply by examining the 
var_dump results in each of the provided samples.

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
