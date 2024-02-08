<?php
namespace App\Services\Feed;

use App\Models\Post;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FeedService
{
    // Define weights for each factor
    const WEIGHT_BOTH_FLAGS = 3;
    const WEIGHT_ONE_FLAG = 2;
    const WEIGHT_LENGTH = 1;
    const MAX_TIME_DECAY_FACTOR = 0.99; // Maximum time decay factor
    const MAX_HOUR_DIFFERENCE = 24 * 365; // Maximum time difference in hours (1 year)

    public function getFeed(): Collection
    {
        $posts = Post::with('commit')->get();

        return $this->rankPosts($posts);
    }

    private function rankPosts(Collection $posts): Collection
    {
        // Define ranking function for each commit
        $rankFunction = function ($post) {
            $score = 0;

            // Check for both flags true
            if ($post->commit->hasBugs && $post->commit->hasSecurityIssues) {
                $score += self::WEIGHT_BOTH_FLAGS;
            } else if ($post->commit->hasBugs || $post->commit->hasSecurityIssues) {
                $score += self::WEIGHT_ONE_FLAG;
            }

            // Add score based on length
            $score += self::WEIGHT_LENGTH * $post->commit->lineCount;

            // Apply time decay based on Carbon dates
            $now = Carbon::now();
            $postCreatedAt = Carbon::parse($post->commit->created_at);
            $hourDelta = $now->diffInHours($postCreatedAt);

            // Limit hour difference and calculate decay factor
            $limitedHourDelta = min($hourDelta, self::MAX_HOUR_DIFFERENCE);
            $decayFactor = pow(self::MAX_TIME_DECAY_FACTOR, $limitedHourDelta);

            $score *= $decayFactor;
            // Ensure a minimum score of 0.1

            return max($score, 0.1);
        };

        // Sort commits by decreasing rank score
        return $posts->sortByDesc($rankFunction);
    }
}
