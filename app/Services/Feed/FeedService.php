<?php
namespace App\Services\Feed;

use App\Models\Commit;
use Illuminate\Support\Collection;

class FeedService
{
    // Define weights for each factor
    const WEIGHT_BOTH_FLAGS = 3;
    const WEIGHT_ONE_FLAG = 2;
    const WEIGHT_LENGTH = 1;
    const TIME_DECAY_FACTOR = 0.9; // Controls time decay

    public function getFeed()
    {
        $commits = Commit::with('posts')
            ->with('posts.commit')
            ->get();
        $posts = $commits->pluck('posts')->flatten();

        return $this->rankPosts($posts);
    }

    private function rankPosts($posts): Collection
    {
        // Define ranking function for each commit
        $rankFunction = function ($post) {
            $score = 0;

            // Check for both flags true
            if ($post->commit->hasBugs && $post->commit->hasSecurityIssues) {
                $score += $this::WEIGHT_BOTH_FLAGS;
            } else if ($post->commit->hasBugs || $post->commit->hasSecurityIssues) {
                $score += $this::WEIGHT_ONE_FLAG;
            }

            // Add score based on length
            $score += $this::WEIGHT_LENGTH * $post->commit->lineCount;

            // Apply time decay based on creation time (use DateTime if available)
            $currentTime = time();
            $timeDelta = $currentTime - strtotime($post->commit->created_at);
            $score *= pow($this::TIME_DECAY_FACTOR, $timeDelta);

            return $score;
        };

        // Sort commits by decreasing rank score
        return $posts->sortByDesc($rankFunction);
    }
}