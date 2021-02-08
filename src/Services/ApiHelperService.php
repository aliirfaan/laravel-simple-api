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
     * @return null | array of errors
     */
    public function validateRequestFields($fieldsArray, $validationRules)
    {
        $errorDetails = null;
        $validator = Validator::make($fieldsArray, $validationRules);

        if ($validator->fails()) {
            $validationErrors = $validator->errors()->toArray();
            foreach ($validationErrors as $validationErrorKey => $validationErrorMessage) {
                // see if we have a submitted value and include it in the response
                $submittedValue = null;
                if (isset($fieldsArray[$validationErrorKey])) {
                    $submittedValue = $fieldsArray[$validationErrorKey];
                }
        
                $anErrorDetail = array(
                    'field' => $validationErrorKey,
                    'value' => $submittedValue,
                );
        
                foreach ($validationErrorMessage as $anErrorMessage) {
                    $anErrorDetail['issue'][] = $anErrorMessage;
                }

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
     * @return ApiResponseCollection
     */
    public function apiValidationErrorResponse($errors, $namespace)
    {
        $errorName = 'VALIDATION_ERROR';
        $errorMessage = 'Invalid data provided.';
        $statusCode = Response::HTTP_BAD_REQUEST;

        return $this->apiErrorResponse($errors, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiDatabaseErrorResponse
     *
     * Convenience function to return response in case of validation error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @return ApiResponseCollection
     */
    public function apiDatabaseErrorResponse($namespace)
    {
        $errorName = 'DATABASE_ERROR';
        $errorMessage = 'Data store could not complete operation.';
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $issue[] = $errorMessage;
        $error['issue'] = $issue;

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiUnknownErrorResponse
     *
     * Convenience function to return response in case of validation error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @return ApiResponseCollection
     */
    public function apiUnknownErrorResponse($namespace)
    {
        $errorName = 'UNKNOWN_ERROR';
        $errorMessage = 'Processing could not be completed due to an error.';
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $issue[] = $errorMessage;
        $error['issue'] = $issue;

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiAuthenticationErrorResponse
     *
     * Convenience function to return response in case of validation error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @return ApiResponseCollection
     */
    public function apiAuthenticationErrorResponse($namespace)
    {
        $errorName = 'AUTHENTICATION_ERROR';
        $errorMessage = 'Could not validate against authentication service.';
        $statusCode = Response::HTTP_UNAUTHORIZED;
        $issue[] = $errorMessage;
        $error['issue'] = $issue;

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }

    /**
     * apiAuthorizationErrorResponse
     *
     * Convenience function to return response in case of validation error, prefilled with errorName and errorMessage
     * Calls $this->apiErrorResponse() with meaningful defaults
     * 
     * @param  string $namespace
     * @return ApiResponseCollection
     */
    public function apiAuthorizationErrorResponse($namespace)
    {
        $errorName = 'AUTHORIZATION_ERROR';
        $errorMessage = 'You are not authorized to do this operation.';
        $statusCode = Response::HTTP_UNAUTHORIZED;
        $issue[] = $errorMessage;
        $error['issue'] = $issue;

        return $this->apiErrorResponse($error, $namespace, $errorName, $errorMessage, $statusCode);
    }
}