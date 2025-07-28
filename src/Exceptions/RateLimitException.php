<?php

namespace Sysborg\ChatGPT\Exceptions;

class RateLimitException extends ChatGPTException
{
    protected int $retryAfter = 60;

    public function __construct(string $message = '', int $retryAfter = 60, array $context = [])
    {
        parent::__construct($message, 429, null, $context);
        $this->retryAfter = $retryAfter;
    }

    /**
     * Get the number of seconds to wait before retrying
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    /**
     * Set the retry after time
     */
    public function setRetryAfter(int $seconds): self
    {
        $this->retryAfter = $seconds;
        return $this;
    }

    /**
     * Convert to array with retry information
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'retry_after' => $this->retryAfter,
        ]);
    }
}