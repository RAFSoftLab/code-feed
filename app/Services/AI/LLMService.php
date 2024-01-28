<?php

namespace App\Services\AI;

interface LLMService
{
    public function findIssues(string $commit): array;
}