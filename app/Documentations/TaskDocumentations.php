<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Tasks",
 *  description="Tasks for Dobby"
 * )
 */

    /**
     * @OA\GET(
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tasks",
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
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tasks",
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
     *                  @OA\Property(property="charge_type",
     *                      type="string",
     *                      example="HOURLY_RATE",
     *                      description="HOURLY_RATE, FIXED_RATE, NON_CHARGEABLE"
     *                  ),
     *                  @OA\Property(property="rate",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a decimal",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="status",
     *                      type="string",
     *                      example="ON_GOING",
     *                      description="ON_GOING, INVOICED"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */


    /**
     * @OA\PUT(
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tasks/{task}",
     *     description="Update",
     *     @OA\Parameter(
     *          name="task",
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
     *                    @OA\Property(property="project_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Name"
     *                  ),
     *                  @OA\Property(property="charge_type",
     *                      type="string",
     *                      example="HOURLY_RATE",
     *                      description="HOURLY_RATE, FIXED_RATE, NON_CHARGEABLE"
     *                  ),
     *                  @OA\Property(property="rate",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a decimal",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="status",
     *                      type="string",
     *                      example="ON_GOING",
     *                      description="ON_GOING, INVOICED"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/tasks/{task}",
     *     description="trash",
     *     @OA\Parameter(
     *          name="task",
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