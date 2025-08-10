<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\TodoList;
use App\Models\TodoListMember;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Collection;

class TodoListMemberRepository extends BaseRepositoryAbstract
{

    /**
     * @var string
     */
    protected string $databaseTableName = 'todo_list_members';
    public string $name = 'todo_list_members';

    /**
     * @param TodoListMember $model
     */
    public function __construct(TodoListMember $model)
    {
        parent::__construct($model, $this->databaseTableName);
    }
}
