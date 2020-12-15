<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DemoSeed extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:seed {email?}';

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
        $seeder = new \DemoSeeder();
        $email = $this->argument('email');
        return $seeder->run($email);
    }
}
