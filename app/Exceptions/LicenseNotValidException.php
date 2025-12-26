<?php

namespace App\Exceptions;

use Exception;

class LicenseNotValidException extends Exception
{
    protected $message = 'License not valid';
    protected $code = 403;
}
