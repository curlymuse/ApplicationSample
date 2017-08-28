<?php namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class DropTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:drop-tables
        {--force : Drop the tables without confirmation/interaction.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all the tables in the current db.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->option('force')
            && ! $this->confirm('Do you really want to drop all the tables in the current database? [y|N]')) {

            error('Drop Tables command aborted');
            return;
        }

        DB::beginTransaction();
        //turn off referential integrity
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach(\DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            \Schema::drop($table_array[key($table_array)]);
        }
        //turn referential integrity back on
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        DB::commit();

        $this->comment(PHP_EOL . 'If no errors were thrown above, all tables were dropped.' . PHP_EOL);

    }
}
