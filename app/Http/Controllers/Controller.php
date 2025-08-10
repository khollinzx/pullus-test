<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return string
     */
    public function welcome(): string
    {
        return "Welcome to Pullus Backend Server ".env("APP_ENV")." API Version 1";
    }

    /**
     * @return mixed
     */
    public function getUserId(): int
    {
        return auth()->user()->getAuthIdentifier();
    }

    /**
     * @return Authenticatable|User|null
     */
    public function getUser(): Authenticatable|User|null
    {
        return auth()->user();
    }
}
