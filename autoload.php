<?php
/**
 * You only need this file if you are not using composer.
 * Why are you not using composer?
 * https://getcomposer.org/
 */

if (version_compare(PHP_VERSION, '7.1.0', '<')) {
    throw new Exception('The SDK requires PHP version 7.1.0 or higher.');
}
