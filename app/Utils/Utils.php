<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;

class  Utils
{

    /**
     * This saves a model records
     *
     * @param Model $model
     * @param array $records
     * @param bool $returnModel
     * @return Model|void
     */
    public static function saveModelRecord(Model $model, array $records = [], bool $returnModel = true)
    {
        if (count($records)) {
            foreach ($records as $k => $v)
                $model->$k = $v;

            $model->save();
        }

        if($returnModel) return $model;
    }

    /**
     * @param Model $model
     * @param Model $polymorphicModel
     * @param string $polymorphicMethod
     * @param array $records
     * @return Model
     */
    public static function savePolymorphicRecord(Model $model, Model $polymorphicModel, string $polymorphicMethod, array $records): Model
    {
        if (count($records)) {
            foreach ($records as $k => $v) {
                $model->$k = $v;
            }
            $polymorphicModel->$polymorphicMethod()->save($model);
        }
        return $model;
    }

    /**
     *
     * @param string $subject
     * @param string $search
     * @param string $replace
     * @return string expected
     * expected
     */
    public static function searchAndReplace(string $subject, string $search, string $replace): string
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     *
     * @param string $pascalString
     * @return string
     */
    public static function convertPascalCaseToSnakeCase(string $pascalString): string
    {
        $pascalString = strtolower($pascalString);
        return str_replace(' ', '_', preg_replace('/(?<!^)[A-Z]/', '_$0', $pascalString));
    }

    /**
     *
     * @param array $pascalKeyValues
     * @return array
     */
    public static function convertArrayOfPascalCaseKeysToSnakeCase(array $pascalKeyValues): array
    {
        $response = [];
        foreach ($pascalKeyValues as $key => $value) {
            $response[self::convertPascalCaseToSnakeCase($key)] = $value;
        }
        return $response;
    }

    /**
     *
     * @param string $snakeString
     * @return string
     */
    public static function convertSnakeCaseToPascalCase(string $snakeString): string
    {
        $snakeString = strtolower($snakeString);
        return lcfirst(str_replace('_', '', ucwords($snakeString, '_')));
    }

    /**
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function getRecordUsingWhereArrays($query, array $filters): mixed
    {
        #check that the $key exists in the array of $acceptedFilters
        foreach ($filters as $column => $value)
            $query->where($column, '=', $value);

        return $query;
    }

    /**
     * @param $query
     * @param array $filters
     * @param array $acceptedFilters
     * @return mixed
     */
    public static function returnFilteredSearchedKeys($query, array $filters, array $acceptedFilters): mixed
    {
        #check that the $key exists in the array of $acceptedFilters
        foreach ($filters as $key => $value)
            if (in_array($key, $acceptedFilters) && $value)
                $query->where($key, 'LIKE', "%$value%");

        return $query;
    }

    /**
     * @param $query
     * @param array $filters
     * @param array $acceptedFilters
     * @return mixed
     */
    public static function returnFilteredSearchedAndRelationshipWithLike($query, array $filters, array $acceptedFilters): mixed
    {
        #check that the $key exists in the array of $acceptedFilters
        foreach ($filters as $key => $value) {
            $key = (new Utils())::searchAndReplace($key, '-', '.');
            if (in_array($key, $acceptedFilters) && $value)
                if (str_contains($key, '.')) {
                    // If the key contains a dot (.), it indicates a relation
                    $variables = explode('.', $key);
                    $relation = $variables[0];
                    $attribute = $variables[1];

                    $query->whereHas($relation, function ($query) use ($attribute, $value) {
                        $query->where($attribute, 'LIKE', "%$value%");
                    });
                } else {
                    $query->where($key, 'LIKE', "%$value%");
                }
        }
        return $query;
    }
}

