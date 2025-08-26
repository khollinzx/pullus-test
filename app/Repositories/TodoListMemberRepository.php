<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\TodoListMember;

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
