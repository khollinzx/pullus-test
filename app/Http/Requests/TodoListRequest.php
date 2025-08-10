<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Utils\JsonResponseAPI;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TodoListRequest extends BaseFormRequest
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
        if (in_array($this->getMethod(), ['POST', 'PATCH', 'PUT'])) {
            # validate header
            if(!$this->hasHeader('Content-Type') || $this->header('Content-Type') !== 'application/json')
                throw new HttpResponseException(JsonResponseAPI::errorResponse( 'Include Content-Type and set the value to: application/json in your header.', ResponseAlias::HTTP_BAD_REQUEST));
        }
        switch ($this->getMethod()) {
            case "PATCH":
            case "PUT":
            case "POST": return $this->validateCreate();
        }
    }

    /**
     * @return string[]
     */
    private function validateCreate(): array
    {
        return [
            'title' => 'required|string',
        ];
    }
}
