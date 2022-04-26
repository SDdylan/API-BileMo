<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="API BileMo", version="0.1")
 * @OA\Server(
 *     url="127.0.0/api",
 *     description="Api BileMo"
 * )
 *
 * @OA\SecurityScheme(
 *      bearerFormat="JWT",
 *      securityScheme="bearer",
 *      type="apiKey",
 *      in="header",
 *      name="bearer",
 * )
 *
 **/
