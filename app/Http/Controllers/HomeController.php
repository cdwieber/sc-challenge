<?php

namespace App\Http\Controllers;

use App\Services\BalanceTeamsService;

class HomeController extends Controller
{
    /**
     * Display the homepage and results.
     */
    public function index() {
        $balancer = new BalanceTeamsService();
        $teams = $balancer->balanceTeams();

        return view('welcome', ['teams' => $teams]);
    }
}
