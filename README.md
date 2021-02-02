# Laravel Simple API

This package offers API response formatting function using Laravel Resources and other API helper functions like API request validation.

## API resource format

It is good practice to have a generic response format. This package uses the following format:
```php
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
```

## Error response format

The error response format used is inspired by [paypal api guide](https://github.com/paypal/api-standards/blob/master/api-style-guide.md#error-handling)

## Features

* Get a generic response format for you API
* Response formatting for API errors
* API request validation

## Requirements

* [Composer](https://getcomposer.org/)
* [Laravel](http://laravel.com/)

## Installation

You can install this package on an existing Laravel project with using composer:

```bash
 $ composer require aliirfaan/laravel-simple-api
```

Register the ServiceProvider by editing **config/app.php** file and adding to providers array:

```php
  aliirfaan\LaravelSimpleApi\SimpleApiServiceProvider::class,
```

Note: use the following for Laravel <5.1 versions:

```php
 'aliirfaan\LaravelSimpleApi\SimpleApiServiceProvider',
```

Publish files with:

```bash
 $ php artisan vendor:publish --provider="aliirfaan\LaravelSimpleApi\SimpleApiServiceProvider"
```

or by using only `php artisan vendor:publish` and select the `aliirfaan\LaravelSimpleApi\SimpleApiServiceProvider` from the outputted list.

## Usage

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use aliirfaan\LaravelSimpleApi\Services\ApiHelperService;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function testApiHelper(Request $request, ApiHelperService $apiHelperService)
    {
        // get your api request fields
        $requestArray = $request->json()->all();

        // get your validatin rules
        $validationRules = User::$createRules;

        //validate api request
        $validationResult = $apiHelperService->validateRequestFields($requestArray, $validationRules);
        if (!is_null($validationResult)) {
            // output error response
            $namespace = 'user';
            $errorName = 'VALIDATION_ERROR';
            $errorMessage = 'Invalid data provided';
            $validationErrorResponse = $apiHelperService->apiErrorResponse($validationResult, $namespace, $errorName, $errorMessage);
            return $validationErrorResponse->response()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        // a general response
        $data = $apiHelperService->responseArrayFormat;
        //dd($data);

        // do your processing
        $data['success'] = true;
        $result = new ApiResponseCollection($data);
        return $result->response()->setStatusCode(Response::HTTP_OK);
    }
}
```

## License

The MIT License (MIT)

Copyright (c) 2020

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.