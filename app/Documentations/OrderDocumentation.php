<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Orders",
 *  description="Orders for Dobby"
 * )
 */

     /**
     * @OA\GET(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders/initial-data",
     *     description="Contact List",
     *     @OA\Response(response="200", description="Contact Data"),
     *     @OA\Parameter(
     *         name="order_type",
     *         in="query",
     *         description="Order Type of the order you are creating",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="false",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"SO", "PO", "QU", "BILL", "INV", "RMD", "RMO", "RMP", "SMD", "SMP", "SMO"},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="query",
     *         description="See Contacts for list of contacts",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="90c1a47a-6ba8-43d8-beda-54bdd21bef71"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortKey",
     *         in="query",
     *         description="sortKey must be, field_name or relationship.field_name, ex. sortKey=name or sortKey=taxRate.name",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default=""
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         in="query",
     *         description="sortOrder must be, asc or desc sortOrder=asc",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default=""
     *         )
     *     ),
     * )
     */


         // SWAGGER

    /**
     * @OA\GET(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders",
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
     *         name="order_type",
     *         in="query",
     *         description="Returns all Orders regarless of type",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"SO", "PO", "QU", "BILL", "INV", "RMD", "RMO", "RMP", "SMD", "SMP", "SMO"},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Returns all Orders that has this status",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default=false,
     *             @OA\Items(
     *                 type="boolean",
     *                 enum = {"DRAFT", "FOR_APPROVAL", "APPROVED", "PAID", "VOID", "DELETED", "INVOICED", "BILLED", "ACCEPTED", "SENT", "DECLINED", "SALES"},
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
     *                 enum = {"currency", "contact", "payments", "refunds", "creditNotePayments"},
     *             )
     *         )
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders",
     *     description="Create an Order",
     *     @OA\Response(response="200", description="Order Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="order_type",
     *                      type="string",
     *                      example="BILL",
     *                      description="Order types are SO, PO, QU, BILL, INV, RM, SM}"
     *                  ),
     *                  @OA\Property(property="contact_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="see contacts for IDS"
     *                  ),
     *                  @OA\Property(property="contact_name",
     *                      type="string",
     *                      example="",
     *                      description="Contact Name is required if contact_id is not provided"
     *                  ),
     *                  @OA\Property(property="order_date",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Order Date"
     *                  ),
     *                  @OA\Property(property="end_date",
     *                      type="string",
     *                      example="2020-06-09 07:16:26",
     *                      description="end date is not required when is credit note is true"
     *                  ),
     *                  @OA\Property(property="reference",
     *                      type="string",
     *                      example="Suscipit eaque rerum rerum. Perferendis vitae unde et et excepturi.",
     *                      description="Reference is a longText"
     *                  ),
     *                  @OA\Property(property="tax_setting",
     *                      type="string",
     *                      example="no tax",
     *                      description="tax setting are, no tax, tax exclusive, tax inclusive"
     *                  ),
     *                  @OA\Property(property="currency_id",
     *                      type="string",
     *                      example="1",
     *                      description="See currencies for available currencies"
     *                  ),
     *                  @OA\Property(property="quotation_project_id",
     *                      type="string",
     *                      example="",
     *                      description="Project ID"
     *                  ),
     *                  @OA\Property(property="quotation_title",
     *                      type="string",
     *                      example="Quote For Elon Musk",
     *                      description="Title On Quote"
     *                  ),
     *                  @OA\Property(property="quotation_summary",
     *                      type="string",
     *                      example="StarLink, Starship, Dragon Capsules",
     *                      description="Summary of the quotation"
     *                  ),
     *                  @OA\Property(property="status",
     *                      type="string",
     *                      example="APPROVED",
     *                      description="Status for the order, currently supports, DRAFT, FOR_APPROVAL, APPROVED, PAID, VOID, SENT"
     *                  ),
     *                  @OA\Property(property="exchange_rate",
     *                      type="string",
     *                      example="1",
     *                      description="is by default 1, if you set a different other than the default it will divide it by the exchange rate"
     *                  ),
     *                  @OA\Property(property="order_lines",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="product_id",
     *                            type="string",
     *                            example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                            description="Contact Person First name"
     *                          ),
     *                          @OA\Property(property="description",
     *                            type="string",
     *                            example="Illo laboriosam eius sint quae sit iste ut quisquam.",
     *                            description="Contact Person Last name"
     *                          ),
     *                          @OA\Property(property="quantity",
     *                            type="string",
     *                            example="10",
     *                            description="Contact Person Email"
     *                          ),
     *                          @OA\Property(property="unit_price",
     *                            type="string",
     *                            example="1500",
     *                            description="Contact Person is the primary contact person"
     *                          ),
     *                          @OA\Property(property="discount",
     *                            type="string",
     *                            example="0.10",
     *                            description="Contact Person is included on emails"
     *                          ),
     *                          @OA\Property(property="tax_rate",
     *                            type="string",
     *                            example="0.00",
     *                            description="tax_rate"
     *                          ),
     *                          @OA\Property(property="tax_rate_id",
     *                            type="string",
     *                            example="1",
     *                            description="See tax Rates"
     *                          ),
     *                          @OA\Property(property="chart_of_account_id",
     *                            type="string",
     *                            example="7",
     *                            description="See Chart Of Accounts"
     *                          ),
     *                          @OA\Property(property="project_id",
     *                            type="string",
     *                            example="1",
     *                            description="See Projects"
     *                          ),
     *                          @OA\Property(property="expense_id",
     *                            type="string",
     *                            example="1",
     *                            description="See Expenses"
     *                          ),
     *                      )
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders/{order}",
     *     description="Show an Order",
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
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders/{order}",
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
     *                  @OA\Property(property="order_type",
     *                      type="string",
     *                      example="BILL",
     *                      description="Order types are SO, PO, INV, QU, BILL"
     *                  ),
     *                  @OA\Property(property="contact_id",
     *                      type="string",
     *                      example="90b90e14-d344-4fbd-932b-8ef18fb1fb54",
     *                      description="see contacts for IDS"
     *                  ),
     *                  @OA\Property(property="contact_name",
     *                      type="string",
     *                      example="",
     *                      description="Contact Name is required if contact_id is not provided"
     *                  ),
     *                  @OA\Property(property="order_date",
     *                      type="string",
     *                      example="2020-06-02 07:16:26",
     *                      description="Order Date"
     *                  ),
     *                  @OA\Property(property="end_date",
     *                      type="string",
     *                      example="2020-06-09 07:16:26",
     *                      description="end date is not required when is credit note is true"
     *                  ),
     *                  @OA\Property(property="reference",
     *                      type="string",
     *                      example="Suscipit eaque rerum rerum. Perferendis vitae unde et et excepturi.",
     *                      description="Reference is a longText"
     *                  ),
     *                  @OA\Property(property="tax_setting",
     *                      type="string",
     *                      example="no tax",
     *                      description="tax setting are, no tax, tax exclusive, tax inclusive"
     *                  ),
     *                  @OA\Property(property="currency_id",
     *                      type="string",
     *                      example="1",
     *                      description="See currencies for available currencies"
     *                  ),
     *                  @OA\Property(property="quotation_title",
     *                      type="string",
     *                      example="Quote For Elon Musk",
     *                      description="Title On Quote"
     *                  ),
     *                  @OA\Property(property="quotation_summary",
     *                      type="string",
     *                      example="StarLink, Starship, Dragon Capsules",
     *                      description="Summary of the quotation"
     *                  ),
     *                  @OA\Property(property="status",
     *                      type="string",
     *                      example="APPROVED",
     *                      description="Status for the order, currently supports, DRAFT, FOR_APPROVAL, APPROVED, PAID, VOID, SENT"
     *                  ),
     *                  @OA\Property(property="exchange_rate",
     *                      type="string",
     *                      example="1",
     *                      description="is by default 1, if you set a different other than the default it will divide it by the exchange rate"
     *                  ),
     *                  @OA\Property(property="order_lines",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="product_id",
     *                            type="string",
     *                            example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                            description="Contact Person First name"
     *                          ),
     *                          @OA\Property(property="description",
     *                            type="string",
     *                            example="Illo laboriosam eius sint quae sit iste ut quisquam.",
     *                            description="Contact Person Last name"
     *                          ),
     *                          @OA\Property(property="quantity",
     *                            type="string",
     *                            example="10",
     *                            description="Contact Person Email"
     *                          ),
     *                          @OA\Property(property="unit_price",
     *                            type="string",
     *                            example="1500",
     *                            description="Contact Person is the primary contact person"
     *                          ),
     *                          @OA\Property(property="discount",
     *                            type="string",
     *                            example="0.10",
     *                            description="Contact Person is included on emails"
     *                          ),
     *                          @OA\Property(property="tax_rate",
     *                            type="string",
     *                            example="0.00",
     *                            description="tax_rate"
     *                          ),
     *                          @OA\Property(property="tax_rate_id",
     *                            type="string",
     *                            example="1",
     *                            description="See tax Rates"
     *                          ),
     *                          @OA\Property(property="chart_of_account_id",
     *                            type="string",
     *                            example="7",
     *                            description="See Chart Of Accounts"
     *                          ),
     *                          @OA\Property(property="delete",
     *                            type="boolean",
     *                            example=true,
     *                            description="when set to true this will delete the orderline"
     *                          ),
     *                          @OA\Property(property="project_id",
     *                            type="string",
     *                            example="1",
     *                            description="See Projects"
     *                          ),
     *                          @OA\Property(property="expense_id",
     *                            type="string",
     *                            example="1",
     *                            description="See Expenses"
     *                          ),
     *                      )
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/orders/{order}",
     *     description="trash an Order",
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
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/order-activities",
     *     description="Show Order Activities",
     *     @OA\Response(response="200", description="Order Data"),
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="query",
     *         description="return order activity by contact_id",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="25"
     *         )
     *     ),
     * )
     */