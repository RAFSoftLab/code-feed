<?php

namespace App\Services\AI;

use Blinq\LLM\Client;
use Blinq\LLM\Config\ApiConfig;

class OpenAIService implements LLMService
{

    private Client $client;
    public function __construct()
    {
        $apiKey = config('ai.openai_key', );
        $config = new ApiConfig('openai', $apiKey);
        $this->client = new Client($config);

    }

    public function findIssues(string $commit): array
    {
        $prompt = "Assume role of a security expert check for security issues in the following commit message:\n $commit";
        $prompt .= "\nAnswer only using  true or false";

        $response = $this->client->chat($prompt)>getLastMessage()->content;
        print ($response."\n");
        return array('security' => boolval($response));
    }
}