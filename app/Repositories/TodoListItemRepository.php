<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\TodoListItem;

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
