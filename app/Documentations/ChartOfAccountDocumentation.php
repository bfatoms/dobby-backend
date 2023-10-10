<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Chart of Accounts",
 *  description="Chart of Accounts for Dobby"
 * )
 */


     /**
     * @OA\GET(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts",
     *     description="Chart of Account List",
     *     @OA\Response(response="200", description="Chart of Account Data"),
     *     @OA\Parameter(
     *         name="all",
     *         in="query",
     *         description="If all is enabled it will try to retrieve all data, and may cause the request to crash",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="false",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {true, false},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="limits the data being returned",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="25"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="skips a few data before getting the data, basically if you offset 3, it will start to get the 4th data from the db result",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="0"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="with",
     *         in="query",
     *         description="with will also include relationships",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="false",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"taxRate", "saleContacts", "purchaseContacts", "purchaseProducts", "saleProducts", "costOfGoodsSoldAccounts", "inventoryProducts", "currency"},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortKey",
     *         in="query",
     *         description="sorts data based on a key or relationship.key ex. sortKey=taxRate.name",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="code"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         in="query",
     *         description="sorts a key descending and ascending",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="asc",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"asc", "desc"},
     *             )
     *         )
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts",
     *     description="Create a Chart of Account",
     *     @OA\Response(response="200", description="Chart of Account Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="code",
     *                      type="string",
     *                      example="6003001",
     *                      description="Account Code",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Vat 12",
     *                      description="Chart of Account Name"
     *                  ),
     *                  @OA\Property(property="tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="Chart of Account Tax Rate, see Tax Rates Index for list of available ids"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts/{account}",
     *     description="Show an account",
     *     @OA\Parameter(
     *          name="account",
     *          in="path",
     *          required=true,
     *          description="Chart of Account ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Chart of Account Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts/{account}",
     *     description="Update an account",
     *     @OA\Parameter(
     *          name="account",
     *          in="path",
     *          required=true,
     *          description="Chart of Account ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Chart of Account Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Vat 30",
     *                      description="Chart of Account name",
     *                  ),
     *                  @OA\Property(property="tax_rate_id",
     *                      type="string",
     *                      example="30",
     *                      description="Chart of Account"
     *                  ),
     *                  @OA\Property(property="type",
     *                      type="string",
     *                      example="liabilities",
     *                      description="The type of the Account, see chart-of-account-types for list of available name of types"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts/{account}",
     *     description="trash a Chart of Account",
     *     @OA\Parameter(
     *          name="account",
     *          in="path",
     *          required=true,
     *          description="Chart of Account ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Chart of Account Data"),
     * )
     */


