<?php

namespace Sysborg\ChatGPT\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sysborg\ChatGPT\Responses\ChatResponse chat(string $message, array $options = [])
 * @method static \Sysborg\ChatGPT\Responses\ChatResponse chatWithHistory(array $messages, array $options = [])
 * @method static \Sysborg\ChatGPT\Responses\CompletionResponse completion(string $prompt, array $options = [])
 * @method static \Sysborg\ChatGPT\Responses\VisionResponse vision(string $imageUrl, string $prompt, array $options = [])
 * @method static \Sysborg\ChatGPT\Responses\VisionResponse visionFromBase64(string $base64Image, string $prompt, array $options = [])
 * @method static \Sysborg\ChatGPT\ChatGPTClient setModel(string $model)
 * @method static \Sysborg\ChatGPT\ChatGPTClient setTemperature(float $temperature)
 * @method static \Sysborg\ChatGPT\ChatGPTClient setMaxTokens(int $maxTokens)
 * @method static array getAvailableModels()
 *
 * @see \Sysborg\ChatGPT\ChatGPTClient
 */
class ChatGPT extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'chatgpt';
    }
}