<?php

namespace App\Documentations;



/**
 * @OA\Tag(
 *  name="Projects",
 *  description="Projects for Dobby"
 * )
 */
    /**
     * @OA\GET(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/price-settings",
     *     description="Get employees cost and sales price under a project",
     *     @OA\Response(response="200", description="Get employees cost and sales price under a project"),
     *     @OA\Parameter(
     *          name="project",
     *          in="path",
     *          required=true,
     *          description="Project ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/invoice",
     *     description="Create Invoice from Project",
     *     @OA\Parameter(
     *          name="project",
     *          in="path",
     *          required=true,
     *          description="Project ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="rate_type",
     *                      type="boolean",
     *                      example="",
     *                      description="TASK_RATE,EMPLOYEE_RATE"
     *                  ),
     *                  @OA\Property(property="from_date",
     *                      type="date",
     *                      example="",
     *                      description="2021-01-01"
     *                  ),
     *                  @OA\Property(property="to_date",
     *                      type="string",
     *                      example="",
     *                      description="2021-01-30"
     *                  ),
     *                  @OA\Property(property="orderline_option",
     *                      type="number",
     *                      example="",
     *                      description="1,2,3"
     *                  ),
     *                  @OA\Property(property="time_entries",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                          description="ID"
     *                      ),
     *                  ),
     *                  @OA\Property(property="tasks",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                          description="ID"
     *                      ),
     *                  ),
     *                  @OA\Property(property="expenses",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                          description="ID"
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/invoice-fixed-amount",
     *     description="Invoice Fixed Amount",
     *     @OA\Parameter(
     *          name="project",
     *          in="path",
     *          required=true,
     *          description="Project ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="mark_all_as_invoiced",
     *                      type="boolean",
     *                      example="",
     *                      description=""
     *                  ),
     *                  @OA\Property(property="close_project",
     *                      type="boolean",
     *                      example="",
     *                      description=""
     *                  ),
     *                  @OA\Property(property="amount",
     *                      type="number",
     *                      example="",
     *                      description=""
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/quotation-fixed-amount",
     *     description="Create quotation from a project",
     *     @OA\Parameter(
     *          name="project",
     *          in="path",
     *          required=true,
     *          description="Project ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="quotation_title",
     *                      type="string",
     *                      example="",
     *                      description=""
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/quotations",
     *     description="Project Quotations",
     *     @OA\Response(response="200", description="Project Data"),
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         description="Project ID",
     *         required=false,
     *         explode=true,
     *     ),
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
     *             default="contact",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"contact"},
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
     * @OA\GET(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/invoices",
     *     description="Project Invoices",
     *     @OA\Response(response="200", description="Project Data"),
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         description="Project ID",
     *         required=false,
     *         explode=true,
     *     ),
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
     *             default="contact",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"contact"},
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
     * @OA\GET(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects",
     *     description="Project List",
     *     @OA\Response(response="200", description="Project Data"),
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
     *             default="contact",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"contact"},
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
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects",
     *     description="Create a Project",
     *     @OA\Response(response="200", description="Project Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Project Name"
     *                  ),
     *                  @OA\Property(property="contact_id",
     *                      type="string",
     *                      example="",
     *                      description="Contact ID"
     *                  ),
     *                  @OA\Property(property="contact_name",
     *                      type="string",
     *                      example="John Doe",
     *                      description="If no contact id, this will be used"
     *                  ),
     *                  @OA\Property(property="deadline",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Deadline"
     *                  ),
     *                  @OA\Property(property="estimate",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a decimal",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="status",
     *                      type="string",
     *                      example="DRAFT",
     *                      description="DRAFT, IN_PROGRESS, CLOSED"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}",
     *     description="Update a project",
     *     @OA\Parameter(
     *          name="project",
     *          in="path",
     *          required=true,
     *          description="Project ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Project Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                   @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Project Name"
     *                  ),
     *                  @OA\Property(property="contact_id",
     *                      type="string",
     *                      example="",
     *                      description="Contact ID"
     *                  ),
     *                  @OA\Property(property="contact_name",
     *                      type="string",
     *                      example="John Doe",
     *                      description="If no contact id, this will be used"
     *                  ),
     *                  @OA\Property(property="deadline",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Deadline"
     *                  ),
     *                  @OA\Property(property="estimate",
     *                      type="string",
     *                      example="12",
     *                      description="Must be a decimal",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="status",
     *                      type="string",
     *                      example="DRAFT",
     *                      description="DRAFT, IN_PROGRESS, CLOSED"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */


    /**
     * @OA\DELETE(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}",
     *     description="trash",
     *     @OA\Parameter(
     *          name="project",
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

    /**
     * @OA\GET(
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/projects/{project}/invoice-initial-data",
     *     description="Project Invoice Initial Data",
     *     @OA\Response(response="200", description="Project Invoice Data"),
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         description="Project ID",
     *         required=false,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="From Date",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="2020-01-01"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="To Date",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="2020-01-20"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="is_invoiced",
     *         in="query",
     *         description="Is Invoiced",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="0"
     *         )
     *     ),
     * )
     */