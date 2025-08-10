<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    /**
     * set constructor
     */
    public function __construct(
        protected UserService $service,
    )
    {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUserByUsername(Request $request): JsonResponse
    {
        try {
            $response = $this->service->handleSearchUserByUsername($this->getUser(), $request);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $user_id
     * @param int $todo_list
     * @return JsonResponse
     */
    public function addUserToTodoList(int $user_id, int $todo_list): JsonResponse
    {
        try {
            $response = $this->service->handleAddUserToATodoList($this->getUser(), $user_id, $todo_list);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }
}
