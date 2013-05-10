# Releasing createsend-php

## Requirements

- You must have [Composer](https://getcomposer.org/) installed.

## Prepare the release

- Increment the `CS_REST_WRAPPER_VERSION` constant in the `class/base_classes.php` file, ensuring that you use [Semantic Versioning](http://semver.org/).
- Add an entry to `HISTORY.md` which clearly explains the new release.
- Install development dependencies and ensure that tests pass locally:

  ```
  composer install --dev
  cd tests && php all_tests.php && cd ..
  ```

- Commit your changes:

  ```
  git commit -am "Version X.Y.Z"
  ```

- Tag the new version:

  ```
  git tag -a vX.Y.Z -m "Version X.Y.Z"
  ```

- Push your changes to GitHub, including the tag you just created:

  ```
  git push origin master --tags
  ```

- Ensure that all [tests](https://travis-ci.org/campaignmonitor/createsend-php) pass.

## Release the module

There is a [GitHub service hook](https://github.com/campaignmonitor/createsend-php/settings/hooks) for the [Packagist](https://packagist.org/) PHP package repository, which is configured to update the [createsend-php](https://packagist.org/packages/campaignmonitor/createsend-php) package when new tags are pushed to GitHub.

So there are no further steps to releasing the module. You should now see the latest version of the module listed on [Packagist](https://packagist.org/packages/campaignmonitor/createsend-php). All done!
