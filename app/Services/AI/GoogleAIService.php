<?php

namespace App\Services\AI;

use GeminiAPI\Client;
use GeminiAPI\Enums\Role;
use GeminiAPI\GenerationConfig;
use GeminiAPI\Resources\Content;
use GeminiAPI\Resources\Parts\TextPart;
use Psr\Http\Client\ClientExceptionInterface;

class GoogleAIService implements LLMService
{

    private Client $client;
    public function __construct()
    {
        $apiKey = config('ai.google_api_key' );
        $this->client = new Client($apiKey);
    }

    public function findIssues(string $commit): array
    {
        $result = 'hasNeither';
        $generationConfig = (new GenerationConfig())
            ->withTemperature(0);
        try {
            $result = $this->client->geminiPro()
                ->withGenerationConfig($generationConfig)
                ->generateContent(
                    new TextPart(
                        <<<TEXT
                        You are cyber-security and coding expert.
                        If commit has both bugs and security issues, answer only hasBoth.
                        If commit has bugs, answer  only hasBugs.
                        If commit has security issues, answer only hasSecurityIssues.
                        If commit has neither, answer only hasNeither.
                        TEXT
                    ),
                    new TextPart($commit)
            )->text();
        } catch (\Exception $e) {
            echo ($e->getMessage());
        }

        return match ($result) {
            'hasBoth' => ['hasBugs' => true, 'hasSecurityIssues' => true],
            'hasBugs' => ['hasBugs' => true, 'hasSecurityIssues' => false],
            'hasSecurityIssues' => ['hasBugs' => false, 'hasSecurityIssues' => true],
            default => ['hasBugs' => false, 'hasSecurityIssues' => false],
        };
    }
}