<?php

namespace App;

/**
 * @OA\Info(
 *     title="Blog API",
 *     version="1.0.0",
 *     description="REST API for the Blog application. Provides endpoints for managing posts, categories, tags, comments, and user authentication.",
 *     @OA\Contact(
 *         email="admin@digikteam.com",
 *         name="DigiK Team"
 *     )
 * )
 *
 * The server url is relative on purpose: the docs are served by the same app as
 * the API, so it resolves against whatever host is being browsed and needs no
 * per-environment change. Templated forms like "{protocol}://{host}" only work
 * when matching ServerVariable entries are declared, and are otherwise emitted
 * literally, which breaks "Try it out".
 *
 * @OA\Server(
 *     url="/",
 *     description="API Server"
 * )
 */
class SwaggerInfo
{
    //
}
