<?php

namespace aliirfaan\LaravelSimpleApi\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use aliirfaan\LaravelSimpleApi\Http\Resources\ApiResponseCollection;

class ApiHelperService
{    
    /**
     * responseArrayFormat
     *
     * @var $responseArrayFormat An array format that maps to API response format
     */
    public $responseArrayFormat = [
        'success' => false,
        'result' => null,
        'errors' => null,
        'status_code' => null,
        'links' => null,
        'message' => null, // general error message
        'extra' => null
    ];
    
    /**
     * validateRequestFields
     *
     * validates request fields against validation rules
     * Output the errors in a specific format inspired by https://github.com/paypal/api-standards/blob/master/api-style-guide.md#error-handling
     * 
     * @param array $fieldsArray fields submitted
     * @param mixed $validationRules fields validation rules
     * @param array $messages
     * @param array $customAttributes
     * @return null | array of errors
     */
    public function validateRequestFields($fieldsArray, array $validationRules, array $messages = [], array $customAttributes = [])
    {
        $errors = null;
        $validator = Validator::make($fieldsArray, $validationRules, $messages, $customAttributes);

        if ($validator->fails()) {
            $validationErrors = $validator->errors()->toArray();
            foreach ($validationErrors as $validationErrorKey => $validationErrorMessage) {
                // see if we have a submitted value and include it in the response
                $submittedValue = null;
                if (isset($fieldsArray[$validationErrorKey])) {
                    $submittedValue = $fieldsArray[$validationErrorKey];
                }

                $issues = null;
                foreach ($validationErrorMessage as $anErrorMessage) {
                    $issues[] = $anErrorMessage;
                }

                $errorDetail = [
                    'issues' => $issues,
                    'value' => $submittedValue,
                    'field' => $validationErrorKey,
                ];


                $anErrorDetail = $this->constructErrorDetail($errorDetail);
                $errors[] = $anErrorDetail;
            }
        }

        return $errors;
    }
    
    /**
     * apiErrorResponse
     * 
     * returns a formatted api response for errors 
     *
     * @param array $errors an array of errors. See @constructErrorDetail($errorDetail) for error detail format
     * @param string $generalErrorTranslationKey translation key in language file for general error message
     * @param array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @param string $statusCode HTTP status code
     * @return ApiResponseCollection
     */
    public function apiErrorResponse($errors, $generalErrorTranslationKey = null, $generalErrorTranslationParameters = [], $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $data = $this->responseArrayFormat;
        if (is_null($generalErrorTranslationKey)) {
            $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.validation_error';
        }
        $errorMessage = __($generalErrorTranslationKey, $generalErrorTranslationParameters);

        $data['errors'] = $errors;
        $data['status_code'] = $statusCode;
        $data['message'] = $errorMessage;
        
        return new ApiResponseCollection($data);
    }
    
    /**
     * generateDebugId
     * 
     * A unique error identifier generated on the server-side and logged for correlation purposes
     * 
     * @param  string $namespace
     * @return string debug id
     */
    public function generateDebugId($namespace = 'error')
    {
        return $namespace . '-' .Str::random();
    }
    
    /**
     * apiValidationErrorResponse
     *
     * Convenience function to return response in case of validation error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param string $namespace
     * @param array $errorDetails an array of errorDetails. See @constructErrorDetail($errorDetail) for error detail format
     * @param string $errorTranslationKey translation key in language file
     * @param array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param string $generalErrorTranslationKey translation key in language file for general error message
     * @param array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiValidationErrorResponse($namespace, $errorDetails, $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.processing_error', $generalErrorTranslationParameters = [])
    {
        $errorName = 'VALIDATION_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.validation_error';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $errorDetails
        ];

        $statusCode = Response::HTTP_BAD_REQUEST;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    /**
     * apiDatabaseErrorResponse
     *
     * Convenience function to return response in case of database error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  array $errorDetail detail of the error
     * @param  string $errorTranslationKey translation key in language file
     * @param  array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param  string $generalErrorTranslationKey translation key in language file for general error message
     * @param  array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiDatabaseErrorResponse($namespace, $errorDetail = [], $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.processing_error', $generalErrorTranslationParameters = [])
    {
        $errorName = 'DATABASE_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.database_error';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $details[] = $this->constructErrorDetail($errorDetail);
        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $details
        ];

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    /**
     * apiUnknownErrorResponse
     *
     * Convenience function to return response in case of unknown error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  array $errorDetail detail of the error
     * @param  string $errorTranslationKey translation key in language file
     * @param  array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param  string $generalErrorTranslationKey translation key in language file for general error message
     * @param  array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiUnknownErrorResponse($namespace, $errorDetail = [], $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.processing_error', $generalErrorTranslationParameters = [])
    {
        $errorName = 'UNKNOWN_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.unknown_error';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $details[] = $this->constructErrorDetail($errorDetail);
        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $details
        ];

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    /**
     * apiAuthenticationErrorResponse
     *
     * Convenience function to return response in case of authentication error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  array $errorDetail detail of the error
     * @param  string $errorTranslationKey translation key in language file
     * @param  array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param  string $generalErrorTranslationKey translation key in language file for general error message
     * @param  array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiAuthenticationErrorResponse($namespace, $errorDetail = [], $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.authentication_error', $generalErrorTranslationParameters = [])
    {
        $errorName = 'AUTHENTICATION_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.authentication_error';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $details[] = $this->constructErrorDetail($errorDetail);
        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $details
        ];

        $statusCode = Response::HTTP_UNAUTHORIZED;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    /**
     * apiAuthorizationErrorResponse
     *
     * Convenience function to return response in case of authorization error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  array $errorDetail detail of the error
     * @param  string $errorTranslationKey translation key in language file
     * @param  array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param  string $generalErrorTranslationKey translation key in language file for general error message
     * @param  array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiAuthorizationErrorResponse($namespace, $errorDetail = [], $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.authorization_error', $generalErrorTranslationParameters = [])
    {
        $errorName = 'AUTHORIZATION_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.authorization_error';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $details[] = $this->constructErrorDetail($errorDetail);
        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $details
        ];

        $statusCode = Response::HTTP_FORBIDDEN;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    /**
     * apiNotFoundErrorResponse
     *
     * Convenience function to return response in case of authorization error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  array $errorDetail detail of the error
     * @param  string $errorTranslationKey translation key in language file
     * @param  array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param  string $generalErrorTranslationKey translation key in language file for general error message
     * @param  array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiNotFoundErrorResponse($namespace, $errorDetail = [], $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.record_not_found', $generalErrorTranslationParameters = [])
    {
        $errorName = 'OBJECT_NOT_FOUND_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.record_not_found';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $details[] = $this->constructErrorDetail($errorDetail);
        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $details
        ];

        $statusCode = Response::HTTP_NOT_FOUND;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    /**
     * apiProcessingErrorResponse
     *
     * Convenience function to return response in case of authorization error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  array $errorDetail detail of the error
     * @param  string $errorTranslationKey translation key in language file
     * @param  array $errorTranslationParameters translation parameters to be replaced in translation message
     * @param  string $generalErrorTranslationKey translation key in language file for general error message
     * @param  array $generalErrorTranslationParameters translation parameters to be replaced in translation message for general error message
     * @return ApiResponseCollection
     */
    public function apiProcessingErrorResponse($namespace, $errorDetail = [], $errorTranslationKey = null, $errorTranslationParameters = [], $generalErrorTranslationKey = 'laravel-simple-api::error_catalogue/messages.processing_error', $generalErrorTranslationParameters = [])
    {
        $errorName = 'PROCESSING_ERROR';
        if (is_null($errorTranslationKey)) {
            $errorTranslationKey = 'laravel-simple-api::error_catalogue/messages.processing_error';
        }
        $errorMessage = __($errorTranslationKey, $errorTranslationParameters);

        $details[] = $this->constructErrorDetail($errorDetail);
        $errors[] = [
            'name' => $errorName,
            'message' => $errorMessage,
            'debug_id' => $this->generateDebugId($namespace),
            'details' => $details
        ];

        $statusCode = Response::HTTP_BAD_REQUEST;

        return $this->apiErrorResponse($errors, $generalErrorTranslationKey, $generalErrorTranslationParameters, $statusCode);
    }

    public function constructErrorDetail($errorDetail)
    {
        return [
            'field' => $errorDetail['field'] ?? null,
            'value' => $errorDetail['value'] ?? null,
            'issues' => $errorDetail['issues'] ?? null,
            'links' => $errorDetail['links'] ?? null
        ];
    }
}