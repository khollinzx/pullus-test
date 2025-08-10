<?php

namespace App\Services;

use App\Abstractions\Implementations\ThirdParties\AnchorService;
use App\Abstractions\Implementations\ThirdParties\DojahService;
use App\Abstractions\Implementations\ThirdParties\PaystackService;
use App\Enums\ServiceResponseMessage;
use App\Events\BroadcastUserNotification;
use App\Jobs\CreateCustomerDetailJob;
use App\Jobs\FetchCustomerDetailJob;
use App\Jobs\PerformKYCVerificationJob;
use App\Jobs\SetUpUserDepositAccountJob;
use App\Jobs\SetUpUserEscrowAccountJob;
use App\Jobs\VerifyCustomerDetailJob;
use App\Models\Country;
use App\Models\OauthAccessToken;
use App\Models\ThirdPartyProvider;
use App\Models\TodoList;
use App\Models\TodoListMember;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserNotification;
use App\Models\UserProfile;
use App\Utils\CloudinaryService;
use App\Utils\Constants;
use App\Utils\GenericServiceResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected GenericServiceResponse $response;

    public function __construct()
    {
        $this->response = new GenericServiceResponse(false, ServiceResponseMessage::ERROR_OCCURRED);
    }

    /**
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function handleUserRegistration(array $validated): GenericServiceResponse
    {
        try {
            /** @var User $user */
            $user = User::repo()->createModel([
                "name" => $validated["name"],
                "username" => $validated["username"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
            ]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::REGISTRATION_SUCCESSFUL;
            $this->response->data = OauthAccessToken::createAccessToken($user);
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function handleUserLogin(array $validated): GenericServiceResponse
    {
        try {
            /** @var User $user */
            $user = User::repo()->findSingleByWhereClause(['email' => $validated['email']]);
            if(!$user) {
                $this->response->message = ServiceResponseMessage::ACCOUNT_DOEST_NO_EXIST;
                return $this->response;
            };
            if(!Auth::guard('user')->attempt(['email'=> $user->email, 'password'=> $validated['password']])) {
                $this->response->message = ServiceResponseMessage::INVALID_CREDENTIALS;
                return $this->response;
            }
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::REGISTRATION_SUCCESSFUL;
            $this->response->data = OauthAccessToken::createAccessToken($user);
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param Request $request
     * @return GenericServiceResponse
     */
    public function handleSearchUserByUsername(User|Authenticatable $user, Request $request): GenericServiceResponse
    {
        try {
            $search = is_null($request->get('query')) ? ' ' : $request->get('query', ' ');
            $users = User::repo()->querySearch($search, ['username', 'name']);
            if($users->count() == 0) {
                $this->response->message = ServiceResponseMessage::CAN_NOT_RETRIEVE_RECORD;
                return $this->response;
            }
            $users->where('id', '!=', $user->id);
            $users = $users->paginate($request->get('limit', 5));
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::RETRIEVED_DATA_SUCCESSFULLY;
            $this->response->data = $users;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $user_id
     * @param int $todo_list
     * @return GenericServiceResponse
     */
    public function handleAddUserToATodoList(User|Authenticatable $user, int $user_id, int $todo_list_id): GenericServiceResponse
    {
        try {
            $check_user = User::repo()->findSingleByWhereClause(['id' => $user_id]);
            if(!$check_user) {
                $this->response->message = ServiceResponseMessage::USER_DOES_NOT_EXIST;
                return $this->response;
            }
            $todo = TodoList::repo()->findSingleByWhereClause(['id' => $todo_list_id]);
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::TODO_DOES_NOT_EXIST;
                return $this->response;
            }
            if($todo->creator_id !== $user->id) {
                $this->response->message = ServiceResponseMessage::CAN_NOT_ADD_MEMBER_TO_TODO_LIST;
                return $this->response;
            }
            if($todo->creator_id === $user_id) {
                $this->response->message = ServiceResponseMessage::CAN_NOT_ADD_SELF;
                return $this->response;
            }
            $data = TodoListMember::repo()->createModel(['user_id' => $user_id, 'todo_list_id' => $todo_list_id]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::CREATE_ACTION_WAS_SUCCESSFULLY;
            $this->response->data = $data;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }
}
