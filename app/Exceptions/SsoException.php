<?php

namespace App\Exceptions;

use RuntimeException;

/** Thrown when the university Identity Provider returns something we cannot trust or use. */
class SsoException extends RuntimeException
{
}
