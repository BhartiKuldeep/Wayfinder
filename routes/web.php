<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\PlaceController;
use App\Controllers\RouteController;
use App\Controllers\SavedRouteController;

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/places', [PlaceController::class, 'index']);
$router->get('/places/create', [PlaceController::class, 'create']);
$router->post('/places', [PlaceController::class, 'store']);
$router->get('/places/{id}', [PlaceController::class, 'show']);
$router->get('/admin/places/{id}/edit', [PlaceController::class, 'edit']);
$router->post('/admin/places/{id}/update', [PlaceController::class, 'update']);
$router->post('/admin/places/{id}/delete', [PlaceController::class, 'destroy']);

$router->get('/route', [RouteController::class, 'index']);
$router->post('/route/find', [RouteController::class, 'find']);
$router->post('/routes/save', [RouteController::class, 'save']);
$router->get('/saved-routes', [SavedRouteController::class, 'index']);
$router->post('/saved-routes/{id}/delete', [SavedRouteController::class, 'destroy']);

$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->post('/admin/categories', [AdminController::class, 'storeCategory']);
$router->get('/admin/edges', [AdminController::class, 'edges']);
$router->post('/admin/edges', [AdminController::class, 'storeEdge']);
$router->post('/admin/edges/{id}/delete', [AdminController::class, 'destroyEdge']);
