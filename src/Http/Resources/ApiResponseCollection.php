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
            'success' => (\array_key_exists('success', $items)) ? $items['success'] : false,
            'result' => (\array_key_exists('result', $items)) ? $items['result'] : null,
            'errors' => (\array_key_exists('errors', $items)) ? $items['errors'] : null,
            'links' => (\array_key_exists('links', $items)) ? $items['links'] : null,
            'message' => (\array_key_exists('message', $items)) ? $items['message'] : null,
            'extra' => (\array_key_exists('extra', $items)) ? $items['extra'] : null,
        ];
    }
}
