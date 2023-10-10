<?php

namespace App\Documentations;


/**
 * @OA\Tag(
 *  name="Permission",
 *  description="Permission for Dobby"
 * )
 */

     /**
     * @OA\GET(
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/permissions",
     *     description="Module Permission List",
     *     @OA\Response(response="200", description="Permission list"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/users/{user}/modules/{module}/permissions/{action}/toggle",
     *     description="Module Permission List",
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
     *          description="User ID of the user granting permission to",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="module",
     *          in="path",
     *          required=true,
     *          description="Module of the action where it belongs to",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="action",
     *          in="path",
     *          required=true,
     *          description="The action you want to have for a user",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Permission list"),
     * )
     */