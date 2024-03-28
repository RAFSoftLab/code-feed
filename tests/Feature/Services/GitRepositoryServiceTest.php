<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Services\Git\GitRepositoryService;
use Tests\TestCase;

class GitRepositoryServiceTest extends TestCase
{
    public function test_clone_repository()
    {
        $user = User::factory()->create();
        $gitRepositoryService = new GitRepositoryService('https://github.com/RAFSoftLab/code-Feed-test-repo.git', $user);
        $commits = $gitRepositoryService->getCommits();

        self::assertCount(6, $commits);

        $commitChange = $gitRepositoryService->getCommitChanges($commits[5]->getHash());
        $expectedCommit =
'commit 381d95e8e9b61bdba076e3ee2d89807419224a51
Author: Slavko Fodor <slavko.fodor@gmail.com>
Date:   Sun Jan 28 06:55:41 2024 +0100

    Initial commit

diff --git a/LICENSE b/LICENSE
new file mode 100644
index 0000000..365d793
--- /dev/null
+++ b/LICENSE
@@ -0,0 +1,21 @@
+MIT License
+
+Copyright (c) 2024 RAFSoftLab, Belgrade
+
+Permission is hereby granted, free of charge, to any person obtaining a copy
+of this software and associated documentation files (the "Software"), to deal
+in the Software without restriction, including without limitation the rights
+to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
+copies of the Software, and to permit persons to whom the Software is
+furnished to do so, subject to the following conditions:
+
+The above copyright notice and this permission notice shall be included in all
+copies or substantial portions of the Software.
+
+THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
+IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
+FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
+AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
+LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
+OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
+SOFTWARE.
diff --git a/README.md b/README.md
new file mode 100644
index 0000000..40428a3
--- /dev/null
+++ b/README.md
@@ -0,0 +1,2 @@
+# code-feed-test-repo
+Repository used to test code-feed
';
        // Because of WSL development.
        $expectedCommit = str_replace("\r\n", "\n", $expectedCommit);

        self::assertEquals($expectedCommit, $commitChange);

        $newCommits =$gitRepositoryService->getNewCommits();

        self::assertCount(0, $newCommits);

        $gitRepositoryService->cleanUp();
    }

}