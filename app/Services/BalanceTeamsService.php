<?php

namespace App\Services;

use App\Models\User;
use Faker;
class BalanceTeamsService {

    /**
     * Keep track of already-assigned players in the entire object scope.
     *
     * @var array
     */
    private $assigned;

    public function __construct() {
        $this->assigned = [];
    }

    /**
     * Generate a fake team name.
     *
     * @return string
     */
    private function fakeTeamName() : string {
        $faker = Faker\Factory::create();

        return 'The ' . $faker->city() . ' ' . $faker->jobTitle() . 's';
    }

    /**
     * Build out the scaffold for the team player slots.
     * Teams must have between 18 and 22 players and be even in number.
     *
     * @return array
     */
    private function buildTeamStructure() : array {
        $numPlayers = User::players()->count();

        // Get minimum number of teams.
        // We're assuming the dataset will always fit the criteria of >= 18 or <=22;
        // therefore, once distributed there should be no player set that does not
        // meet minimum team staffing requirements.
        $teamMinimum = (int) floor($numPlayers / 18);

        // If there are too many players for even team numbers, add another team.
        if ($teamMinimum % 2 !== 0) {
            $teamMinimum++;
        }

        $teams = [];
        // Distribute teams as evenly as possible.
        for ($i = 0; $i < $teamMinimum; $i++ ) {
            $teams[$this->fakeTeamName()] = [];
        }

        return $teams;
    }

    /**
     * Seed the teams with one goalie each.
     *
     * @return array
     */
    private function seedTeams() : array {
        $teams = $this->buildTeamStructure();

        // Seed initial teams with a goalie.
        foreach ($teams as $team => $players) {
            $player = User::players()->where('can_play_goalie', '=', 1)->whereNotIn('id', $this->assigned)->inRandomOrder()->first();
            $teams[$team][] = [
                'name'    => $player->full_name,
                'ranking' => $player->ranking,
                'goalie'  => $player->is_goalie,
            ];

            // Add the player's id to the list of already-assigned players
            $this->assigned[] = $player->id;
        }

        return $teams;
    }

    /**
     * Perform the heuristic algorithm to assign each player to a team.
     *
     * @return array
     */
    public function balanceTeams() : array {
        // Get our freshly seeded teams;
        $teams = $this->seedTeams();

        // Get the global player average.
        $globalAverage = User::players()->average('ranking');

        // Run the algorithm.
        while(count($this->assigned) < User::players()->count()) {
            foreach ($teams as $team => $players) {
                // What is the current average ranking?
                $rankings = array_column($players, 'ranking');
                $currentPlayerAverage = array_sum($rankings) / count($rankings);

                // What player ranking do we need to bring us closer to the mean?
                $targetSum = $currentPlayerAverage * (count($rankings) + 1);
                $targetRanking = (int)round($targetSum - array_sum($rankings));

                // Let's see if we have a player that meets the criteria.
                $player = User::players()->where('ranking', '=', $targetRanking)->whereNotIn('id', $this->assigned)->inRandomOrder()->first();

                // If we can't find a player that meets the criteria...
                if ($player === null) {
                    // Do we need to bring the average higher or lower?
                    $player = ($currentPlayerAverage >= $globalAverage) ?
                        User::players()->where('ranking', '<', $currentPlayerAverage)->whereNotIn('id', $this->assigned)->orderBy('ranking', 'asc')->first() :
                        User::players()->where('ranking', '>', $currentPlayerAverage)->whereNotIn('id', $this->assigned)->orderBy('ranking', 'desc')->first();
                }

                if ($player) {
                    // Add the player
                    $teams[$team][] = [
                        'name' => $player->full_name,
                        'ranking' => $player->ranking,
                        'goalie' => $player->is_goalie,
                    ];
                    // Mark them as assigned.
                    $this->assigned[] = $player->id;
                }
            }
        }

        return $teams;
    }
}
