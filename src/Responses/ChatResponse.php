<?php

namespace Sysborg\ChatGPT\Responses;

class ChatResponse extends BaseResponse
{
    protected array $choices;
    protected string $finishReason;

    public function __construct(array $response)
    {
        parent::__construct($response);
        $this->choices = $response['choices'] ?? [];
        $this->finishReason = $this->choices[0]['finish_reason'] ?? '';
    }

    /**
     * Get the main response content
     */
    public function getContent(): string
    {
        return $this->choices[0]['message']['content'] ?? '';
    }

    /**
     * Get all choices
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * Get the finish reason
     */
    public function getFinishReason(): string
    {
        return $this->finishReason;
    }

    /**
     * Get the message role
     */
    public function getRole(): string
    {
        return $this->choices[0]['message']['role'] ?? '';
    }

    /**
     * Check if the response was truncated due to length
     */
    public function isTruncated(): bool
    {
        return $this->finishReason === 'length';
    }

    /**
     * Check if the response was stopped due to content filter
     */
    public function isFiltered(): bool
    {
        return $this->finishReason === 'content_filter';
    }

    /**
     * Check if the response completed successfully
     */
    public function isComplete(): bool
    {
        return $this->finishReason === 'stop';
    }

    /**
     * Get the full message object
     */
    public function getMessage(): array
    {
        return $this->choices[0]['message'] ?? [];
    }

    /**
     * Convert to array with chat-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'choices' => $this->choices,
            'finish_reason' => $this->finishReason,
            'role' => $this->getRole(),
            'is_complete' => $this->isComplete(),
            'is_truncated' => $this->isTruncated(),
            'is_filtered' => $this->isFiltered(),
        ]);
    }

    /**
     * Magic method to convert to string
     */
    public function __toString(): string
    {
        return $this->getContent();
    }
}