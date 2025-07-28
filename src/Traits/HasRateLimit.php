<?php

namespace Sysborg\ChatGPT\Traits;

use Sysborg\ChatGPT\Exceptions\RateLimitException;

trait HasRateLimit
{
    protected int $rateLimit = 0;
    protected array $requestTimes = [];

    /**
     * Initialize rate limiting
     */
    protected function initializeRateLimit(int $limit): void
    {
        $this->rateLimit = $limit;
    }

    /**
     * Check if request is within rate limit
     */
    protected function checkRateLimit(): void
    {
        if ($this->rateLimit === 0) {
            return; // No rate limit
        }

        $now = time();
        $oneMinuteAgo = $now - 60;

        // Remove requests older than 1 minute
        $this->requestTimes = array_filter(
            $this->requestTimes,
            fn($time) => $time > $oneMinuteAgo
        );

        // Check if we're at the limit
        if (count($this->requestTimes) >= $this->rateLimit) {
            throw new RateLimitException(
                "Rate limit of {$this->rateLimit} requests per minute exceeded"
            );
        }

        // Add current request time
        $this->requestTimes[] = $now;
    }

    /**
     * Reset rate limit counter
     */
    public function resetRateLimit(): void
    {
        $this->requestTimes = [];
    }

    /**
     * Get current rate limit status
     */
    public function getRateLimitStatus(): array
    {
        $now = time();
        $oneMinuteAgo = $now - 60;

        $recentRequests = array_filter(
            $this->requestTimes,
            fn($time) => $time > $oneMinuteAgo
        );

        return [
            'limit' => $this->rateLimit,
            'remaining' => max(0, $this->rateLimit - count($recentRequests)),
            'reset_at' => count($recentRequests) > 0 ? min($recentRequests) + 60 : $now,
        ];
    }
}