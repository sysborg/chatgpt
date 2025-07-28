# Sysborg ChatGPT Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sysborg/chatgpt?style=flat-square)](https://packagist.org/packages/sysborg/chatgpt)
[![Total Downloads](https://img.shields.io/packagist/dt/sysborg/chatgpt?style=flat-square)](https://packagist.org/packages/sysborg/chatgpt)
[![License: MIT](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![Laravel](https://img.shields.io/badge/laravel-10%2B-red?style=flat-square\&logo=laravel)
![PHP](https://img.shields.io/badge/php-%5E8.1-blue?style=flat-square\&logo=php)
![Composer](https://img.shields.io/badge/ready%20for-composer-8952a8?style=flat-square\&logo=composer)

A simple and effective integration between Laravel and ChatGPT. This package provides a clean, developer-friendly interface for interacting with the OpenAI API, including support for chat, text completions, and image analysis with GPT-4 Vision.

---

## Features

* ✅ **Chat Completions** – Conversational AI with memory
* ✅ **Text Completions** – Continue or generate texts
* ✅ **Vision Analysis** – Image understanding with GPT-4 Vision
* ✅ **Rate Limiting** – Configurable request throttling
* ✅ **Retry Logic** – Automatic retries on failures
* ✅ **Multiple Models** – GPT-3.5, GPT-4, and GPT-4 Vision supported
* ✅ **Laravel Integration** – Includes Service Provider and Facade
* ✅ **Auto Discovery** – Automatically registered in Laravel

---

## Installation

Install the package via Composer:

```bash
composer require sysborg/chatgpt
```

### Publish Configuration

To publish the config file:

```bash
php artisan vendor:publish --tag=chatgpt-config
```

### Set Your API Key

Add your OpenAI API key in the `.env` file:

```env
OPENAI_API_KEY=your_openai_api_key_here
```

---

## Configuration

Customize `config/chatgpt.php` to match your needs:

```php
return [
    'api_key' => env('OPENAI_API_KEY'),
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),
    'timeout' => env('OPENAI_TIMEOUT', 60),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
    'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    'retry_attempts' => env('OPENAI_RETRY_ATTEMPTS', 2),
    'retry_delay' => env('OPENAI_RETRY_DELAY', 1),
    'rate_limit' => env('OPENAI_RATE_LIMIT', 60), // 0 = unlimited
];
```

---

## Basic Usage

### Simple Chat

```php
use Sysborg\ChatGPT\Facades\ChatGPT;

$response = ChatGPT::chat('Hello, how are you?');
echo $response->getContent();
```

### Chat With History

```php
$messages = [
    ['role' => 'user', 'content' => 'What is the capital of Brazil?'],
    ['role' => 'assistant', 'content' => 'The capital of Brazil is Brasília.'],
    ['role' => 'user', 'content' => 'What is the population?']
];

$response = ChatGPT::chatWithHistory($messages);
echo $response->getContent();
```

### Text Completion

```php
$response = ChatGPT::completion('Once upon a time in a distant kingdom');
echo $response->getContent();
```

### Image Analysis

```php
$response = ChatGPT::vision(
    'https://example.com/image.jpg',
    'Describe this image in detail'
);

// With base64 image
$base64 = base64_encode(file_get_contents('/path/to/image.jpg'));
$response = ChatGPT::visionFromBase64(
    $base64,
    'What do you see in this image?'
);

echo $response->getAnalysis();
```

---

## Dynamic Configuration

```php
$response = ChatGPT::setModel('gpt-4')
    ->setTemperature(0.9)
    ->setMaxTokens(2000)
    ->chat('Tell a creative story');
```

---

## Handling Responses

### Chat Response

```php
$response = ChatGPT::chat('Hi!');

echo $response->getContent();
echo $response->getModel();
echo $response->getTotalTokens();
echo $response->getFinishReason();

if ($response->isComplete()) {
    echo "Completed successfully";
}

if ($response->isTruncated()) {
    echo "Response was truncated";
}
```

### Vision Response

```php
$response = ChatGPT::vision($imageUrl, 'Analyze this image');

echo $response->getAnalysis();
print_r($response->getDetectedEntities());
echo $response->getSummary();

if ($response->hasSafetyConcerns()) {
    echo "This image may contain sensitive content";
}
```

---

## Error Handling

```php
use Sysborg\ChatGPT\Exceptions\ChatGPTException;
use Sysborg\ChatGPT\Exceptions\RateLimitException;

try {
    $response = ChatGPT::chat('Hi!');
} catch (RateLimitException $e) {
    echo "Rate limit exceeded. Retry in: " . $e->getRetryAfter() . " seconds";
} catch (ChatGPTException $e) {
    echo "API error: " . $e->getMessage();
    print_r($e->getContext());
}
```

---

## Rate Limiting

Built-in rate limit handling:

```php
$status = ChatGPT::getRateLimitStatus();
echo "Requests remaining: " . $status['remaining'];

ChatGPT::resetRateLimit(); // Useful for testing
```

---

## Available Models

```php
$models = ChatGPT::getAvailableModels();
print_r($models);
```

**Example Output:**

```php
[
    'gpt-3.5-turbo',
    'gpt-3.5-turbo-16k',
    'gpt-4',
    'gpt-4-turbo',
    'gpt-4-vision-preview',
    'gpt-4-32k'
]
```

---

## Advanced Examples

### Chat with Custom Config

```php
$response = ChatGPT::chatWithHistory([
    ['role' => 'system', 'content' => 'You are a programming assistant.'],
    ['role' => 'user', 'content' => 'How do I create a REST API in Laravel?']
], [
    'model' => 'gpt-4',
    'temperature' => 0.3,
    'max_tokens' => 1500,
    'top_p' => 0.9
]);
```

### Vision Analysis with Settings

```php
$response = ChatGPT::vision($imageUrl, 'Analyze this image', [
    'model' => 'gpt-4-vision-preview',
    'detail' => 'high', // Options: low, high, auto
    'max_tokens' => 1000
]);
```

---

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

---

## License

This package is licensed under the [MIT License](LICENSE.md).

---

## Author

**Anderson Arruda**
📧 [andmarruda@gmail.com](mailto:andmarruda@gmail.com)
🐙 [@andmarruda on GitHub](https://github.com/andmarruda)

---

## Support

If you encounter any issues or have questions, please open a [GitHub Issue](https://github.com/sysborg/chatgpt/issues).