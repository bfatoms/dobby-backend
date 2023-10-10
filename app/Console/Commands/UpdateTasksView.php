<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateTasksView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-view:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Tasks View';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::statement("DROP VIEW IF EXISTS tasks_view");

        DB::statement("
            create view tasks_view as
            select
            count(tasks.id) as time_entries_count,
            sum(time_entries.duration) as total_hours,
            (
                case
                    when tasks.charge_type = 'HOURLY_RATE' then sum(time_entries.duration) * tasks.rate
                    when tasks.charge_type = 'FIXED_RATE' then tasks.rate
                    else 0
                end
            ) as total_amount,
            tasks.project_id,
            tasks.id,
            tasks.name,
            tasks.charge_type,
            tasks.rate,
            tasks.status,
            tasks.created_at,
            tasks.updated_at
            from tasks
            left join time_entries on time_entries.task_id = tasks.id
            group by tasks.id
        ");

        return true;
    }
}
