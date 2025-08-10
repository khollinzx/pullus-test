<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\TodoList;
use App\Models\TodoListItem;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Collection;

class TodoListItemRepository extends BaseRepositoryAbstract
{

    /**
     * @var string
     */
    protected string $databaseTableName = 'todo_list_items';
    public string $name = 'todo_list_items';

    /**
     * @param TodoListItem $model
     */
    public function __construct(TodoListItem $model)
    {
        parent::__construct($model, $this->databaseTableName);
    }
}
