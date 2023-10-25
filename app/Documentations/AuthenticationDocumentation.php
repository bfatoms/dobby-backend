<?php

namespace App\Documentations;

    /**
     * 
     * @OA\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      in="header",
     *      name="bearerAuth",
     *      type="http",
     *      scheme="bearer",
     *      bearerFormat="JWT",
     * )
     */

    /**
     * @OA\Tag(
     *  name="Authentication",
     *  description="Authentication for Dobby"
     * )
     */

     /**
     * @OA\GET(
     *     tags={"Authentication"},
     *     path="/api/auth/verify/{token}",
     *     description="Verify Email",
     *     @OA\Parameter(
     *          name="token",
     *          in="path",
     *          required=true,
     *          description="verification token you get after sign-up",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Email Verified"),
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Authentication"},
     *     path="/api/auth/login",
     *     description="Login",
     *     @OA\Response(response="200", description="Upon Successful Login you can add the Bearer access_token on the Authorization Above click the green icon"),
     *     @OA\Response(response="400", description="When email or password are wrong or in a bad format"),
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
     *                  @OA\Property(property="password",
     *                      type="string",
     *                      example="password",
     *                      description="User Password"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     *
     */


    /**
     * @OA\GET(
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/auth/me",
     *     description="Get the current logged in user of the System",
     *     @OA\Response(response="200", description="returns your data"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          ),
     *      ),
     * )
     */


    /**
     * @OA\POST(
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     path="/api/auth/refresh",
     *     description="Issues a new token",
     *     @OA\Response(response="200", description="This is like a login"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json"
     *      )
     *     )
     * )
     */

    /**
     * @OA\POST(
     *     tags={"Authentication"},
     *     path="/api/auth/forgot-password",
     *     description="Forgot password",
     *     @OA\Response(response="200", description="Forgot Password"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="operating_system",
     *                      type="string",
     *                      example="Mac OS",
     *                      description="First Name",
     *                  ),
     *                  @OA\Property(property="browser",
     *                      type="string",
     *                      example="Demo Inc. App",
     *                      description="Chrome or Your Company App",
     *                  ),
     *                  @OA\Property(property="email",
     *                      type="string",
     *                      example="jarret_oneal4@mailgun.io",
     *                      description="Email address",
     *                      format="email"
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */

    /**
     * @OA\GET(
     *     tags={"Authentication"},
     *     path="/api/auth/reset-password/{token}/check",
     *     description="Check if this reset password token is allowed",
     *     @OA\Parameter(
     *          name="token",
     *          in="path",
     *          required=true,
     *          description="password reset token on your email",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Token Exists"),
     *     @OA\Response(response="422", description="Token Expired or Invalid"),
     * )
     */

    /**
     * @OA\PUT(
     *     tags={"Authentication"},
     *     path="/api/auth/reset-password/{token}",
     *     description="Reset password",
     *     @OA\Parameter(
     *          name="token",
     *          in="path",
     *          required=true,
     *          description="password reset token on your email",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *     ),
     *     @OA\Response(response="200", description="Forgot Password"),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="password",
     *                      type="string",
     *                      example="password",
     *                      description="User Password"
     *                  ),
     *                  @OA\Property(property="password_confirmation",
     *                      type="string",
     *                      example="password",
     *                      description="Password Confirmation"
     *                  ),
     *                  @OA\Property(property="operating_system",
     *                      type="string",
     *                      example="Mac OS",
     *                      description="First Name",
     *                  ),
     *                  @OA\Property(property="browser",
     *                      type="string",
     *                      example="Demo Inc. App",
     *                      description="Chrome or Your Company App",
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */
