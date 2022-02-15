<?php

namespace aliirfaan\LaravelSimpleApi\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use aliirfaan\LaravelSimpleApi\Http\Resources\ApiResponseCollection;

class ApiHelperService
{    
    /**
     * responseArrayFormat
     *
     * @var $responseArrayFormat An array format that maps to API response format
     */
    public $responseArrayFormat = array (
        'success' => false,
        'result' => null,
        'errors' => null,
        'status_code' => null,
        'links' => null,
        'message' => null,
        'extra' => null
    );
    
    /**
     * validateRequestFields
     *
     * validates request fields against validation rules
     * Output the errors in a specific format inspired by https://github.com/paypal/api-standards/blob/master/api-style-guide.md#error-handling
     * 
     * @param  array $fieldsArray fields submitted
     * @param  mixed $validationRules fields validation rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return null | array of errors
     */
    public function validateRequestFields($fieldsArray, array $validationRules, array $messages = [], array $customAttributes = [])
    {
        $errorDetails = null;
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
                $anErrorDetail = $this->constructErrorDetails($issues, $validationErrorKey, $submittedValue);
                $errorDetails[] = $anErrorDetail;
            }
        }

        return $errorDetails;
    }
    
    /**
     * apiErrorResponse
     * 
     * returns a formatted api response for errors 
     *
     * @param  array $errors
     * @param  string $namespace namespace to better log error. Example wallet, user, account
     * @param  string $errorName name of the error
     * @param  string $errorMessage user error message
     * @param  string $statusCode HTTP status code
     * @return ApiResponseCollection
     */
    public function apiErrorResponse($errors, $namespace, $errorName = 'VALIDATION_ERROR', $errorMessage = 'Invalid data provided', $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $data = $this->responseArrayFormat;
        $data['errors'][] = array(
            'name' => $errorName,
            'debug_id' => $this->generateDebugId($namespace),
            'message' => $errorMessage,
            'details' => $errors
        );

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
     * @param  array $errors
     * @param  string $namespace
     * @param  string $errorMessage
     * @return ApiResponseCollection
     */
    public function apiValidationErrorResponse($errors, $namespace, $errorMessage = null)
    {
        $errorName = 'VALIDATION_ERROR';
        if (is_null($errorMessage)) {
            $errorMessage = 'Invalid data provided.';
        }
        $statusCode = Response::HTTP_BAD_REQUEST;

        return $this->apiErrorResponse($errors, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiDatabaseErrorResponse
     *
     * Convenience function to return response in case of database error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  string $errorMessage
     * @return ApiResponseCollection
     */
    public function apiDatabaseErrorResponse($namespace, $errorMessage = null)
    {
        $errorName = 'DATABASE_ERROR';
        if (is_null($errorMessage)) {
            $errorMessage = 'Data store could not complete operation.';
        }
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $issue[] = $errorMessage;
        $error[] = $this->constructErrorDetails($issue);

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiUnknownErrorResponse
     *
     * Convenience function to return response in case of unknown error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  string $errorMessage
     * @return ApiResponseCollection
     */
    public function apiUnknownErrorResponse($namespace, $errorMessage = null)
    {
        $errorName = 'UNKNOWN_ERROR';
        if (is_null($errorMessage)) {
            $errorMessage = 'Processing could not be completed due to an error.';
        }
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $issue[] = $errorMessage;
        $error[] = $this->constructErrorDetails($issue);

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiAuthenticationErrorResponse
     *
     * Convenience function to return response in case of authentication error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  string $errorMessage
     * @return ApiResponseCollection
     */
    public function apiAuthenticationErrorResponse($namespace, $errorMessage = null)
    {
        $errorName = 'AUTHENTICATION_ERROR';
        if (is_null($errorMessage)) {
            $errorMessage = 'Could not validate against authentication service.';
        }
        $statusCode = Response::HTTP_UNAUTHORIZED;

        $issue[] = $errorMessage;
        $error[] = $this->constructErrorDetails($issue);

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiAuthorizationErrorResponse
     *
     * Convenience function to return response in case of authorization error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  string $errorMessage
     * @return ApiResponseCollection
     */
    public function apiAuthorizationErrorResponse($namespace, $errorMessage = null)
    {
        $errorName = 'AUTHORIZATION_ERROR';
        if (is_null($errorMessage)) {
            $errorMessage = 'You are not authorized to do this operation.';
        }
        $statusCode = Response::HTTP_FORBIDDEN;

        $issue[] = $errorMessage;
        $error[] = $this->constructErrorDetails($issue);

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiNotFoundErrorResponse
     *
     * Convenience function to return response in case of authorization error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @param  string $errorMessage
     * @return ApiResponseCollection
     */
    public function apiNotFoundErrorResponse($namespace, $errorMessage = null)
    {
        $errorName = 'OBJECT_NOT_FOUND_ERROR';
        if (is_null($errorMessage)) {
            $errorMessage = 'The record was nto found.';
        }
        $statusCode = Response::HTTP_NOT_FOUND;

        $issue[] = $errorMessage;
        $error[] = $this->constructErrorDetails($issue);

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * constructErrorDetails
     *
     * @param  array $issues An array of messages for the error
     * @param  string $field The name of the field
     * @param  mixed $value The value of the field
     * @param  array $links Link to information about error
     * @return array
     */
    public function constructErrorDetails($issues, $field = null, $value = null, $links = null)
    {
        return [
            'field' => $field,
            'value' => $value,
            'issue' => $issues,
            'links' => $links
        ];
    }
}