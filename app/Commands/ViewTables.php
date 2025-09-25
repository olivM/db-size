<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use DB;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use function Termwind\{render};

class ViewTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'),);
        Config::set('database.connections.mysql.port', env('DB_PORT', '3306'),);
        Config::set('database.connections.mysql.database', env('LOCAL_DB_NAME', 'mysql'));
        Config::set('database.connections.mysql.username', env('LOCAL_DB_USER', 'root'));
        Config::set('database.connections.mysql.password', env('LOCAL_DB_PWD', ''));

        DB::purge('mysql');


        // get tables size for the current database
        $tables = DB::select('
            SELECT
                table_name AS "Table",
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size"
            FROM information_schema.TABLES
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC;
        ');

        $total = collect($tables)->sum('Size');
        collect($tables)->map(function ($table) use ($total) {
            $table->Percentage = round($table->Size / $total * 100, 2);
            return $table;
        });


        render(
            view('tables', [
                'tables' => $tables
            ])
        );
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
