<?php

namespace App\Documentations;

/**
 * @OA\Tag(
 *  name="IMEX",
 *  description="Import Export for Dobby"
 * )
 */

    /**
     * @OA\POST(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts/import",
     *     description="Import Data for Chart of Accounts",
     *     @OA\Response(response="200", description="Chart of Account Data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="file",
     *                      type="file",
     *                      description="only accepts csv file",
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */


    /**
     * @OA\GET(
     *     tags={"Chart of Accounts"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-accounts/export",
     *     description="Import Data for Chart of Accounts",
     *     @OA\Response(response="200", description="Chart of Account Data"),
     * )
     */