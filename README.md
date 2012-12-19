# createsend [![Build Status](https://secure.travis-ci.org/campaignmonitor/createsend-php.png)][travis]
A php library which implements the complete functionality of the Campaign Monitor API.

[travis]: http://travis-ci.org/campaignmonitor/createsend-php

## Installation

After downloading the wrapper simply include the relevant api class e.g
 
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
3. Ensure that the build passes, by running `cd tests && php all_tests.php` (CI runs on: `5.3` and `5.4`)
4. It should go without saying, but do not increment the version number in your commits.
5. Submit a pull request.