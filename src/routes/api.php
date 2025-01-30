<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DynamoDbController;
use App\Http\Controllers\OrderController;

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

// Route::post('/user/save', [UserController::class, 'saveUser']);
// Route::post('/insert-item', [DynamoDbController::class, 'insertItem']);
// Route::post('/create-table', [DynamoDbController::class, 'createTable']);
// Route::get('/list-table', [DynamoDbController::class, 'listTables']);

// Route::post('/save-item', [DynamoDBController::class, 'saveToDynamoDB']);
Route::post('/insert-item', [DynamoDBController::class, 'insertItem']);
Route::get('/dynamodb/items', [DynamoDBController::class, 'getItems']);
// Route::put('/store/{storeID}/order/{orderID}/remark', [DynamoDBController::class, 'updateRemark']);

Route::post('/createTable', [OrderController::class, 'createTable']);
Route::post('/addItems', [OrderController::class, 'addItems']);
Route::put('/store/{storeId}/updateRemarks', [OrderController::class, 'updateRemarks']);

Route::get('/store/{storeId}/getStoreData', [OrderController::class, 'getStoreData']);
