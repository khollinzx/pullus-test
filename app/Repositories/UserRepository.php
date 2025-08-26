<?php

namespace App\Repositories;

use App\Abstractions\AbstractClasses\BaseRepositoryAbstract;
use App\Models\User;

class UserRepository extends BaseRepositoryAbstract
{

    /**
     * @var string
     */
    protected string $databaseTableName = 'users';
    public string $name = 'users';

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model, $this->databaseTableName);
    }
}
