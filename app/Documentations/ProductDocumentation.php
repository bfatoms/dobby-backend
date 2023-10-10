<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="Products",
 *  description="Products for Dobby"
 * )
 */

    /**
     * @OA\GET(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products/search",
     *     description="Show a product",
     *     @OA\Parameter(
     *         name="order_type",
     *         in="query",
     *         description="order types",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="BILL"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search term",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="25"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Product Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products/{product}/transaction-history",
     *     description="Show a products transaction history",
     *     @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Product Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products",
     *     description="Product List",
     *     @OA\Response(response="200", description="Product Data"),
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
     *                 enum = {"quantityOnHand", "purchaseTaxRate", "saleTaxRate", "costOfGoodsSoldAccount", "purchaseAccount", "saleAccount", "inventoryAssetAccount"},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="to search a data just send in the key on the parameter then add a value see example",
     *         example="primaryPerson.email=like:louie",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             default="like:001"
     *         )
     *     ),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products",
     *     description="Create a product",
     *     @OA\Response(response="200", description="Product Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="code",
     *                      type="string",
     *                      example="10001",
     *                      description="Product Code is a string",
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Spaceship Apollo",
     *                      description="Product Name"
     *                  ),
     *                  @OA\Property(property="is_purchased",
     *                      type="string",
     *                      example=true,
     *                      description="boolean",
     *                      format="boolean"
     *                  ),
     *                  @OA\Property(property="purchase_price",
     *                      type="string",
     *                      example=12.3541,
     *                      description="Purchase Price",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="purchase_tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Tax Rates of list available Tax Rate",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="purchase_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *                  @OA\Property(property="purchase_description",
     *                      type="string",
     *                      example="A Super Lightweight aluminum-steel alloy designed to withstand missions in space",
     *                      description="Description is 255 characters long",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="cost_of_goods_sold_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *                  @OA\Property(property="is_sold",
     *                      type="string",
     *                      example=true,
     *                      description="boolean",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="sale_price",
     *                      type="string",
     *                      example=1000.00,
     *                      description="Sale Price"
     *                  ),
     *                  @OA\Property(property="sale_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *                  @OA\Property(property="sale_tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Tax Rates of list available Tax Rate"
     *                  ),
     *                  @OA\Property(property="sale_description",
     *                      type="string",
     *                      example="A Super Lightweight aluminum-steel alloy designed to withstand missions in space",
     *                      description="Product Description"
     *                  ),
     *                  @OA\Property(property="is_tracked",
     *                      type="string",
     *                      example=true,
     *                      description="If this is a product that is tracked"
     *                  ),
     *                  @OA\Property(property="inventory_asset_account_id",
     *                      type="string",
     *                      example="Vat 12",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products/{product_id}",
     *     description="Show a product",
     *     @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Product Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products/{product_id}",
     *     description="Update a product",
     *     @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Product Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="code",
     *                      type="string",
     *                      example="10001",
     *                      description="Product Code is a string",
     *                  ),
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Spaceship Apollo",
     *                      description="Product Name"
     *                  ),
     *                  @OA\Property(property="is_purchased",
     *                      type="string",
     *                      example=true,
     *                      description="boolean",
     *                      format="boolean"
     *                  ),
     *                  @OA\Property(property="purchase_price",
     *                      type="string",
     *                      example=12.3541,
     *                      description="Purchase Price",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="purchase_tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Tax Rates of list available Tax Rate",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="purchase_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *                  @OA\Property(property="purchase_description",
     *                      type="string",
     *                      example="A Super Lightweight aluminum-steel alloy designed to withstand missions in space",
     *                      description="Description is 255 characters long",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="cost_of_goods_sold_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *                  @OA\Property(property="is_sold",
     *                      type="string",
     *                      example=true,
     *                      description="boolean",
     *                      format="decimal"
     *                  ),
     *                  @OA\Property(property="sale_price",
     *                      type="string",
     *                      example=1000.00,
     *                      description="Sale Price"
     *                  ),
     *                  @OA\Property(property="sale_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *                  @OA\Property(property="sale_tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Tax Rates of list available Tax Rate"
     *                  ),
     *                  @OA\Property(property="sale_description",
     *                      type="string",
     *                      example="A Super Lightweight aluminum-steel alloy designed to withstand missions in space",
     *                      description="Product Description"
     *                  ),
     *                  @OA\Property(property="is_tracked",
     *                      type="string",
     *                      example=true,
     *                      description="If this is a product that is tracked"
     *                  ),
     *                  @OA\Property(property="inventory_asset_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="See Chart of Accounts for available Account"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/products/{product_id}",
     *     description="trash a product",
     *     @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Product Data"),
     * )
     */