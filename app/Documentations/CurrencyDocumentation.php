<?php

namespace App\Documentations;


/**
 * @OA\Tag(
 *  name="Currencies",
 *  description="Currencies for Dobby"
 * )
 */

    /**
     * @OA\GET(
     *     tags={"Currencies"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/system-currencies",
     *     description="Show a list of currencies supported by the system",
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Currencies"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/currencies",
     *     description="Currency List",
     *     @OA\Response(response="200", description="Currency Data"),
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
     *                 enum = {"chartOfAccounts", "orders"},
     *             )
     *         )
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Currencies"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/currencies",
     *     description="Create a Currency",
     *     @OA\Response(response="200", description="Currency Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="code",
     *                      type="string",
     *                      example="PHP",
     *                      description="3 character code",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Philippine Peso",
     *                      description="Currency Name"
     *                  ),
     *                  @OA\Property(property="symbol",
     *                      type="string",
     *                      example="₱",
     *                      description="Currency Name"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Currencies"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/currencies/{currency}",
     *     description="Show a user",
     *     @OA\Parameter(
     *          name="currency",
     *          in="path",
     *          required=true,
     *          description="Currency ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Currencies"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/currencies/{currency}",
     *     description="Update a user",
     *     @OA\Parameter(
     *          name="currency",
     *          in="path",
     *          required=true,
     *          description="Currency ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Currency Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="code",
     *                      type="string",
     *                      example="PHP",
     *                      description="3 character code",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Philippine Peso",
     *                      description="Currency Name"
     *                  ),
     *                  @OA\Property(property="symbol",
     *                      type="string",
     *                      example="₱",
     *                      description="Currency Name"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Currencies"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/currencies/{currency}",
     *     description="trash a Currency",
     *     @OA\Parameter(
     *          name="currency",
     *          in="path",
     *          required=true,
     *          description="Currency ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Currency Data"),
     * )
     */