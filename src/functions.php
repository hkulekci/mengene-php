<?php
if (!function_exists('curl_file_create')) {
    /**
     * Create a CURLFile object
     *
     * @param string $filename
     * @param string $mimetype
     * @param string $postname
     * @return string
     */
    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
            . ($postname ? "" : basename($filename))
            . ($mimetype ? ";type=$mimetype" : "");
    }
}

if (!function_exists('json_last_error_msg')) {
    /**
     * @return string
     */
    function json_last_error_msg() {
        $errors = [
            JSON_ERROR_NONE => 'No error has occurred',
            JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX => 'Syntax error',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        ];

        $errorCode = json_last_error();

        return isset($errors[$errorCode]) ? $errors[$errorCode] : 'Unknown JSON error';
    }
}