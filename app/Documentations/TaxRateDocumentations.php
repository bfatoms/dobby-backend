<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Tax Rates",
 *  description="Tax Rates for Dobby"
 * )
 */

    /**
     * @OA\GET(
     *     tags={"Tax Rates"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tax-rates",
     *     description="Tax Rate List",
     *     @OA\Response(response="200", description="Tax Rate Data"),
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
     *         name="name",
     *         in="query",
     *         description="find name",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default=""
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortKey",
     *         in="query",
     *         description="sorts data based on a key",
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
     *             default="false",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"asc", "desc"},
     *             )
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
     *                 enum = {"saleTaxRates", "purchaseTaxRates", "productSales", "productPurchases", "orderLines", "chartOfAccounts"},
     *             )
     *         )
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Tax Rates"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tax-rates",
     *     description="Create a tax rate",
     *     @OA\Response(response="200", description="Tax Rate Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="rate",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a whole number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Vat 12",
     *                      description="Tax Rate Name"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Tax Rates"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tax-rates/{rate}",
     *     description="Show a user",
     *     @OA\Parameter(
     *          name="rate",
     *          in="path",
     *          required=true,
     *          description="Tax Rate ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Tax Rate Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Tax Rates"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tax-rates/{rate}",
     *     description="Update a user",
     *     @OA\Parameter(
     *          name="rate",
     *          in="path",
     *          required=true,
     *          description="Tax Rate ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Tax Rate Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Vat 30",
     *                      description="Tax rate name",
     *                  ),
     *                  @OA\Property(property="rate",
     *                      type="string",
     *                      example="30",
     *                      description="Tax Rate"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Tax Rates"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tax-rates/{rate}",
     *     description="trash a tax rate",
     *     @OA\Parameter(
     *          name="rate",
     *          in="path",
     *          required=true,
     *          description="Tax Rate ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Tax Rate Data"),
     * )
     */