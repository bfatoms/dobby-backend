<?php

namespace App\Documentations;

    /**
     * @OA\Tag(
     *  name="Chart of Account Types",
     *  description="Chart of Account Types for Dobby"
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Chart of Account Types"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/chart-of-account-types",
     *     description="Chart of Account Type Index",
     *     @OA\Response(response="200", description="Chart of Account Types Data"),
     * )
     */