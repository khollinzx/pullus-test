<?php

namespace App\Models;

use App\Traits\HasRepositoryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TodoList extends Model
{
    use HasFactory, HasRepositoryTrait;

    public array $relationships = [
        'user',
        'items.user',
        'invites.user',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TodoListItem::class, 'todo_list_id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(TodoListMember::class, 'todo_list_id');
    }
}
