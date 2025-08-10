<?php

use App\Http\Controllers\Auth\UserOnboardController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\TodoListItemController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::get('welcome', [Controller::class, 'welcome']);
    Route::group(['middleware' => ['validate.headers']], function () {
        # This manages all the internal calls and implementation of the endpoints
        Route::group(['prefix' => 'onboard' ], function () {
            Route::post('login', [UserOnboardController::class, 'login']);
            Route::post('register', [UserOnboardController::class, 'register']);
        });

        Route::group(['middleware' => ['manage.access']], function () {
            Route::group(['middleware' => ['auth:api']], function () {
                Route::group(['prefix' => 'users'], function () {
                    Route::get('search', [UserController::class, 'searchUserByUsername']);
                    Route::put('user/{user_id}/add-user-to-todo-list/{todo_list_id}', [UserController::class, 'addUserToTodoList']);
                });

                Route::group(['prefix' => 'todos'], function () {
                    Route::get('', [TodoListController::class, 'listTodoLists']);
                    Route::get('todo/{todo_id}', [TodoListController::class, 'findTodoList']);
                    Route::post('add', [TodoListController::class, 'createTodoList']);
                    Route::put('todo/{todo_id}', [TodoListController::class, 'updateTodoList']);
                    Route::delete('todo/{todo_id}', [TodoListController::class, 'deleteTodoList']);
                    Route::group(['prefix' => 'items'], function () {
                        Route::get('{todo_list_id}', [TodoListItemController::class, 'listTodoListItems']);
                        Route::get('item/{item_id}', [TodoListItemController::class, 'findTodoListItem']);
                        Route::post('{todo_list_id}/add', [TodoListItemController::class, 'createTodoListItem']);
                        Route::put('/{item_id}/edit', [TodoListItemController::class, 'updateTodoListItem']);
                        Route::delete('{item_id}', [TodoListItemController::class, 'deleteTodoListItem']);
                    });
                });
            });
        });
    });
});
