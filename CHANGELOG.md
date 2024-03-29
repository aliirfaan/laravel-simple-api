# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com) and this project adheres to [Semantic Versioning](https://semver.org).

## 7.0.0 - 2023-06-10

### Added

- Nothing

### Changed

- all convenience functions have an additional message parameter that take precedence on message translation

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- README.md
## 6.1.1 - 2023-06-07

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- README.md

## 6.1.0 - 2023-06-07

### Added

- Nothing

### Changed

- output empty error details for better object mapping. Even if error details is empty, a generic object is returned. {"field": null, "value": null, "issues": null, "links": null}

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
## 6.0.0 - 2023-06-07

### Added

- Nothing

### Changed

- validateRequestFields()
- all convenience functions can now show can show many error details instead of single detail

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 5.1.0 - 2023-06-06

### Added

- apiTooManyRequestsErrorResponse() convenience function for too many requests errors
- resources\lang\en\error_catalogue\messages.php - too many requests key

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 5.0.1 - 2023-05-02

### Added

- Nothing

### Changed

- apiDatabaseErrorResponse(), apiUnknownErrorResponse(), apiAuthenticationErrorResponse(), apiAuthorizationErrorResponse(), apiNotFoundErrorResponse() - change message key
- resources\lang\en\error_catalogue\messages.php - change unknown_error key text

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 5.0.0 - 2023-04-20

### Added

- php >=7.0.0 constraint
- localization
- constructErrorDetail()
- apiProcessingErrorResponse()

### Changed

- ApiResponseCollection use null coalescing operator
- SimpleApiServiceProvider load translation in provider
- validateRequestFields(), apiErrorResponse(), apiValidationErrorResponse(), apiDatabaseErrorResponse(), apiUnknownErrorResponse(), apiAuthenticationErrorResponse(), apiAuthorizationErrorResponse(), apiNotFoundErrorResponse()

### Deprecated

- Nothing

### Removed

- constructErrorDetails()

### Fixed

- Nothing

## 4.1.0 - 2022-11-22

### Added

- apiNotFoundErrorResponse()

### Changed

- apiValidationErrorResponse() - new parameter to change error message
- apiDatabaseErrorResponse() - new parameter to change error message
- apiUnknownErrorResponse() - new parameter to change error message
- apiAuthenticationErrorResponse() - new parameter to change error message
- apiAuthorizationErrorResponse() - new parameter to change error message

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- Nothing

## 4.0.0 - 2021-11-03

### Added

- Nothing

### Changed

- class ApiHelperService function validateRequestFields()
- add two parameters: $messages, $customAttributes 

## 3.1.0 - 2021-07-30

### Added

- class HypermediaRelation to generate HATEAOS links

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- Nothing

## 3.0.0 - 2021-07-28

### Added

- function constructErrorDetails($issues, $field = null, $value = null, $links = null)

### Changed

- review all function and use constructErrorDetails

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- Nothing

## 2.2.0 - 2021-02-08

### Added

- Nothing

### Changed

- HTTP status code 403 when unauthorized, 401 when not authenticated
- PHP doc text

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- Nothing

## 2.1.0 - 2021-02-08

### Added

- convenience function for common api errors with sensible defaults: validation, database, unknown exception, authentication, authorization

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- Nothing

## 2.0.1 - 2021-02-04

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- composer refresh

## 2.0.0 - 2021-02-04

### Added

- Nothing

### Changed

- review error response format in apiErrorResponse function

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- nothing

## 1.3.0 - 2021-02-04

### Added

- status code as parameter in api response function

### Changed

- api response function

### Deprecated

- Nothing

### Removed

- nothing

### Fixed

- nothing

## 1.2.0 - 2021-02-04

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- date time in debug id generation as it would be logged anyway with current data time in logging system

### Fixed

- nothing

## 1.1.0 - 2021-02-04

### Added

- 'extra' array key in response format

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- nothing