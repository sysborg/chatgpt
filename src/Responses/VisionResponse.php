<?php

namespace Sysborg\ChatGPT\Responses;

class VisionResponse extends ChatResponse
{
    /**
     * Get the vision analysis content
     */
    public function getAnalysis(): string
    {
        return $this->getContent();
    }

    /**
     * Check if the response contains image analysis
     */
    public function hasImageAnalysis(): bool
    {
        return !empty($this->getContent());
    }

    /**
     * Get the confidence level if available
     */
    public function getConfidence(): ?float
    {
        // OpenAI doesn't currently provide confidence scores for vision
        // This method is here for future compatibility
        return null;
    }

    /**
     * Extract any detected objects/entities mentioned in the response
     */
    public function getDetectedEntities(): array
    {
        $content = $this->getContent();
        $entities = [];
        
        // Simple pattern matching for common entities
        // This is a basic implementation - you might want to use NLP libraries for better extraction
        $patterns = [
            'people' => '/\b(?:person|people|man|woman|child|individual|human)\b/i',
            'objects' => '/\b(?:car|vehicle|building|tree|animal|dog|cat|bird)\b/i',
            'colors' => '/\b(?:red|blue|green|yellow|black|white|brown|gray|grey|purple|orange|pink)\b/i',
            'emotions' => '/\b(?:happy|sad|angry|surprised|excited|calm|peaceful|worried|confused)\b/i',
        ];

        foreach ($patterns as $category => $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $entities[$category] = array_unique($matches[0]);
            }
        }

        return $entities;
    }

    /**
     * Check if the image analysis indicates any safety concerns
     */
    public function hasSafetyConcerns(): bool
    {
        $content = strtolower($this->getContent());
        $safetyKeywords = ['inappropriate', 'unsafe', 'dangerous', 'harmful', 'violence', 'adult content'];
        
        foreach ($safetyKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get a summary of what was detected in the image
     */
    public function getSummary(): string
    {
        $content = $this->getContent();
        
        // Extract the first sentence as a summary
        $sentences = preg_split('/[.!?]+/', $content, 2);
        return trim($sentences[0] ?? '') . '.';
    }

    /**
     * Convert to array with vision-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'analysis' => $this->getAnalysis(),
            'has_image_analysis' => $this->hasImageAnalysis(),
            'detected_entities' => $this->getDetectedEntities(),
            'has_safety_concerns' => $this->hasSafetyConcerns(),
            'summary' => $this->getSummary(),
            'confidence' => $this->getConfidence(),
        ]);
    }
}