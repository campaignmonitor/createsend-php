# Guidelines for contributing

1. [Fork the repository](https://help.github.com/articles/fork-a-repo).
2. [Create a topic branch](http://learn.github.com/p/branching.html).
3. Make your changes, including tests for your changes.
4. Ensure that all tests pass, by running:

    ```
    composer install --dev
    cd tests && php all_tests.php && cd ..
    ```

    The [Travis CI build](https://travis-ci.org/campaignmonitor/createsend-php) runs on PHP `5.3` and `5.4`.

5. It should go without saying, but do not increment the version number in your commits.
6. [Submit a pull request](https://help.github.com/articles/using-pull-requests).
