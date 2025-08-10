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
use App\Models\TodoListItem;
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

class TodoListService
{
    protected GenericServiceResponse $response;

    public function __construct()
    {
        $this->response = new GenericServiceResponse(false, ServiceResponseMessage::ERROR_OCCURRED);
    }

    /**
     * @param User|Authenticatable $user
     * @param Request $request
     * @return GenericServiceResponse
     */
    public function listTodo(User|Authenticatable $user, Request $request): GenericServiceResponse
    {
        try {
            $my_todos = TodoList::with((new TodoList())->relationships)
                ->whereHas('invites', function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', '=', $user->id);
                    });
                })->get();
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::RETRIEVED_DATA_SUCCESSFULLY;
            $this->response->data = $my_todos;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param int $todo_id
     * @return GenericServiceResponse
     */
    public function viewTodo(int $todo_id): GenericServiceResponse
    {
        try {
            $todo = TodoList::repo()->findSingleByWhereClause(['id' => $todo_id]);
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::TODO_DOES_NOT_EXIST;
                return $this->response;
            }
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::RETRIEVED_DATA_SUCCESSFULLY;
            $this->response->data = $todo;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function createTodo(User|Authenticatable $user, array $validated): GenericServiceResponse
    {
        try {
            /** @var User $user */
            $todo = TodoList::repo()->createModel(["title" => $validated["title"], "creator_id" => $user->id]);
            $member = TodoListMember::repo()->findSingleByWhereClause(['todo_list_id' => $todo->id, 'user_id' => $user->id]);
            if(!$member) {
                TodoListMember::repo()->createModel(['todo_list_id' => $todo->id, 'user_id' => $user->id]);
            }
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::CREATE_ACTION_WAS_SUCCESSFULLY;
            $this->response->data = $todo;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $todo_id
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function updateTodo(User|Authenticatable $user, int $todo_id, array $validated): GenericServiceResponse
    {
        try {
            /** @var TodoList $todo */
            $todo = TodoList::repo()->findSingleByWhereClause(['id' => $todo_id]);
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::DOES_NOT_EXIST;
                return $this->response;
            }
            $todo = TodoList::repo()->updateByIdAndGetBackRecord($todo->id, ["title" => $validated["title"]]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::UPDATE_ACTION_WAS_SUCCESSFULLY;
            $this->response->data = $todo;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $todo_id
     * @return GenericServiceResponse
     */
    public function deleteTodo(User|Authenticatable $user, int $todo_id): GenericServiceResponse
    {
        try {
            /** @var TodoList $todo */
            $todo = TodoList::repo()->findSingleByWhereClause(['id' => $todo_id]);
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::DOES_NOT_EXIST;
                return $this->response;
            }
            if($todo->creator_id !== $user->id) {
                $this->response->message = ServiceResponseMessage::NOT_GRANTED;
                return $this->response;
            }
            $todo = TodoList::repo()->deleteById($todo->id);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::RECORD_DELETE;
            $this->response->data = $todo;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $user_id
     * @param int $todo_list_id
     * @return GenericServiceResponse
     */
    public function addUserToTodoList(User|Authenticatable $user, int $user_id, int $todo_list_id): GenericServiceResponse
    {
        try {
            $user = User::repo()->findSingleByWhereClause(['id' => $user_id]);
            $todo = TodoList::repo()->findSingleByWhereClause(['id' => $todo_list_id]);
            if(!$user) {
                $this->response->message = ServiceResponseMessage::USER_DOES_NOT_EXIST;
                return $this->response;
            }
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::TODO_DOES_NOT_EXIST;
                return $this->response;
            }
            $exist = TodoListMember::repo()->findSingleByWhereClause(['user_id' => $user_id]);
            if($exist) {
                $this->response->message = ServiceResponseMessage::TODO_DOES_NOT_EXIST;
                return $this->response;
            }
            $data = TodoListMember::repo()->createModel(['user_id' => $user_id, 'todo_list_id' => $todo_list_id]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::MEMBER_ADDED_SUCCESSFULLY;
            $this->response->data = $data;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $todo_list_id
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function addItemToATodoList(User|Authenticatable $user, int $todo_list_id, array $validated): GenericServiceResponse
    {
        try {
            $exist = TodoListMember::repo()->findSingleByWhereClause(['user_id' => $user->id, 'todo_list_id' => $todo_list_id]);
            if($exist) {
                $this->response->message = ServiceResponseMessage::DOES_NOT_BELONG;
                return $this->response;
            }
            $todo = TodoList::repo()->findSingleByWhereClause(['id' => $todo_list_id]);
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::TODO_DOES_NOT_EXIST;
                return $this->response;
            }
            $data = TodoListItem::repo()->createModel([
                'user_id' => $user->id,
                'todo_list_id' => $todo_list_id,
                'note' => $validated['note'],
            ]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::MEMBER_ADDED_SUCCESSFULLY;
            $this->response->data = $data;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }
}
