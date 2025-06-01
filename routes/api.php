<?php

require_once 'Router.php';
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AuthenticatedMiddleware;
use App\Middlewares\AuthAgentMiddleware;
use helpers\RateLimiter;
use App\Controllers\AuthController;
use App\Controllers\DepartmentController;
use App\Controllers\TicketController;


/* Authentication routes are below  */

Router::add('POST', '/register', [AuthController::class, 'register']);
Router::add('POST', '/login', [AuthController::class, 'login']);
Router::add('POST', '/logout', function () {
    AuthMiddleware::handle();
    AuthController::logout();
});

/* end */

/* Departments Routes are below */

Router::add('POST', '/departments', function () {
    AuthMiddleware::handle(['admin']);
    DepartmentController::create();
});

Router::add('PUT', '/departments/{id}', function ($id) {
    AuthMiddleware::handle(['admin']);
    DepartmentController::update($id);
});

Router::add('DELETE', '/departments/{id}', function ($id) {
    AuthMiddleware::handle(['admin']);
    DepartmentController::delete($id);
});

/* end */

/* Tickets routes are below */
Router::add('POST', '/tickets', function () {
    AuthenticatedMiddleware::handle();
    
    $userId = $_REQUEST['auth_user']['id'] ?? $_SERVER['REMOTE_ADDR']; 
    RateLimiter::limit("tickets_create_{$userId}", 20, 60); 
    TicketController::create();
});


Router::add('PUT', '/tickets/{id}/assign', function ($id) {
    AuthAgentMiddleware::handle(['agent']);
    TicketController::assign($id);
});

Router::add('PUT', '/tickets/{id}/changeStatus', function ($id) {
    AuthAgentMiddleware::handle(['agent']);
    TicketController::updateStatus($id);
});

Router::add('POST', '/tickets/addnotes/{id}', function ($id) {
    AuthenticatedMiddleware::handle(['agent']);
    TicketController::addNote($id);
});



/* end */

Router::dispatch();