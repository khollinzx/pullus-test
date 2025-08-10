<?php

namespace App\Traits;


use App\Abstractions\AbstractClasses\DTOAbstract;
use App\Utils\GenericServiceResponse;

trait DTOTrait
{
    /**
     *
     * @param \stdClass $data
     * @param object $dtoInstance
     * @return object
     */
    public static function transformJsonToObject(\stdClass $data, object $dtoInstance): object
    {
        # convert to an associative array
        $content = json_decode(json_encode($data), true);
        foreach($content as $key => $value) {
            if (property_exists($dtoInstance, $key)) {
                $dtoInstance->$key = $value;
            }
        }

        return $dtoInstance;
    }

    /**
     * @param array|null $values
     * @return DTOAbstract|DTOTrait|GenericServiceResponse|null
     */
    public static function transformArrayOfJsonToArrayOfObject(?array $values = null): ?self
    {
        $dto = null;
        if (! is_null($values)) {
            $dto = new self();
            foreach ($values as $key => $value) {
                if (property_exists($dto, $key)) {
                    $dto->$key = $value;
                }
            }
        }
        return $dto;
    }

//    public static function transform(): array
//    {
//
//    }
}
