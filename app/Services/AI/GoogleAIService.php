<?php

namespace App\Services\AI;

use Exception;
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

    public function explain(string $commit): string
    {
        $result = 'No response from Google';

        $generationConfig = (new GenerationConfig())
            ->withTemperature(0);
        try {
            $result = $this->client->geminiPro()
                ->withGenerationConfig($generationConfig)
                ->generateContent(
                    new TextPart(
                        <<<TEXT
                        You are cyber-security and coding expert.
                        Try to find any bugs or security issues in the following commit and explain them.
                        TEXT
                    ),
                    new TextPart($commit)
                )->text();
        } catch (Exception $e) {
            echo ($e->getMessage());
        }

        return $result;
    }

    public function summarize(mixed $commit): array
    {
        $result = '';

        $generationConfig = (new GenerationConfig())
            ->withTemperature(0);
        try {
            $result = $this->client->geminiPro()
                ->withGenerationConfig($generationConfig)
                ->generateContent(
                    new TextPart(
                        <<<TEXT
                        You are an expert coder. Write a summary of what was done in the code commit provided.
                        It should also contain maximum 3 bullet points explaining what was done.
                        Do not include author name in the summary.
                        TEXT
                    ),
                    new TextPart($commit)
                )->text();
        } catch (Exception $e) {
            echo ($e->getMessage());
        }

        print ($result.PHP_EOL);

        return array($result);
    }
}