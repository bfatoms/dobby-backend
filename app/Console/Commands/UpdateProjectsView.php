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
            CREATE VIEW user_project_rates AS
            SELECT
                projects.id AS project_id,
                time_entries.user_id AS user_id,
                SUM(time_entries.duration) AS total_duration,
                CASE
                    WHEN project_prices.id IS NULL AND project_settings.id IS NOT NULL THEN 1
                    ELSE 0
                END AS is_project_setting_id,
                project_settings.product_id AS project_setting_product_id,
                COUNT(time_entries.user_id) AS time_entries_count,
                (
                    CASE
                        WHEN project_prices.sales_price IS NOT NULL THEN project_prices.sales_price
                        WHEN project_settings.sales_price IS NOT NULL THEN project_settings.sales_price
                        WHEN products.sale_price IS NOT NULL THEN products.sale_price
                    END
                ) AS final_sales_price,
                (
                    CASE
                        WHEN project_prices.purchase_price IS NOT NULL THEN project_prices.purchase_price
                        WHEN project_settings.purchase_price IS NOT NULL THEN project_settings.purchase_price
                        WHEN products.purchase_price IS NOT NULL THEN products.purchase_price
                    END
                ) AS final_purchase_price
            FROM
                projects
            LEFT JOIN tasks ON tasks.project_id = projects.id
            LEFT JOIN time_entries ON time_entries.task_id = tasks.id
            LEFT JOIN project_prices ON project_prices.project_id = projects.id AND project_prices.user_id = time_entries.user_id
            LEFT JOIN project_settings ON project_settings.user_id = time_entries.user_id
            LEFT JOIN products ON products.id = project_settings.product_id
            GROUP BY projects.id, time_entries.user_id, project_settings.id, project_prices.id;
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
