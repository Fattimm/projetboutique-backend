<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DetteController;
use Laravel\Passport\Http\Controllers\ScopeController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\DenyAuthorizationController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Route::group(['middleware' => ['role:ADMIN']], function () {

    Route::post('users', [UserController::class, 'store']);
    Route::get('users', [UserController::class, 'index']);

    Route::delete('/users/{id}', [UserController::class, 'deleteAccount']);

    // });

    // Route::group(['middleware' => ['role:BOUTIQUIER']], function () {

    Route::patch('/articles/{id}', [ArticleController::class, 'updateStock']);
    Route::post('/articles/stock', [ArticleController::class, 'updateMultipleStocks']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'showById']);
    Route::post('/articles/libelle', [ArticleController::class, 'showByLibelle']);



    Route::get('/clients/filter', [ClientController::class, 'filterByAccount']);
    Route::get('/clients/status', [ClientController::class, 'filterByStatus']);
    Route::post('/clients/telephone', [ClientController::class, 'searchByTelephone']);
    Route::post('/clients/{id}/dettes', [ClientController::class, 'getClientDettes']);
    Route::post('/clients/{id}/user', [ClientController::class, 'getClientWithUser']);
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);

    // });


    Route::post('/dettes', [DetteController::class, 'store']);
    Route::get('/dettes', [DetteController::class, 'index']);
    Route::get('/dettes/{id}', [DetteController::class, 'show']);
    Route::post('/dettes/{id}/articles', [DetteController::class, 'listArticles']);
    Route::get('/dettes/{id}/paiements', [DetteController::class, 'listPaiements']);
    Route::post('/dettes/{id}/paiements', [DetteController::class, 'addPaiement']);



    Route::get('/logout', [AuthController::class, 'logout']);
});







Route::post('/token', [
    'uses' => [AccessTokenController::class, 'issueToken'],
    'as' => 'token',
    'middleware' => 'throttle',
]);

Route::get('/authorize', [
    'uses' => [AuthorizationController::class, 'authorize'],
    'as' => 'authorizations.authorize',
    'middleware' => 'web',
]);

$guard = config('passport.guard', null);

Route::middleware(['web', $guard ? 'auth:' . $guard : 'auth'])->group(function () {
    Route::post('/token/refresh', [
        'uses' => [TransientTokenController::class, 'refresh'],
        'as' => 'token.refresh',
    ]);

    Route::post('/authorize', [
        'uses' => [ApproveAuthorizationController::class, 'approve'],
        'as' => 'authorizations.approve',
    ]);

    Route::delete('/authorize', [
        'uses' => [DenyAuthorizationController::class, 'deny'],
        'as' => 'authorizations.deny',
    ]);

    Route::get('/tokens', [
        'uses' => [AuthorizedAccessTokenController::class, 'forUser'],
        'as' => 'tokens.index',
    ]);

    Route::delete('/tokens/{token_id}', [
        'uses' => [AuthorizedAccessTokenController::class, 'destroy'],
        'as' => 'tokens.destroy',
    ]);

    // Route::get('/clients', [
    //     'uses' => [ClientController::class, 'forUser'],
    //     'as' => 'clients.index',
    // ]);

    // Route::post('/clients', [
    //     'uses' => [ClientController::class, 'store'],
    //     'as' => 'clients.store',
    // ]);

    // Route::put('/clients/{client_id}', [
    //     'uses' => [ClientController::class, 'update'],
    //     'as' => 'clients.update',
    // ]);

    // Route::delete('/clients/{client_id}', [
    //     'uses' => [ClientController::class, 'destroy'],
    //     'as' => 'clients.destroy',
    // ]);

    Route::get('/scopes', [
        'uses' => [ScopeController::class, 'all'],
        'as' => 'scopes.index',
    ]);

    Route::get('/personal-access-tokens', [
        'uses' => [PersonalAccessTokenController::class, 'forUser'],
        'as' => 'personal.tokens.index',
    ]);

    Route::post('/personal-access-tokens', [
        'uses' => [PersonalAccessTokenController::class, 'store'],
        'as' => 'personal.tokens.store',
    ]);

    Route::delete('/personal-access-tokens/{token_id}', [
        'uses' => [PersonalAccessTokenController::class, 'destroy'],
        'as' => 'personal.tokens.destroy',
    ]);
});
