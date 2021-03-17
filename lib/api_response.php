<?php

/**
 * @param array $data
 * @param bool $succes
 * @param int $code
 * @return array
 */
function api_prepare_success_response($data = [], bool $succes = true, int $code = 200) {
    return [
        'code'    => $code,
        'success' => $succes,
        'data'    => $data,
    ];
}

/**
 * @param $data
 * @param int $code
 * @return array
 */
function api_prepare_response($data, int $code = 200) {
    return api_prepare_success_response($data, $code);
}


/**
 * @param Throwable $throwable
 * @param string $errorUserMessage
 * @param array $data
 * @param bool $success
 * @param int $code
 * @return array
 */
function api_prepare_throwable_response(
    Throwable $throwable,
    string $errorUserMessage,
    $data = [],
    bool $success = false,
    int $code = 500
) {
    return [
        'code'    => $code,
        'success' => $success,
        'data'    => $data,
        'error'   => [
            'code'            => $throwable->getCode(),
            'internalMessage' => $throwable->getMessage(),
            'userMessage'     => $errorUserMessage,
        ],
    ];
}

/**
 * @param Throwable $throwable
 * @param string $errorUserMessage
 * @param array $data
 * @param bool $success
 * @param int $code
 * @return array
 */
function api_prepare_exception_response(
    Throwable $throwable,
    string $errorUserMessage,
    $data = [],
    bool $success = false,
    int $code = 500
) {
    return api_prepare_throwable_response($throwable, $errorUserMessage, $data, $success, $code);
}

/**
 * @param string $errorUserMessage
 * @param array $data
 * @param bool $success
 * @param int $code
 * @return array
 */
function api_prepare_error_response(
    string $errorUserMessage,
    $data = [],
    bool $success = false,
    int $code = 500
) {
    return [
        'code'    => $code,
        'success' => $success,
        'data'    => $data,
        'error'   => [
            'userMessage' => $errorUserMessage,
        ],
    ];
}