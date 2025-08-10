<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\TodoList;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Collection;

class TodoListRepository extends BaseRepositoryAbstract
{

    /**
     * @var string
     */
    protected string $databaseTableName = 'todo_lists';
    public string $name = 'todo_lists';

    /**
     * @param TodoList $model
     */
    public function __construct(TodoList $model)
    {
        parent::__construct($model, $this->databaseTableName);
    }
}
