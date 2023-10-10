<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProjectsView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-view:projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Projects View';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::statement("DROP VIEW IF EXISTS user_project_rates");
        DB::statement("
        create view user_project_rates as
            select
            projects.id as project_id,
            time_entries.user_id as user_id,
            sum(time_entries.duration) as total_duration,
            ISNULL(project_prices.id) AND project_settings.id IS NOT NULL as is_project_setting_id,
            project_settings.product_id as project_setting_product_id,
            
            /* 
            projects.name as project_name,
            tasks.id as task_id,
            tasks.name as task_name,
            time_entries.user_id as 'time_entry_user_id', */
            /* products.sale_price as 'service_sales_price',
            products.purchase_price as 'service_purchase_price',
            project_settings.sales_price as 'global_sales_price',
            project_settings.purchase_price as 'global_purchase_price',
            project_prices.sales_price as 'override_sales_price',
            project_prices.sales_price as 'override_purchase_price', */
            
            count(time_entries.user_id) as time_entries_count,
            (
                case
                    when project_prices.sales_price IS NOT NULL then project_prices.sales_price
                       when project_settings.sales_price IS NOT NULL then project_settings.sales_price
                    when  products.sale_price IS NOT NULL then  products.sale_price
                end
            ) as final_sales_price,
            (
                case
                    when project_prices.purchase_price IS NOT NULL then project_prices.purchase_price
                       when project_settings.purchase_price IS NOT NULL then project_settings.purchase_price
                    when  products.purchase_price IS NOT NULL then  products.purchase_price
                end
            ) as final_purchase_price
             from
            projects
            left join tasks on tasks.project_id = projects.id
            left join time_entries on time_entries.task_id = tasks.id
            left join project_prices on project_prices.project_id = projects.id and project_prices.user_id = time_entries.user_id
            left join project_settings on project_settings.user_id = time_entries.user_id
            left join products on products.id = project_settings.product_id
            group by projects.id, time_entries.user_id, project_settings.id, project_prices.id
        ");

        DB::statement("DROP VIEW IF EXISTS user_project_costs");
        DB::statement("
        create view user_project_costs as
            select
            sum(user_project_rates.final_sales_price * user_project_rates.total_duration) as sum_sales_price,
            sum(user_project_rates.final_purchase_price * user_project_rates.total_duration) as sum_purchase_price,
            user_project_rates.user_id,
            projects.id as project_id
            from
            projects
            left join user_project_rates on user_project_rates.project_id = projects.id
            group by projects.id, user_project_rates.user_id;
        ");

        DB::statement("DROP VIEW IF EXISTS project_costs");
        DB::statement("
            create view project_costs as
                select
                count(user_project_costs.user_id) as user_count,
                IFNULL(sum(user_project_costs.sum_purchase_price), 0) + sum(IFNULL(expenses.unit_price, 0) * IFNULL(expenses.quantity, 0)) as cost,
                projects.id as project_id
                from
                projects
                left join user_project_costs on user_project_costs.project_id = projects.id
                left join expenses on expenses.project_id = projects.id
                group by projects.id, user_project_costs.project_id
        ");

        DB::statement("DROP VIEW IF EXISTS project_order_line_amounts;");
        DB::statement("
        create view project_order_line_amounts as
            select
            id as order_line_id,
            project_id,
            quantity * (unit_price / (1+IFNULL(tax_rate,0))) * (1-IFNULL(discount,0)) as total_amount,
            quantity,
            unit_price,
            tax_rate,
            discount
            from
            order_lines;
        ");

        DB::statement("DROP VIEW IF EXISTS project_invoices;");
        DB::statement("
        create view project_invoices as
            select
            id as project_id,
            sum(total_amount) as invoiced
            from
            projects
            left join project_order_line_amounts on project_order_line_amounts.project_id = projects.id
            group by id
        ");

        DB::statement("DROP VIEW IF EXISTS projects_view");
        DB::statement("
            create view projects_view as
            select
            IF(projects.compute_estimate = 1, (
                select 
                sum(
                        case 
                            when tasks.charge_type = 'FIXED_RATE' then tasks.rate
                            when tasks.charge_type = 'HOURLY_RATE' then tasks.estimated_hours * tasks.rate
                        end
                        +
                        case
                        when expenses.is_estimated = 1 then expenses.unit_price * expenses.quantity
                        end
                    )
                from projects
                left join tasks on tasks.project_id = projects.id
                left join expenses on expenses.project_id = projects.id
            ), projects.estimate) as estimate,
            sum(tasks_view.total_amount) + sum(expenses_view.estimated_charge_amount) as time_and_expense,
            project_costs.cost as cost,
            project_invoices.invoiced as invoiced,
            NULL as profit,
            projects.compute_estimate,
            projects.id,
            projects.name,
            projects.contact_id,
            projects.deadline,
            projects.status,
            projects.created_at,
            projects.updated_at
            from
            projects
            left join expenses_view on expenses_view.project_id = projects.id
            left join tasks_view on tasks_view.project_id = projects.id
            left join project_costs on project_costs.project_id = projects.id
            left join project_invoices on project_invoices.project_id = projects.id
            group by projects.id, project_costs.project_id
        ");

        return true;
    }
}
