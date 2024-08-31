<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController;
use Laravel\Passport\Http\Controllers\DenyAuthorizationController;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;
use Laravel\Passport\Http\Controllers\ScopeController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:api')->group(function () {
    // Route::post('/articles', [ArticleController::class, 'store']);
    // Route::get('articles', [ArticleController::class, 'index']);
    // Route::patch('articles/{id}', [ArticleController::class, 'updateStock']);
    // Route::post('articles/stock', [ArticleController::class, 'updateMultipleStocks']);
    // Afficher les détails d'un article par ID
    // Route::get('articles/{id}', [ArticleController::class, 'showById']);
    // Afficher les détails d'un article par libellé
    // Route::post('articles/libelle', [ArticleController::class, 'showByLibelle']);
// });

Route::prefix('v1')->group(function () {
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store','show']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::patch('/articles/{id}', [ArticleController::class, 'updateStock']);
    Route::post('/articles/stock', [ArticleController::class, 'updateMultipleStocks']);
    // Afficher les détails d'un article par ID
    Route::get('/articles/{id}', [ArticleController::class, 'showById']);
    // Afficher les détails d'un article par libellé
    Route::post('/articles/libelle', [ArticleController::class, 'showByLibelle']);

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

Route::middleware(['web', $guard ? 'auth:'.$guard : 'auth'])->group(function () {
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

    Route::get('/clients', [
        'uses' => [ClientController::class, 'forUser'],
        'as' => 'clients.index',
    ]);

    Route::post('/clients', [
        'uses' => [ClientController::class, 'store'],
        'as' => 'clients.store',
    ]);

    Route::put('/clients/{client_id}', [
        'uses' => [ClientController::class, 'update'],
        'as' => 'clients.update',
    ]);

    Route::delete('/clients/{client_id}', [
        'uses' => [ClientController::class, 'destroy'],
        'as' => 'clients.destroy',
    ]);

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
