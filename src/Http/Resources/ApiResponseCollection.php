<?php

namespace aliirfaan\LaravelSimpleApi\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * ApiResponseCollection
 * 
 * A format for API responses
 * success: bool | whether response is success or failure
 * result: response main body
 * errors: response errors
 * links: HATEOS links
 * message: response diaply message
 * extra: any extra data
 */
class ApiResponseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $items = $this->collection->all();
        return [
            'success' => $items['success'] ?? false,
            'result' => $items['result'] ?? null,
            'errors' => $items['errors'] ?? null,
            'links' => $items['links'] ?? null,
            'message' => $items['message'] ?? null,
            'extra' => $items['extra'] ?? null
        ];
    }
}
