<?php

namespace phpCollab\Security;

use Exception;

/**
 * Class UnauthorizedException
 *
 * Thrown when a user attempts to access a resource they don't have permission to access
 *
 * This exception should be caught and handled gracefully by displaying an error message
 * and logging the unauthorized access attempt.
 *
 * @package phpCollab\Security
 */
class UnauthorizedException extends Exception
{
    /**
     * HTTP status code for unauthorized access
     */
    const HTTP_STATUS_CODE = 403;

    /**
     * UnauthorizedException constructor.
     *
     * @param string $message Error message
     * @param int $code Error code (defaults to 403 Forbidden)
     * @param Exception|null $previous Previous exception for chaining
     */
    public function __construct(
        string $message = "You are not authorized to access this resource",
        int $code = self::HTTP_STATUS_CODE,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get HTTP status code for this exception
     *
     * @return int HTTP status code (403)
     */
    public function getHttpStatusCode(): int
    {
        return self::HTTP_STATUS_CODE;
    }
}
