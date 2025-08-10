<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Models\User;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OnBoardRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if(in_array(basename($this->url()), ['login', 'register', 'set-password'])) {
            # validate header
            if(!$this->hasHeader('Content-Type') || $this->header('Content-Type') !== 'application/json')
                throw new HttpResponseException(JsonResponseAPI::errorResponse( 'Include Content-Type and set the value to: application/json in your header.', ResponseAlias::HTTP_BAD_REQUEST));
        }
        switch (basename($this->url())) {
            case "login"       : return $this->validateLogin();
            case "register"       : return $this->validateSignup();
        }
    }

    /**
     * @return string[]
     */
    private function validateLogin(): array
    {
        return [
            'email' => "required|email|exists:users,email",
            'password' => "required|string"
        ];
    }

    /**
     * @return string[]
     */
    private function validateSignup(): array
    {
        return [
            'email' => "required|email|unique:users,email",
            'password' => "required|string|min:6",
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
        ];
    }
}
