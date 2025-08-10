<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserRequest extends BaseFormRequest
{
    public function __construct(protected Controller $controller)
    {
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if(in_array(basename($this->url()), ['update-profile', 'change-password'])) {
            # validate header
            if(!$this->hasHeader('Content-Type') || $this->header('Content-Type') !== 'application/json')
                throw new HttpResponseException(JsonResponseAPI::errorResponse( 'Include Content-Type and set the value to: application/json in your header.', ResponseAlias::HTTP_BAD_REQUEST));
        }
        switch (basename($this->url())) {
            case "update-profile"       : return $this->validateUpdate();
            case "change-password" : return $this->changePassword();
            case "add-user-kyc-address" : return $this->validateKYCAddress();
            case "add-user-kyc-identity-verification" : return $this->validateKYCIdentity();
            case "add-bank-account" : return $this->addBankAccounts();
        }
    }

    /**
     * @return string[]
     */
    private function validateUpdate(): array
    {
        return [
            'phone' => "required|numeric|unique:users,phone_number,{$this->controller->getUserId()},id",
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
        ];
    }

    /**
     * @return string[]
     */
    private function changePassword(): array
    {
        return [
            'old_password' => "required|string|min:6",
            'new_password' => [
                "required",
                "string",
                function ($attribute, $value, $fail) {
                    if($value === $this->old_password) return $fail('The new password should not be the same as the previous password.');
                }
            ],
            'confirmed_password' => "required|string|same:new_password|min:6"
        ];
    }

    /**
     * @return string[]
     */
    private function validateKYCAddress(): array
    {
        return [
            'gender_id' => "required|integer|exists:genders,id",
            'state_id' => "required|integer|exists:states,id",
            'city' => "required|string",
            'house_number' => "required|numeric",
            'street' => "required|string",
            'zip_code' => "required|string",
        ];
    }

    /**
     * @return string[]
     */
    private function validateKYCIdentity(): array
    {
        return [
            'selfie' => "required|mimes:jpeg,png,jpg|max:2048",
            'identity_number' => "required|string",
            'bvn' => "required|string|min:11",
            'identity_type_id' => "required|integer|exists:identity_types,id",
        ];
    }

    /**
     * @return string[]
     */
    private function addBankAccounts(): array
    {
        return [
            'bank_account_number' => "required|numeric",
            'bank_code' => "required|string",
        ];
    }

}
