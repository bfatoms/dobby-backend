<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateExpensesView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-view:expenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Expenses View';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::statement("DROP VIEW IF EXISTS expenses_view");

        DB::statement("
            create view expenses_view as
                select
                (
                    case
                        when expenses.charge_type = 'MARK_UP' then expenses.quantity * expenses.unit_price * (1 + expenses.mark_up)
                        when expenses.charge_type = 'PASS_COST_ALONG' then expenses.quantity * expenses.unit_price
                        when expenses.charge_type = 'CUSTOM_PRICE' then expenses.custom_price * expenses.quantity
                        else 0
                    end
                ) as estimated_charge_amount,
                expenses.*
                from
                expenses
            ");

        return true;
    }
}
