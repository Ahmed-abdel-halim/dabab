<?php

namespace App\Traits;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Str;

trait ApiResponseTrait
{
    protected function successResponse($data = null, $message = 'Success', $code = 200)
    {
        // paginated data
        if ($data instanceof AbstractPaginator) {
            $resourceName = $this->resolveResourceName($data->getCollection()->first());
            return response()->json([
                'status'  => true,
                'message' => $message,
                    $resourceName => $data->items(),
                    'pagination'  => [
                        'total'        => $data->total(),
                        'per_page'     => $data->perPage(),
                        'current_page' => $data->currentPage(),
                        'last_page'    => $data->lastPage(),
                        'next_page_url'=> $data->nextPageUrl(),
                        'prev_page_url'=> $data->previousPageUrl(),
                    ]
                
            ], $code);
        }

        // if it's a collection or array
        if (is_iterable($data)) {
            $dataCollection = collect($data);
            $firstItem = $dataCollection->first();

            // إذا كانت مصفوفة بسيطة (associative array) أو مصفوفة من غير الكائنات
            if (!is_object($firstItem)) {
                return response()->json([
                    'status'  => true,
                    'message' => $message,
                    'data'    => $data
                ], $code);
            }

            // إذا كانت مصفوفة من الكائنات (Models)
            $resourceName = $this->resolveResourceName($firstItem);
            return response()->json([
                'status'  => true,
                'message' => $message,
                'data'    => [
                    $resourceName => $data
                ]
            ], $code);
        }

        // if it's a single model or null
        $resourceName = $this->resolveResourceName($data);
        return response()->json([
            'status'  => true,
            'message' => $message,
                $resourceName => $data
            
        ], $code);
    }

    protected function errorResponse($message = 'Error', $code = 400, $errors = [])
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }

    //returns resource name in plural form based on model class
    protected function resolveResourceName($model): string
    {
        if (!$model) {
            return 'items';
        }

        $className = class_basename($model);
        return Str::plural(Str::camel(Str::lower($className))); //  doctor -> doctors
    }
}