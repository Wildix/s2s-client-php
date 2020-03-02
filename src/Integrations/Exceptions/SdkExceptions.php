<?php

namespace Wildix\Integrations\Exceptions;

/**
 * Class SdkExceptions
 *
 * @package Wildix\Integrations
 */
class SdkExceptions extends \Exception
{
    const ERROR_INVALID_PARAM = 'Required "%s" key not supplied in config and could not find default environment variable';
}
