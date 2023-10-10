<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="System",
 *  description="The apis listed below doesn't have permissions, so they are the best way to use if you have dropdowns etc.."
 * )
 */


     /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/tax-rates",
     *     description="Show a list of tax-rates",
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/accounts",
     *     description="Show a list of accounts supported by the system",
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/currencies",
     *     description="Show a list of currencies the organization added",
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/available-currencies",
     *     description="Show a list of available currencies supported by the system",
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/currencies-default",
     *     description="Shows the default currency for this organization",
     *     @OA\Response(response="200", description="Product Search Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/chart-of-accounts",
     *     description="Show a list of chart of account types supported by the system",
     *     @OA\Response(response="200", description="Chart of Accounts Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/chart-of-account-types",
     *     description="Show a list of chart of account types supported by the system",
     *     @OA\Response(response="200", description="Chart of Accounts Type Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/tax-types",
     *     description="Show a list of tax types supported by the system",
     *     @OA\Response(response="200", description="Tax Types Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/due-date-types",
     *     description="Show a list of due date types supported by the system",
     *     @OA\Response(response="200", description="Due Date Types Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/contacts",
     *     description="Show a list of contacts",
     *     @OA\Response(response="200", description="Product Search Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/settings",
     *     description="Show the org settings",
     *     @OA\Response(response="200", description="Product Search Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"System"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system/projects",
     *     description="Show a list of projects",
     *     @OA\Response(response="200", description="Project Search Data"),
     * )
     */