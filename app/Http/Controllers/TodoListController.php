<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoListRequest;
use App\Services\TodoListService;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TodoListController extends Controller
{

    /**
     * set constructor
     */
    public function __construct(protected TodoListService $service,)
    {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listTodoLists(Request $request): JsonResponse
    {
        try {
            $response = $this->service->listTodo($this->getUser(), $request);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param int $todo_list_id
     * @return JsonResponse
     */
    public function findTodoList(Request $request, int $todo_list_id): JsonResponse
    {
        try {
            $response = $this->service->viewTodo($todo_list_id);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param TodoListRequest $request
     * @return JsonResponse
     */
    public function createTodoList(TodoListRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->createTodo($this->getUser(), $validated);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param TodoListRequest $request
     * @param int $todo_list_id
     * @return JsonResponse
     */
    public function updateTodoList(TodoListRequest $request, int $todo_list_id): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->updateTodo($this->getUser(), $todo_list_id, $validated);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param int $todo_list_id
     * @return JsonResponse
     */
    public function deleteTodoList(Request $request, int $todo_list_id): JsonResponse
    {
        try {
            $response = $this->service->deleteTodo($this->getUser(), $todo_list_id);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }



}
