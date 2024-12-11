<?php

namespace App\Classes\Enums;

enum ZohoCRMCommonErrorsEnum: string
{
    // 200 Series
    case OK = '200: The API request is successful.';
    case CREATED = '201: Request fulfilled for record insertion.';
    case ACCEPTED = '202: The request has been accepted.';
    case NO_CONTENT = '204: There is no content available for the request.';
    case MULTI_STATUS = '207: Partially successful request.';

    // 300 Series
    case NOT_MODIFIED = '304: The requested resource has not been modified.';

    // 400 Series
    case BAD_REQUEST = '400: The request or the authentication considered is invalid.';
    case INVALID_DATA = '400: The ID given is invalid.';
    case AUTHORIZATION_ERROR = '401: Invalid API key provided.';
    case FORBIDDEN = '403: No permission to do the operation.';
    case NOT_FOUND = '404: Invalid request.';
    case METHOD_NOT_ALLOWED = '405: The specified method is not allowed.';
    case REQUEST_ENTITY_TOO_LARGE = '413: File size exceeded the limit.';
    case UNSUPPORTED_MEDIA_TYPE = '415: The media/file type is not supported.';
    case TOO_MANY_REQUESTS = '429: Request limit exceeded for 24 hours.';

    // 500 Series
    case INTERNAL_SERVER_ERROR = '500: Generic server error.';
}
