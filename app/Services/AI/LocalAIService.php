<?php

namespace App\Services\AI;

use App\Services\AI\LLMService;
use Illuminate\Support\Facades\Http;

class LocalAIService implements LLMService
{

    public function findIssues(string $commit): array
    {
        $result = 'hasNeither';
        $systemText =
            <<<TEXT
            You are cyber-security and coding expert.
            If commit has both bugs and security issues, answer only hasBoth.
            If commit has bugs, answer  only hasBugs.
            If commit has security issues, answer only hasSecurityIssues.
            If commit has neither, answer only hasNeither.
            Do not provide any other words in answer except hasBoth, hasBugs, hasSecurityIssues and hasNeither.
            TEXT;
        $result = $this->removePunctuation($this->queryModel($systemText.$commit));

        print ($result."\n");

        return match ($result) {
            'hasBoth' => ['hasBugs' => true, 'hasSecurityIssues' => true],
            'hasBugs' => ['hasBugs' => true, 'hasSecurityIssues' => false],
            'hasSecurityIssues' => ['hasBugs' => false, 'hasSecurityIssues' => true],
            default => ['hasBugs' => false, 'hasSecurityIssues' => false],
        };
    }

    public function explain(string $commit): string
    {
        $systemText =
            <<<TEXT
            You are cyber-security and coding expert.
            Try to find any bugs or security issues in the following commit and explain them.
            TEXT;

        $result = $this->removePunctuation($this->queryModel($systemText.$commit));

        print ($result."\n");

        return $result;
    }

    public function summarize(mixed $commit): array
    {
        $result = '';
        $systemText =
            <<<TEXT
            You are an expert coder. Write a summary of what was done in the code commit provided.
            It should also contain maximum 3 bullet points explaining what was done.
            Do not include author name in the summary.
            TEXT;
        $result = $this->removePunctuation($this->queryModel($systemText.$commit));

        print ($result.PHP_EOL);

        return array($result);
    }

    public function queryModel($question) {
        $response = Http::post(config('services.ai.local_ai_url').'api/generate', [
            'model' => config('services.ai.local_ai_model'),
            'prompt' => $question,
            'stream' => false,
            'temperature' => 0,
        ]);

        if ($response->successful()) {
            return $response->json()['response'];
        } else {
            throw new \Exception('Failed to send POST request: ' . $response->body());
        }
    }

    function removePunctuation($string): string
    {
        return trim(preg_replace("/[[:punct:]]+/", "", $string));
    }
}