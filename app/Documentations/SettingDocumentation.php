<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Settings",
 *  description="Settings for Dobby"
 * )
 */


     /**
     * @OA\GET(
     *     tags={"Settings"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/settings",
     *     description="Currency List",
     *     @OA\Response(response="200", description="Currency Data")
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Settings"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/settings/{setting}",
     *     description="Show a user",
     *     @OA\Parameter(
     *          name="setting",
     *          in="path",
     *          required=true,
     *          description="Setting ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Setting Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Settings"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/settings/{setting}",
     *     description="Update a user",
     *     @OA\Parameter(
     *          name="setting",
     *          in="path",
     *          required=true,
     *          description="Setting ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Setting Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="currency_id",
     *                      type="string",
     *                      example="PHP",
     *                      description="See system-currencies for list of available currencies",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="bill_due",
     *                      type="string",
     *                      example="1",
     *                      description="Due"
     *                  ),
     *                  @OA\Property(property="bill_due_type",
     *                      type="string",
     *                      example="of the current month",
     *                      description="Due Type"
     *                  ),
     *                  @OA\Property(property="invoice_due",
     *                      type="string",
     *                      example="1",
     *                      description="Due"
     *                  ),
     *                  @OA\Property(property="invoice_due_type",
     *                      type="string",
     *                      example="of the current month",
     *                      description="Due Type"
     *                  ),
     *                  @OA\Property(property="quote_due",
     *                      type="string",
     *                      example="1",
     *                      description="Due"
     *                  ),
     *                  @OA\Property(property="quote_due_type",
     *                      type="string",
     *                      example="of the current month",
     *                      description="Due Type"
     *                  ),
     *                  @OA\Property(property="invoice_prefix",
     *                      type="string",
     *                      example="INV-",
     *                      description="Prefix"
     *                  ),
     *                  @OA\Property(property="invoice_next_number",
     *                      type="string",
     *                      example="1",
     *                      description="next number"
     *                  ),
     *                  @OA\Property(property="sales_order_prefix",
     *                      type="string",
     *                      example="INV-",
     *                      description="Prefix"
     *                  ),
     *                  @OA\Property(property="sales_order_next_number",
     *                      type="string",
     *                      example="1",
     *                      description="next number"
     *                  ),
     *                  @OA\Property(property="purchase_order_prefix",
     *                      type="string",
     *                      example="INV-",
     *                      description="Prefix"
     *                  ),
     *                  @OA\Property(property="purchase_order_next_number",
     *                      type="string",
     *                      example="1",
     *                      description="next number"
     *                  ),
     *                  @OA\Property(property="quote_prefix",
     *                      type="string",
     *                      example="INV-",
     *                      description="Prefix"
     *                  ),
     *                  @OA\Property(property="quote_next_number",
     *                      type="string",
     *                      example="1",
     *                      description="next number"
     *                  ),
     *                  @OA\Property(property="credit_note_prefix",
     *                      type="string",
     *                      example="CN-",
     *                      description="Prefix"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */