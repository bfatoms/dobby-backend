<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Transfer Money",
 *  description="Transfer Monies for Dobby"
 * )
 */


     /**
     * @OA\GET(
     *     tags={"Transfer Money"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/transfer-monies",
     *     description="Order List",
     *     @OA\Response(response="200", description="Order Data"),
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
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Transfer Money"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/transfer-monies",
     *     description="Create a Transfer Money",
     *     @OA\Response(response="200", description="Transfer Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="from_bank_account_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="Must be of type Bank see chart of accounts"
     *                  ),
     *                  @OA\Property(property="to_bank_account_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="Must be of type Bank see chart of accounts"
     *                  ),
     *                  @OA\Property(property="from_amount",
     *                      type="string",
     *                      example="",
     *                      description="Must be of type Bank see chart of accounts"
     *                  ),
     *                  @OA\Property(property="to_amount",
     *                      type="string",
     *                      example="",
     *                      description="To Amount"
     *                  ),
     *                  @OA\Property(property="reference",
     *                      type="string",
     *                      example="2020-06-09 07:16:26",
     *                      description="Reference is a longText"
     *                  ),
     *                  @OA\Property(property="order_date",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="end date is not required when is credit note is true"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Transfer Money"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/transfer-monies/{transfer}",
     *     description="Show an Transfer",
     *     @OA\Parameter(
     *          name="transfer",
     *          in="path",
     *          required=true,
     *          description=" ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Transfer Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Transfer Money"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/transfer-monies/{order}",
     *     description="Update an order",
     *     @OA\Parameter(
     *          name="order",
     *          in="path",
     *          required=true,
     *          description="Order ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Order Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="from_bank_account_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="Must be of type Bank see chart of accounts"
     *                  ),
     *                  @OA\Property(property="to_bank_account_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="Must be of type Bank see chart of accounts"
     *                  ),
     *                  @OA\Property(property="from_amount",
     *                      type="string",
     *                      example="",
     *                      description="Must be of type Bank see chart of accounts"
     *                  ),
     *                  @OA\Property(property="to_amount",
     *                      type="string",
     *                      example="",
     *                      description="To Amount"
     *                  ),
     *                  @OA\Property(property="reference",
     *                      type="string",
     *                      example="2020-06-09 07:16:26",
     *                      description="Reference is a longText"
     *                  ),
     *                  @OA\Property(property="order_date",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="end date is not required when is credit note is true"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */