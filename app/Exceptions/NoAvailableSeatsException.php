<?php

namespace App\Exceptions;

use Exception;

class NoAvailableSeatsException extends Exception
{
    protected $message = 'No available seats';
    protected $code = 403;
}
