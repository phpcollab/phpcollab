<?php

namespace phpCollab;

/**
 * Class RequestData
 *
 * Request data object that replaces $GLOBALS['initrequest'] usage.
 * This class holds HTTP request parameters and can be injected into
 * Gateway classes using dependency injection.
 *
 * @package phpCollab
 */
class RequestData
{
    private array $data;

    /**
     * RequestData constructor.
     *
     * @param array $data Request data array
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Create RequestData from current $GLOBALS['initrequest']
     *
     * @return self
     */
    public static function fromGlobals(): self
    {
        return new self($GLOBALS['initrequest'] ?? []);
    }

    /**
     * Get a value from request data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if a key exists in request data
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get all request data
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Set a value in request data
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }
}
