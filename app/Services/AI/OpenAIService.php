<?php

namespace App\Services\AI;

use OpenAI\Client;

class OpenAIService implements LLMService
{

    private Client $client;
    public function __construct()
    {
        $apiKey = config('ai.openai_key', );
        $this->client = \OpenAI::client($apiKey);
    }

    public function findIssues(string $commit): array
    {
        $systemMessage = <<<TEXT
                            You are cyber-security and coding expert.
                            If commit has both bugs and security issues, answer only hasBoth.
                            If commit has bugs, answer  only hasBugs.
                            If commit has security issues, answer only hasSecurityIssues.
                            TEXT;

        $result = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' =>  $commit],
            ],
            'temperature' => 0,
        ]);

        $result = $result->choices[0]->message->content;

        return match ($result) {
            'hasBoth' => ['hasBugs' => true, 'hasSecurityIssues' => true],
            'hasBugs' => ['hasBugs' => true, 'hasSecurityIssues' => false],
            'hasSecurityIssues' => ['hasBugs' => false, 'hasSecurityIssues' => true],
            default => ['hasBugs' => false, 'hasSecurityIssues' => false],
        };
    }

    public function explain(string $commit): string
    {
        // TODO: Implement explain() method.
        return '';
    }

    public function summarize(mixed $commit): array
    {
        // TODO: Implement summarize() method.
    }
}