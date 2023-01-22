<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TrialSeed extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial:seed {trial_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds database with demo data for testing purposes only';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return int
     * @throws \ReflectionException
     */
    public function handle() {
        $seeder = new \TrialSeeder();
        return $seeder->run($this->argument('trial_id'));
    }
}
