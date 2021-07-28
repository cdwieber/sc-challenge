<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BalanceTeamsService;
use App\Models\User;

class PlayersIntegrityTest extends TestCase
{

    private $teams;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $balancer = new BalanceTeamsService();
        $this->teams = $balancer->balanceTeams();
    }

    /**
     * The total number of teams should be even.
     *
     * @return void
     */
    public function testTeamNumberIsEven ()
    {
        $isEven = (count($this->teams) % 2 == 0);
        $this->assertTrue($isEven);
    }

    /**
     * The teams should consist of between 18 and 22 players.
     *
     * @return void
     */
    public function testTeamDistributionWithinParameters ()
    {
        foreach($this->teams as $team => $players) {
            $this->assertThat(
                count($players),
                $this->logicalAnd(
                    $this->greaterThanOrEqual(18),
                    $this->lessThanOrEqual(22)
                )
            );
        }
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGoaliePlayersExist ()
    {
        /*
                Check there are players that have can_play_goalie set as 1
        */
        $result = User::where('user_type', 'player')->where('can_play_goalie', 1)->count();
        $this->assertTrue($result > 1);

    }

    /**
     * All teams require exactly one goalie.
     */
    public function testAtLeastOneGoaliePlayerPerTeam ()
    {
        $this->assertNotNull($this->teams);
        foreach($this->teams as $team => $players) {
            foreach ($players as $player) {
                $hasGoalie = false;
                if ($player['goalie'] == true) {
                    $hasGoalie = true;
                    continue 2;
                }
                $this->assertTrue($hasGoalie);
            }
        }
    }
}
