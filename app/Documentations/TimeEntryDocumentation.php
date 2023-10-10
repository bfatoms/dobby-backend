<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Time Entries",
 *  description="Time Entries for Dobby"
 * )
 */
    /**
     * @OA\GET(
     *     tags={"Time Entries"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/time-entries",
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
     *             default="task",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"task"},
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
     *             default="type"
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
     *     tags={"Time Entries"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/time-entries",
     *     description="Create",
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="task_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="user_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="description",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Description"
     *                  ),
     *                  @OA\Property(property="type",
     *                      type="string",
     *                      example="DURATION",
     *                      description="DURATION, START_END"
     *                  ),
     *                  @OA\Property(property="start_at",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Date"
     *                  ),
     *                  @OA\Property(property="end_at",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Date"
     *                  ),
     *                  @OA\Property(property="time_entry_date",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Date"
     *                  ),
     *                  @OA\Property(property="duration",
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
     *     tags={"Time Entries"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/time-entries/{time_entry}",
     *     description="Update",
     *     @OA\Parameter(
     *          name="time_entry",
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
     *                   @OA\Property(property="task_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="user_id",
     *                      type="string",
     *                      example="",
     *                      description="ID"
     *                  ),
     *                  @OA\Property(property="description",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Description"
     *                  ),
     *                  @OA\Property(property="type",
     *                      type="string",
     *                      example="DURATION",
     *                      description="DURATION, START_END"
     *                  ),
     *                  @OA\Property(property="start_at",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Date"
     *                  ),
     *                  @OA\Property(property="end_at",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Date"
     *                  ),
     *                  @OA\Property(property="time_entry_date",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Date"
     *                  ),
     *                  @OA\Property(property="duration",
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
     *     tags={"Time Entries"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/time-entries/{time_entry}",
     *     description="trash",
     *     @OA\Parameter(
     *          name="time_entry",
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