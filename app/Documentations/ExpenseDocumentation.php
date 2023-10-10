<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Expenses",
 *  description="Expense for Dobby"
 * )
 */

    /**
     * @OA\POST(
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/expenses/track",
     *     description="Track Estimated Expense",
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="estimated_expense_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="tracked_expense_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/expenses",
     *     description="List",
     *     @OA\Response(response="200", description="Data"),
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
     *         description="skips a few data before getting the data, basically if you offset 3, it will start to get the 4th data from the db result",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="project",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"project"},
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
     *             default="name"
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
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/expenses",
     *     description="Create",
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="project_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Name"
     *                  ),
     *                  @OA\Property(property="quantity",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="unit_price",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="charge_type",
     *                      type="string",
     *                      example="MARK_UP",
     *                      description="MARK_UP, PASS_COST_ALONG, CUSTOM_PRICE, NON_CHARGEABLE"
     *                  ),
     *                  @OA\Property(property="mark_up",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="custom_price",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="is_invoiced",
     *                      type="boolean",
     *                      example=true,
     *                      description=""
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */


    /**
     * @OA\PUT(
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/expenses/{expense}",
     *     description="Update",
     *     @OA\Parameter(
     *          name="expense",
     *          in="path",
     *          required=true,
     *          description="ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                   @OA\Property(property="project_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Name"
     *                  ),
     *                  @OA\Property(property="quantity",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="unit_price",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="charge_type",
     *                      type="string",
     *                      example="MARK_UP",
     *                      description="MARK_UP, PASS_COST_ALONG, CUSTOM_PRICE, NON_CHARGEABLE"
     *                  ),
     *                  @OA\Property(property="mark_up",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="custom_price",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a number",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="is_invoiced",
     *                      type="boolean",
     *                      example=true,
     *                      description=""
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Expenses"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/expenses/{expense}",
     *     description="trash",
     *     @OA\Parameter(
     *          name="expense",
     *          in="path",
     *          required=true,
     *          description="ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Data"),
     * )
     */