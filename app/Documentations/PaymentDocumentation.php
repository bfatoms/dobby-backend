<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Payments",
 *  description="Payments for Dobby"
 * )
 */

     /**
     * @OA\GET(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/payments",
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
     *                 enum = {"orders", "creditOrder"},
     *             )
     *         )
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders/{order}/pay",
     *     description="Pay an Order single payment",
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
     *                  @OA\Property(property="amount",
     *                      type="string",
     *                      example="1000.00",
     *                      description="Payment Amount"
     *                  ),
     *                  @OA\Property(property="paid_at",
     *                      type="string",
     *                      example="2020-01-01 12:00:00",
     *                      description="Payment Date"
     *                  ),
     *                  @OA\Property(property="chat_of_account_id",
     *                      type="string",
     *                      example=null,
     *                      description="Required if not Credit Note, Over Payment, Pre Payment"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/payments",
     *     description="Multiple Payments on Order",
     *     @OA\Response(response="200", description="Payments Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="paid_at",
     *                      type="string",
     *                      example="2020-01-01T8:00:00+8",
     *                      description="Payment date"
     *                  ),
     *                  @OA\Property(property="chart_of_account_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="see chart of accounts for IDS"
     *                  ),
     *                  @OA\Property(property="reference",
     *                      type="string",
     *                      example="Perferendis molestiae.",
     *                      description="Contact Name is required if contact_id is not provided"
     *                  ),
     *                  @OA\Property(property="exchange_rate",
     *                      type="string",
     *                      example="0.776",
     *                      description="Order Date"
     *                  ),
     *                  @OA\Property(property="orders",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id",
     *                            type="string",
     *                            example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                            description="Order ID"
     *                          ),
     *                          @OA\Property(property="amount",
     *                            type="string",
     *                            example="1000.00",
     *                            description="Payment Amount"
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/payments/credit-notes",
     *     description="Multiple Payments on Order",
     *     @OA\Response(response="200", description="Payments Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="paid_at",
     *                      type="string",
     *                      example="2020-01-01T8:00:00+8",
     *                      description="Payment date"
     *                  ),
     *                  @OA\Property(property="credit_note_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="see chart of accounts for IDS"
     *                  ),
     *                  @OA\Property(property="reference",
     *                      type="string",
     *                      example="Perferendis molestiae.",
     *                      description="Contact Name is required if contact_id is not provided"
     *                  ),
     *                  @OA\Property(property="orders",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id",
     *                            type="string",
     *                            example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                            description="Order ID"
     *                          ),
     *                          @OA\Property(property="amount",
     *                            type="string",
     *                            example="1000.00",
     *                            description="Payment Amount"
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders/{order}/refund",
     *     description="Refunds an amount on Order",
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
     *                  @OA\Property(property="amount",
     *                      type="string",
     *                      example="1000.00",
     *                      description="Refund Amount"
     *                  ),
     *                  @OA\Property(property="paid_at",
     *                      type="string",
     *                      example="2020-01-01 12:00:00",
     *                      description="Refund Date"
     *                  ),
     *                  @OA\Property(property="chat_of_account_id",
     *                      type="string",
     *                      example=null,
     *                      description="Required if not Credit Note, Over Payment, Pre Payment"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */