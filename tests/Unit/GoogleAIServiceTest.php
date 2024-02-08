<?php

namespace Tests\Unit;

use App\Services\AI\GoogleAIService;
use Tests\TestCase;

class GoogleAIServiceTest extends TestCase
{
// findIssues method returns correct array based on input commit
    public function test_find_issues_no_issues()
    {
        $googleAIService = resolve(GoogleAIService::class);
        $commit =
            <<<TEXT
                commit 381d95e8e9b61bdba076e3ee2d89807419224a51
                Author: Slavko Fodor <slavko.fodor@gmail.com>
                Date:   Sun Jan 28 06:55:41 2024 +0100

                Initial commit
                diff --git a/LICENSE b/LICENSE
                new file mode 100644
                index 0000000..365d793
                 THE
                +SOFTWARE.
                diff --git a/README.md b/README.md
                new file mode 100644
                index 0000000..40428a3
                --- /dev/null
                +++ b/README.md
                @@ -0,0 +1,2 @@
                +# code-Feed-test-repo
                +Repository used to test code-Feed
            TEXT;

        $result = $googleAIService->findIssues($commit);

        $this->assertEquals(['hasBugs' => false, 'hasSecurityIssues' => false], $result);
    }

    public function test_find_issues_found_issues()
    {
        $googleAIService = resolve(GoogleAIService::class);
        $commit =
            <<<TEXT
                commit 2086ceb525df08210b926e5d228a87e99d2d1e8f
                Author: Slavko Fodor <slavko.fodor@gmail.com>
                Date:   Sun Jan 28 07:10:43 2024 +0100
                
                    Simple js to test the LLM
                
                diff --git a/auth/server.js b/auth/server.js
                new file mode 100644
                index 0000000..19b07b4
                --- /dev/null
                +++ b/auth/server.js
                @@ -0,0 +1,12 @@
                +const express = require('express');
                +const app = express();
                +const users = require('./usersDatabase');
                +
                +var userInput = prompt("Please enter your password:");
                +document.write("Your password is: " + userInput);
                +
                +var string = null
                +document.write(string.length);
                +
                +var userInput = "<script>alert('test');</script>";
                +document.getElementById("output").innerHTML = userInput;       
            TEXT;

        $result = $googleAIService->findIssues($commit);

        $this->assertEquals(['hasBugs' => true, 'hasSecurityIssues' => true], $result);
    }

    public function test_find_issues_found_bugs_only()
    {
        $googleAIService = resolve(GoogleAIService::class);
        $commit =
            <<<TEXT
                diff --git a/auth/test.php b/auth/test.php
                new file mode 100644
                index 0000000..90bf29b
                --- /dev/null
                +++ b/auth/test.php
                @@ -0,0 +1,3 @@
                +<?php
                +object = null;
                +object->get();      
            TEXT;

        $result = $googleAIService->findIssues($commit);

        $this->assertEquals(['hasBugs' => true, 'hasSecurityIssues' => false], $result);
    }

}