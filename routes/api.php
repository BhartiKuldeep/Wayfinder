<?php

use App\Controllers\Api\AdminDiagnosticsController;
use App\Controllers\Api\HealthController;
use App\Controllers\Api\PlaceApiController;
use App\Controllers\Api\RouteApiController;

$router->get('/api/health', [HealthController::class, 'index']);
$router->get('/api/places', [PlaceApiController::class, 'index']);
$router->get('/api/places/suggestions', [PlaceApiController::class, 'suggestions']);
$router->get('/api/places/{id}', [PlaceApiController::class, 'show']);
$router->get('/api/routes/shortest', [RouteApiController::class, 'shortest']);
$router->get('/api/routes/report', [RouteApiController::class, 'report']);
$router->get('/api/admin/graph-diagnostics', [AdminDiagnosticsController::class, 'graph']);
