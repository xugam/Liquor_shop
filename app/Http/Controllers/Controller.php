<?php

namespace App\Http\Controllers;

/**
 *  @OA\Info(
 *     version="1.0",
 *     title="Liquor Shop",
 *     description="Liquor Shop Project"
 * )
 *
 *  @OA\Server(
 *     url="http://192.168.100.118:8080/api/",
 *     description="Localhost API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization"
 * )
 */
abstract class Controller
{
    //
}
