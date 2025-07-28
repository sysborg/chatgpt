<?php

namespace Sysborg\ChatGPT\Responses;

class CompletionResponse extends BaseResponse
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
        return $this->choices[0]['text'] ?? '';
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
     * Get the log probabilities
     */
    public function getLogprobs(): ?array
    {
        return $this->choices[0]['logprobs'] ?? null;
    }

    /**
     * Check if the response was truncated due to length
     */
    public function isTruncated(): bool
    {
        return $this->finishReason === 'length';
    }

    /**
     * Check if the response completed successfully
     */
    public function isComplete(): bool
    {
        return $this->finishReason === 'stop';
    }

    /**
     * Get the completion text without leading/trailing whitespace
     */
    public function getCleanContent(): string
    {
        return trim($this->getContent());
    }

    /**
     * Convert to array with completion-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'choices' => $this->choices,
            'finish_reason' => $this->finishReason,
            'is_complete' => $this->isComplete(),
            'is_truncated' => $this->isTruncated(),
            'clean_content' => $this->getCleanContent(),
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