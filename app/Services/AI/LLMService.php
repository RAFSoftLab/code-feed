<?php

namespace App\Services\AI;

interface LLMService
{
    public function findIssues(string $commit): array;
    public function explain(string $commit): string;

    public function summarize(string $commit): array;
}