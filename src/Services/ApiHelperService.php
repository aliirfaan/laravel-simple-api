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
     * @return ApiResponseCollection
     */
    public function apiErrorResponse($errors, $namespace, $errorName = 'VALIDATION_ERROR', $errorMessage = 'Invalid data provided')
    {
        $data = $this->responseArrayFormat;
        $data['errors'][] = array(
            'name' => $errorName,
            'debug_id' => $this->generateDebugId($namespace),
            'message' => $errorMessage,
            'details' => array(
                 $errors,
            ),
        );

        $data['status_code'] = Response::HTTP_BAD_REQUEST;
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
        return $namespace . '-' . date('Ymdhis') . '-' .Str::random();
    }
}