<?php

namespace App\Services;

use App\Enums\ServiceResponseMessage;
use App\Events\TodoItemNotification;
use App\Models\TodoList;
use App\Models\TodoListItem;
use App\Models\TodoListMember;
use App\Models\User;
use App\Utils\GenericServiceResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class TodoListItemService
{
    protected GenericServiceResponse $response;

    public function __construct()
    {
        $this->response = new GenericServiceResponse(false, ServiceResponseMessage::ERROR_OCCURRED);
    }

    /**
     * @param User|Authenticatable $user
     * @param int $todo_id
     * @return GenericServiceResponse
     */
    public function listTodoListItems(User|Authenticatable $user, int $todo_id): GenericServiceResponse
    {
        try {
            $items = TodoListItem::repo()->queryRecordByAttributes(['todo_list_id' => $todo_id]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::RETRIEVED_DATA_SUCCESSFULLY;
            $this->response->data = $items;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param int $item_id
     * @return GenericServiceResponse
     */
    public function viewTodoList(int $item_id): GenericServiceResponse
    {
        try {
            $todo = TodoListItem::repo()->findSingleByWhereClause(['id' => $item_id]);
            if(!$todo) {
                $this->response->message = ServiceResponseMessage::TODO_ITEM_DOES_NOT_EXIST;
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
     * @param int $todo_id
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function createTodo(User|Authenticatable $user, int $todo_id, array $validated): GenericServiceResponse
    {
        try {
            $member = TodoListMember::repo()->findSingleByWhereClause(['todo_list_id' => $todo_id, 'user_id' => $user->id]);
            if(!$member) {
                $this->response->message = ServiceResponseMessage::NOT_PERMITTED;
                return $this->response;
            }
            /** @var TodoListItem  $data */
            $data = TodoListItem::repo()->createModel([
                'todo_list_id' => $todo_id,
                'user_id' => $user->id,
                'note' => $validated['note'],
            ]);
            broadcast(new TodoItemNotification($data));
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::CREATE_ACTION_WAS_SUCCESSFULLY;
            $this->response->data = $data;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $item_id
     * @param array $validated
     * @return GenericServiceResponse
     */
    public function updateTodo(User|Authenticatable $user, int $item_id, array $validated): GenericServiceResponse
    {
        try {
            $todo_item = TodoListItem::repo()->findSingleByWhereClause(['id' => $item_id]);
            if(!$todo_item) {
                $this->response->message = ServiceResponseMessage::TODO_ITEM_DOES_NOT_EXIST;
                return $this->response;
            }
            $member = TodoListMember::repo()->findSingleByWhereClause(['todo_list_id' => $todo_item->todo_list_id, 'user_id' => $user->id]);
            if(!$member) {
                $this->response->message = ServiceResponseMessage::NOT_PERMITTED;
                return $this->response;
            }
            $data = TodoListItem::repo()->updateByIdAndGetBackRecord( $todo_item->id,[
                'note' => $validated['note'],
            ]);
            $this->response->status = true;
            $this->response->message = ServiceResponseMessage::UPDATE_ACTION_WAS_SUCCESSFULLY;
            $this->response->data = $data;
            return $this->response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->response;
        }
    }

    /**
     * @param User|Authenticatable $user
     * @param int $item_id
     * @return GenericServiceResponse
     */
    public function deleteTodo(User|Authenticatable $user, int $item_id): GenericServiceResponse
    {
        try {
            /** @var TodoListItem $todo_item */
            $todo_item = TodoListItem::repo()->findSingleByWhereClause(['id' => $item_id]);
            if(!$todo_item) {
                $this->response->message = ServiceResponseMessage::DOES_NOT_EXIST;
                return $this->response;
            }
            if($todo_item->user_id !== $user->id) {
                $this->response->message = ServiceResponseMessage::NOT_GRANTED;
                return $this->response;
            }
            $todo = TodoList::repo()->deleteById($todo_item->id);
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
