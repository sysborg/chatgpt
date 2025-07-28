<?php

namespace Sysborg\ChatGPT\Responses;

abstract class BaseResponse
{
    protected array $rawResponse;
    protected string $id;
    protected string $object;
    protected int $created;
    protected string $model;
    protected array $usage;

    public function __construct(array $response)
    {
        $this->rawResponse = $response;
        $this->id = $response['id'] ?? '';
        $this->object = $response['object'] ?? '';
        $this->created = $response['created'] ?? 0;
        $this->model = $response['model'] ?? '';
        $this->usage = $response['usage'] ?? [];
    }

    /**
     * Get the response ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the object type
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * Get the creation timestamp
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * Get the model used
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get usage information
     */
    public function getUsage(): array
    {
        return $this->usage;
    }

    /**
     * Get prompt tokens used
     */
    public function getPromptTokens(): int
    {
        return $this->usage['prompt_tokens'] ?? 0;
    }

    /**
     * Get completion tokens used
     */
    public function getCompletionTokens(): int
    {
        return $this->usage['completion_tokens'] ?? 0;
    }

    /**
     * Get total tokens used
     */
    public function getTotalTokens(): int
    {
        return $this->usage['total_tokens'] ?? 0;
    }

    /**
     * Get the raw response array
     */
    public function getRaw(): array
    {
        return $this->rawResponse;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'model' => $this->model,
            'usage' => $this->usage,
            'content' => $this->getContent(),
        ];
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * Abstract method to get content
     */
    abstract public function getContent(): string;
}