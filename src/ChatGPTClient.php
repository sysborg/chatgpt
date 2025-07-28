<?php

namespace Sysborg\ChatGPT;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Sysborg\ChatGPT\Exceptions\ChatGPTException;
use Sysborg\ChatGPT\Exceptions\RateLimitException;
use Sysborg\ChatGPT\Responses\ChatResponse;
use Sysborg\ChatGPT\Responses\CompletionResponse;
use Sysborg\ChatGPT\Responses\VisionResponse;
use Sysborg\ChatGPT\Traits\HasRateLimit;

class ChatGPTClient
{
    use HasRateLimit;

    protected Client $client;
    protected array $config;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->model = $config['default_model'];
        $this->temperature = $config['temperature'];
        $this->maxTokens = $config['max_tokens'];

        $this->client = new Client([
            'base_uri' => $config['base_url'] . '/',
            'timeout' => $config['timeout'],
            'headers' => [
                'Authorization' => 'Bearer ' . $config['api_key'],
                'Content-Type' => 'application/json',
                'User-Agent' => $config['user_agent'],
            ],
        ]);

        $this->initializeRateLimit($config['rate_limit']);
    }

    /**
     * Send a simple chat message
     */
    public function chat(string $message, array $options = []): ChatResponse
    {
        $messages = [
            ['role' => 'user', 'content' => $message]
        ];

        return $this->chatWithHistory($messages, $options);
    }

    /**
     * Send chat with message history
     */
    public function chatWithHistory(array $messages, array $options = []): ChatResponse
    {
        $this->checkRateLimit();

        $payload = array_merge([
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? $this->temperature,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'top_p' => $options['top_p'] ?? $this->config['top_p'],
            'frequency_penalty' => $options['frequency_penalty'] ?? $this->config['frequency_penalty'],
            'presence_penalty' => $options['presence_penalty'] ?? $this->config['presence_penalty'],
        ], $options);

        $response = $this->makeRequest('chat/completions', $payload);

        return new ChatResponse($response);
    }

    /**
     * Text completion (legacy)
     */
    public function completion(string $prompt, array $options = []): CompletionResponse
    {
        $this->checkRateLimit();

        $payload = array_merge([
            'model' => $options['model'] ?? $this->model,
            'prompt' => $prompt,
            'temperature' => $options['temperature'] ?? $this->temperature,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'top_p' => $options['top_p'] ?? $this->config['top_p'],
            'frequency_penalty' => $options['frequency_penalty'] ?? $this->config['frequency_penalty'],
            'presence_penalty' => $options['presence_penalty'] ?? $this->config['presence_penalty'],
        ], $options);

        $response = $this->makeRequest('completions', $payload);

        return new CompletionResponse($response);
    }

    /**
     * Vision analysis with image URL
     */
    public function vision(string $imageUrl, string $prompt, array $options = []): VisionResponse
    {
        $this->checkRateLimit();

        $messages = [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $prompt
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => $imageUrl,
                            'detail' => $options['detail'] ?? 'auto'
                        ]
                    ]
                ]
            ]
        ];

        $payload = array_merge([
            'model' => $options['model'] ?? 'gpt-4-vision-preview',
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? $this->temperature,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
        ], $options);

        $response = $this->makeRequest('chat/completions', $payload);

        return new VisionResponse($response);
    }

    /**
     * Vision analysis with base64 image
     */
    public function visionFromBase64(string $base64Image, string $prompt, array $options = []): VisionResponse
    {
        // Ensure proper base64 data URL format
        if (!str_starts_with($base64Image, 'data:image/')) {
            $base64Image = 'data:image/jpeg;base64,' . $base64Image;
        }

        return $this->vision($base64Image, $prompt, $options);
    }

    /**
     * Set the model
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set the temperature
     */
    public function setTemperature(float $temperature): self
    {
        $this->temperature = max(0.0, min(2.0, $temperature));
        return $this;
    }

    /**
     * Set max tokens
     */
    public function setMaxTokens(int $maxTokens): self
    {
        $this->maxTokens = max(1, $maxTokens);
        return $this;
    }

    /**
     * Get available models
     */
    public function getAvailableModels(): array
    {
        return [
            'gpt-3.5-turbo',
            'gpt-3.5-turbo-16k',
            'gpt-4',
            'gpt-4-turbo',
            'gpt-4-vision-preview',
            'gpt-4-32k',
        ];
    }

    /**
     * Make API request with retry logic
     */
    protected function makeRequest(string $endpoint, array $payload): array
    {
        $attempts = 0;
        $maxAttempts = $this->config['retry_attempts'] + 1;

        while ($attempts < $maxAttempts) {
            try {
                $response = $this->client->post($endpoint, [
                    'json' => $payload
                ]);

                return json_decode($response->getBody()->getContents(), true);

            } catch (RequestException $e) {
                $attempts++;

                if ($e->getResponse()) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);

                    // Rate limit exceeded
                    if ($statusCode === 429) {
                        if ($attempts < $maxAttempts) {
                            sleep($this->config['retry_delay']);
                            continue;
                        }
                        throw new RateLimitException('Rate limit exceeded. Please try again later.');
                    }

                    // Other client errors - don't retry
                    if ($statusCode >= 400 && $statusCode < 500) {
                        throw new ChatGPTException(
                            $errorBody['error']['message'] ?? 'Client error occurred',
                            $statusCode
                        );
                    }
                }

                // Server errors or network issues - retry
                if ($attempts < $maxAttempts) {
                    sleep($this->config['retry_delay']);
                    continue;
                }

                throw new ChatGPTException(
                    'Request failed after ' . $this->config['retry_attempts'] . ' retries: ' . $e->getMessage(),
                    $e->getCode()
                );
            }
        }

        throw new ChatGPTException('Unexpected error occurred');
    }
}