<?php

namespace App\Documentations;


/**
 * @OA\Tag(
 *  name="User",
 *  description="User for Dobby"
 * )
 */

     /**
     * @OA\POST(
     *     tags={"User"},
     *     path="/api/users/invite",
     *     description="Invite a user",
     *     @OA\Response(response="200", description="User Invite"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="first_name",
     *                      type="string",
     *                      example="Jarrett",
     *                      description="First Name",
     *                  ),
     *                  @OA\Property(property="last_name",
     *                      type="string",
     *                      example="O'Neal IV",
     *                      description="Last Name",
     *                  ),
     *                  @OA\Property(property="email",
     *                      type="string",
     *                      example="jarret_oneal4@mailgun.io",
     *                      description="Email address",
     *                      format="email"
     *                  ),
     *                  @OA\Property(property="password",
     *                      type="string",
     *                      example="password",
     *                      description="User Password"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}/project-price",
     *     description="User project price",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="User Project price Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="project_id",
     *                      type="string",
     *                      example="",
     *                      description="Project ID"
     *                  ),
     *                  @OA\Property(property="sales_price",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a decimal",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="purchase_price",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a decimal",
     *                      format="decimal"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users",
     *     description="User List",
     *     @OA\Response(response="200", description="User Data"),
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
     *         description="calls a relationship of the model ex. ?with=avatar,attachments",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="avatar,attachments"
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
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users",
     *     description="Create a user",
     *     @OA\Response(response="200", description="User Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="email",
     *                      type="string",
     *                      example="tomas@test.com",
     *                      description="Email address",
     *                      format="email"
     *                  ),
     *                  @OA\Property(property="first_name",
     *                      type="string",
     *                      example="Levi",
     *                      description="User First Name"
     *                  ),
     *                  @OA\Property(property="last_name",
     *                      type="string",
     *                      example="Levi",
     *                      description="User Last Name"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}",
     *     description="Show a user",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="User Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}",
     *     description="Update a user",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="User Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="email",
     *                      type="string",
     *                      example="tomas@test.com",
     *                      description="Email address",
     *                      format="email"
     *                  ),
     *                  @OA\Property(property="first_name",
     *                      type="string",
     *                      example="Levi",
     *                      description="User First Name"
     *                  ),
     *                  @OA\Property(property="last_name",
     *                      type="string",
     *                      example="Levi",
     *                      description="User Last Name"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}",
     *     description="trash a user",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="User Data"),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}/avatar",
     *     description="Upload a User Avatar",
     *     @OA\Response(response="200", description="User Data"),
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="file",
     *                      type="file",
     *                      description="Avatar to use",
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}/permissions",
     *     description="Show a users permissions",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="User Data"),
     * )
     */