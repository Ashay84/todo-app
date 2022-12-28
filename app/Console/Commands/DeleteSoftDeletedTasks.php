<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteSoftDeletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:soft_deleted_tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command deletes all tasks which are soft deleted by user';

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
     * @return int
     */
    public function handle()
    {
        $tskToBeDeleted = Task::whereNotNull('deleted_at')->whereDate('deleted_at', '<', Carbon::now()->subMonth()->endOfDay())->withTrashed();
//        Task::whereNotNull('deleted_at')->forceDelete();
        $tskToBeDeleted->forceDelete();
        Log::info('Number of tasks deleted : ' . $tskToBeDeleted->count());

    }
}
