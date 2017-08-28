<?php

namespace App\Console;

use App\Console\Commands\ClearDataFilePieces;
use App\Console\Commands\DataTranslate\TruncateFiles;
use App\Console\Commands\DropTables;
use App\Console\Commands\CompileApiDocuments;
use App\Console\Commands\Generators\Test\GenerateLogTest;
use App\Console\Commands\IngestArnData;
use App\Console\Commands\SendDailyLicenseeProposalUpdate;
use App\Console\Commands\SendProposalExpiringReminders;
use App\Console\Commands\SendRequestExpiringReminders;
use App\Console\Commands\Generators\GenerateCondition;
use App\Console\Commands\Generators\GenerateJobPolicy;
use App\Console\Commands\Generators\GenerateRepository;
use App\Console\Commands\Generators\GenerateRepositoryInterface;
use App\Console\Commands\Generators\GenerateTransformer;
use App\Console\Commands\Generators\Test\GenerateControllerTest;
use App\Console\Commands\Generators\Test\GenerateJobPolicyTest;
use App\Console\Commands\Generators\Test\GenerateJobTest;
use App\Console\Commands\Generators\Test\GenerateMailerTest;
use App\Console\Commands\Generators\Test\GenerateRepositoryTest;
use App\Console\Commands\SendWeeklyLicenseeDigest;
use App\Console\Commands\SplitDataFiles;
use App\Console\Commands\PullRemoteArnData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DropTables::class,
        CompileApiDocuments::class,
        GenerateRepository::class,
        GenerateJobPolicy::class,
        GenerateCondition::class,
        GenerateTransformer::class,
        GenerateMailerTest::class,
        GenerateRepositoryInterface::class,
        GenerateRepositoryTest::class,
        GenerateControllerTest::class,
        GenerateJobTest::class,
        GenerateLogTest::class,
        GenerateJobPolicyTest::class,
        SendRequestExpiringReminders::class,
        SendProposalExpiringReminders::class,
        SendWeeklyLicenseeDigest::class,
        SendDailyLicenseeProposalUpdate::class,
        TruncateFiles::class,
        IngestArnData::class,
        SplitDataFiles::class,
        ClearDataFilePieces::class,
        PullRemoteArnData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command(SendRequestExpiringReminders::class)
        //     ->everyMinute()
        //     ->withoutOverlapping();

        // $schedule->command(SendProposalExpiringReminders::class)
        //     ->everyMinute()
        //     ->withoutOverlapping();

        $schedule->command(SendWeeklyLicenseeDigest::class)
            ->weekly()
            ->withoutOverlapping();

        $schedule->command(SendDailyLicenseeProposalUpdate::class)
            ->daily()
            ->withoutOverlapping();
    }
    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
