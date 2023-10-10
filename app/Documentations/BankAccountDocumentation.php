<?php

namespace App\Documentations;

    /**
     * @OA\Tag(
     *  name="Bank Accounts",
     *  description="Bank Accounts for Dobby"
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Bank Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/banks/{account}/transactions",
     *     description="Show an account",
     *     @OA\Parameter(
     *          name="account",
     *          in="path",
     *          required=true,
     *          description="Bank Account ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Bank Account Data"),
     * )
     */

    /**
     * @OA\DELETE(
     *     tags={"Bank Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/banks/{account}/transactions",
     *     description="Show an account",
     *     @OA\Parameter(
     *          name="account",
     *          in="path",
     *          required=true,
     *          description="Bank Account ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id",
     *                          type="string",
     *                          example="b5cafe68-fe81-4770-a783-30b40d5c35a5",
     *                          description="ID of the transaction"
     *                      ),
     *                      @OA\Property(property="type",
     *                          type="string",
     *                          example="transfer-money",
     *                          description="Type of the transaction"
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response="200", description="Bank Account Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Bank Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/banks/{account}/transaction-trends?from={from}&to={to}",
     *     description="Show account trends",
     *     @OA\Parameter(
     *          name="account",
     *          in="path",
     *          required=true,
     *          description="Bank Account ID",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="from",
     *          in="query",
     *          required=true,
     *          description="Bank Account ID",
     *          example="2020-01-01",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="to",
     *          in="query",
     *          required=true,
     *          description="Bank Account ID",
     *          example="2020-01-01",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Bank Account Data"),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Bank Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/banks/transaction-trends?from={from}&to={to}",
     *     description="Show account trends",
     *     @OA\Parameter(
     *          name="from",
     *          in="query",
     *          required=true,
     *          description="Bank Account ID",
     *          example="2020-01-01",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="to",
     *          in="query",
     *          required=true,
     *          description="Bank Account ID",
     *          example="2020-01-01",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Bank Account Data"),
     * )
     */