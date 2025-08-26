<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\TodoList;

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
