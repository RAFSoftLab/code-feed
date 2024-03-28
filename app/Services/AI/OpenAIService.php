<?php

namespace App\Services\AI;

use Exception;
use OpenAI\Client;

class OpenAIService implements LLMService
{

    private Client $client;
    public function __construct()
    {
        $apiKey = config('services.ai.openai_key' );
        $this->client = \OpenAI::client($apiKey);
    }

    public function findIssues(string $commit): array
    {
        $systemMessage = <<<TEXT
                            You are cyber-security and coding expert.
                            If commit has both bugs and security issues, answer hasBoth.
                            If commit has bugs, answer  hasBugs.
                            If commit has security issues, answer hasSecurityIssues.
                            If commit has neither, answer hasNeither.
                            TEXT;
        try {
            $result = $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $commit],
                ],
                'temperature' => 0,
            ]);

            $result = $result->choices[0]->message->content;
            print ($result.PHP_EOL);
            return match ($result) {
                'hasBoth' => ['hasBugs' => true, 'hasSecurityIssues' => true],
                'hasBugs' => ['hasBugs' => true, 'hasSecurityIssues' => false],
                'hasSecurityIssues' => ['hasBugs' => false, 'hasSecurityIssues' => true],
                default => ['hasBugs' => false, 'hasSecurityIssues' => false],
            };
        } catch (Exception $e) {
            print ($e->getMessage());
        }

        return ['hasBugs' => false, 'hasSecurityIssues' => false];
    }

    public function explain(string $commit): string
    {
        $systemMessage = <<<TEXT
                            You are cyber-security and coding expert.
                            Try to find any bugs or security issues in the following commit and explain them.
                            TEXT;
        try {
            $result = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $commit],
                ],
                'temperature' => 0,
            ]);

            return $result->choices[0]->message->content;
        } catch (Exception $e) {
            print ($e->getMessage());
        }

        return 'No response from OpenAI';
    }

    public function summarize(string $commit): array
    {
        $summary = '';
        $systemMessage = <<<TEXT
                            You are an expert coder. Write a summary of what was done in the code commit provided.
                            It should also contain maximum 3 bullet points explaining what was done.
                            Do not include author name in the summary.
                            TEXT;
        try {
            $result = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $commit],
                ],
                'temperature' => 0,
            ]);

            $summary = $result->choices[0]->message->content;
            print($summary . PHP_EOL);
        } catch (Exception $e) {
            print ($e->getMessage());
        }

        return array($summary);
    }
}