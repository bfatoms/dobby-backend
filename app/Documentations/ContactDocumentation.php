<?php

namespace App\Documentations;


/**
 * @OA\Tag(
 *  name="Customers and Suppliers",
 *  description="Contact for Dobby"
 * )
 */

     /**
     * @OA\GET(
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts/tax-types",
     *     description="Shows a list of contact tax types",
     *     @OA\Response(response="200", description="Contact Tax Type Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts/due-date-types",
     *     description="Show a list of contact due date types",
     *     @OA\Response(response="200", description="Contact Due Date Type Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts",
     *     description="Contact List",
     *     @OA\Response(response="200", description="Contact Data"),
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
     *             default="false",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"primaryPerson", "contactPersons", "saleAccount", "saleTaxRate", "purchaseAccount", "purchaseTaxRate", "sales", "purchases", "quotes", "bills", "billCreditNotes", "invoices", "invoiceCreditNotes", "spendMoney", "receiveMoney", "orders"},
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
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts",
     *     description="Create a Contact",
     *     @OA\Response(response="200", description="Contact Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Contact Name"
     *                  ),
     *                  @OA\Property(property="mobile_number",
     *                      type="string",
     *                      example="981237412",
     *                      description="Contact Mobile Number"
     *                  ),
     *                  @OA\Property(property="website",
     *                      type="string",
     *                      example="https://manilastyles.com",
     *                      description="Contact Website"
     *                  ),
     *                  @OA\Property(property="address",
     *                      type="string",
     *                      example="1234 Rufino st cor. Valero",
     *                      description="Contact Address"
     *                  ),
     *                  @OA\Property(property="city",
     *                      type="string",
     *                      example="Makati",
     *                      description="Contact City"
     *                  ),
     *                  @OA\Property(property="zip",
     *                      type="string",
     *                      example="1043",
     *                      description="Contact Zip"
     *                  ),
     *                  @OA\Property(property="sale_tax_type",
     *                      type="string",
     *                      example="tax exlusive",
     *                      description="Contact Sales Tax Type see Tax Types for list of available Tax Types"
     *                  ),
     *                  @OA\Property(property="sale_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="Contact Sales Account ID see Chart Of Accounts for available accounts"
     *                  ),
     *                  @OA\Property(property="purchase_tax_type",
     *                      type="string",
     *                      example="tax inclusive",
     *                      description="Contact Purchase Tax Type see Tax Types for list of available Tax Types"
     *                  ),
     *                  @OA\Property(property="purchase_account_id",
     *                      type="string",
     *                      example="2",
     *                      description="Contact Purchase Account ID see Chart Of Accounts for available accounts"
     *                  ),
     *                  @OA\Property(property="tax_identification_number",
     *                      type="string",
     *                      example="303-303-303-000",
     *                      description="Contact Tax Identification Number ex. (EIN, TIN)"
     *                  ),
     *                  @OA\Property(property="sale_tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="Contact Sales Tax Rate ID see Tax Rate Index for list of available tax rates"
     *                  ),
     *                  @OA\Property(property="purchase_tax_rate_id",
     *                      type="string",
     *                      example="2",
     *                      description="Contact Purchases Tax Rate ID see Tax Rate Index for list of available tax rates"
     *                  ),
     *                  @OA\Property(property="business_registration_number",
     *                      type="string",
     *                      example="B2012-12398740",
     *                      description="Contact Business Registration Number"
     *                  ),
     *                  @OA\Property(property="credit_limit",
     *                      type="string",
     *                      example="1000000",
     *                      description="Contact Credit Limit"
     *                  ),
     *                  @OA\Property(property="sale_discount",
     *                      type="string",
     *                      example="10",
     *                      description="Contact Sales Discount"
     *                  ),
     *                  @OA\Property(property="bill_due",
     *                      type="string",
     *                      example="8",
     *                      description="Contact Bill Due is a whole number say ex. 5"
     *                  ),
     *                  @OA\Property(property="bill_due_type",
     *                      type="string",
     *                      example="of the following month",
     *                      description="Contact Bill Due Type is a string"
     *                  ),
     *                  @OA\Property(property="invoice_due",
     *                      type="string",
     *                      example="8",
     *                      description="Contact Invoice Due is a whole number say ex. 5"
     *                  ),
     *                  @OA\Property(property="invoice_due_type",
     *                      type="string",
     *                      example="of the following month",
     *                      description="Contact Invoice Due Type is a string"
     *                  ),
     *                  @OA\Property(property="contact_persons",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="first_name",
     *                            type="string",
     *                            example="Shockeil",
     *                            description="Contact Person First name"
     *                          ),
     *                          @OA\Property(property="last_name",
     *                            type="string",
     *                            example="O'Nail",
     *                            description="Contact Person Last name"
     *                          ),
     *                          @OA\Property(property="email_name",
     *                            type="string",
     *                            example="hello@manilastyles.com",
     *                            description="Contact Person Email"
     *                          ),
     *                          @OA\Property(property="is_primary",
     *                            type="boolean",
     *                            example="true",
     *                            description="Contact Person is the primary contact person"
     *                          ),
     *                          @OA\Property(property="include_in_emails",
     *                            type="boolean",
     *                            example="false",
     *                            description="Contact Person is included on emails"
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
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts/{contact}",
     *     description="Show a contact",
     *     @OA\Parameter(
     *          name="contact",
     *          in="path",
     *          required=true,
     *          description="Contact ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Contact Data"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts/{contact}",
     *     description="Update a contact",
     *     @OA\Parameter(
     *          name="contact",
     *          in="path",
     *          required=true,
     *          description="Contact ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Contact Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="name",
     *                      type="string",
     *                      example="Manila Styles",
     *                      description="Contact Name"
     *                  ),
     *                  @OA\Property(property="mobile_number",
     *                      type="string",
     *                      example="981237412",
     *                      description="Contact Mobile Number"
     *                  ),
     *                  @OA\Property(property="website",
     *                      type="string",
     *                      example="https://manilastyles.com",
     *                      description="Contact Website"
     *                  ),
     *                  @OA\Property(property="address",
     *                      type="string",
     *                      example="1234 Rufino st cor. Valero",
     *                      description="Contact Address"
     *                  ),
     *                  @OA\Property(property="city",
     *                      type="string",
     *                      example="Makati",
     *                      description="Contact City"
     *                  ),
     *                  @OA\Property(property="zip",
     *                      type="string",
     *                      example="1043",
     *                      description="Contact Zip"
     *                  ),
     *                  @OA\Property(property="sale_tax_type",
     *                      type="string",
     *                      example="tax exlusive",
     *                      description="Contact Sales Tax Type see Tax Types for list of available Tax Types"
     *                  ),
     *                  @OA\Property(property="sale_account_id",
     *                      type="string",
     *                      example="1",
     *                      description="Contact Sales Account ID see Chart Of Accounts for available accounts"
     *                  ),
     *                  @OA\Property(property="purchase_tax_type",
     *                      type="string",
     *                      example="tax inclusive",
     *                      description="Contact Purchase Tax Type see Tax Types for list of available Tax Types"
     *                  ),
     *                  @OA\Property(property="purchase_account_id",
     *                      type="string",
     *                      example="2",
     *                      description="Contact Purchase Account ID see Chart Of Accounts for available accounts"
     *                  ),
     *                  @OA\Property(property="tax_identification_number",
     *                      type="string",
     *                      example="303-303-303-000",
     *                      description="Contact Tax Identification Number ex. (EIN, TIN)"
     *                  ),
     *                  @OA\Property(property="sale_tax_rate_id",
     *                      type="string",
     *                      example="1",
     *                      description="Contact Sales Tax Rate ID see Tax Rate Index for list of available tax rates"
     *                  ),
     *                  @OA\Property(property="purchase_tax_rate_id",
     *                      type="string",
     *                      example="2",
     *                      description="Contact Purchases Tax Rate ID see Tax Rate Index for list of available tax rates"
     *                  ),
     *                  @OA\Property(property="business_registration_number",
     *                      type="string",
     *                      example="B2012-12398740",
     *                      description="Contact Business Registration Number"
     *                  ),
     *                  @OA\Property(property="credit_limit",
     *                      type="string",
     *                      example="1000000",
     *                      description="Contact Credit Limit"
     *                  ),
     *                  @OA\Property(property="sale_discount",
     *                      type="string",
     *                      example="10",
     *                      description="Contact Sales Discount"
     *                  ),
     *                  @OA\Property(property="bill_due",
     *                      type="string",
     *                      example="8",
     *                      description="Contact Bill Due is a whole number say ex. 5"
     *                  ),
     *                  @OA\Property(property="bill_due_type",
     *                      type="string",
     *                      example="of the following month",
     *                      description="Contact Bill Due Type is a string"
     *                  ),
     *                  @OA\Property(property="invoice_due",
     *                      type="string",
     *                      example="8",
     *                      description="Contact Invoice Due is a whole number say ex. 5"
     *                  ),
     *                  @OA\Property(property="invoice_due_type",
     *                      type="string",
     *                      example="of the following month",
     *                      description="Contact Invoice Due Type is a string"
     *                  ),
     *                  @OA\Property(property="contact_persons",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="first_name",
     *                            type="string",
     *                            example="Shockeil",
     *                            description="Contact Person First name"
     *                          ),
     *                          @OA\Property(property="last_name",
     *                            type="string",
     *                            example="O'Nail",
     *                            description="Contact Person Last name"
     *                          ),
     *                          @OA\Property(property="email_name",
     *                            type="string",
     *                            example="hello@manilastyles.com",
     *                            description="Contact Person Email"
     *                          ),
     *                          @OA\Property(property="is_primary",
     *                            type="boolean",
     *                            example="true",
     *                            description="Contact Person is the primary contact person"
     *                          ),
     *                          @OA\Property(property="include_in_emails",
     *                            type="boolean",
     *                            example="false",
     *                            description="Contact Person is included on emails"
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
     *     tags={"Customers and Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/contacts/{contact}",
     *     description="trash a Contact",
     *     @OA\Parameter(
     *          name="contact",
     *          in="path",
     *          required=true,
     *          description="Contact ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Contact Data"),
     * )
     */