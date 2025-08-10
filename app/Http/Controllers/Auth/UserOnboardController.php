<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\OnBoardRequest;
use App\Mail\SendMailNotification;
use App\Models\Bank;
use App\Models\Country;
use App\Models\Gender;
use App\Models\OauthAccessToken;
use App\Models\State;
use App\Models\User;
use App\Services\UserService;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserOnboardController extends Controller
{

    /**
     * set constructor
     */
    public function __construct(protected UserService $service,)
    {}

    /** signup section
     */
    public function register(OnBoardRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $response = $this->service->handleUserRegistration($validated);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }

    /** login section
     */
    public function login(OnBoardRequest $request)
    {
        $validated = $request->validated();
        try {
            $response = $this->service->handleUserLogin($validated);
            if (!$response->status) return JsonResponseAPI::errorResponse($response->message);
            return JsonResponseAPI::successResponse($response->message, $response->data, JsonResponseAPI::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonResponseAPI::errorResponse("Internal server error.", JsonResponseAPI::HTTP_BAD_REQUEST);
        }
    }
}
