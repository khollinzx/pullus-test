<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoListItemRequest;
use App\Services\TodoListItemService;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TodoListItemController extends Controller
{

    /**
     * set constructor
     */
    public function __construct(protected TodoListItemService $service,)
    {}

    /**
     * @param Request $request
     * @param int $todo_list_id
     * @return JsonResponse
     */
    public function listTodoListItems(Request $request, int $todo_list_id): JsonResponse
    {
        try {
            $response = $this->service->listTodoListItems($this->getUser(), $todo_list_id);
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
    public function findTodoListItem(Request $request, int $todo_list_id): JsonResponse
    {
        try {
            $response = $this->service->viewTodoList($todo_list_id);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param TodoListItemRequest $request
     * @param int $todo_list_id
     * @return JsonResponse
     */
    public function createTodoListItem(TodoListItemRequest $request, int $todo_list_id): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->createTodo($this->getUser(), $todo_list_id, $validated);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param TodoListItemRequest $request
     * @param int $item_id
     * @return JsonResponse
     */
    public function updateTodoListItem(TodoListItemRequest $request, int $item_id): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->updateTodo($this->getUser(), $item_id, $validated);
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
    public function deleteTodoListItem(Request $request, int $todo_list_id): JsonResponse
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
