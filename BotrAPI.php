<?php

/**
 * This file contains an API client for the Bits on the Run API version 1.4
 *
 * PHP version 5, 7
 *
 * For the System API documentation see:
 * http://developer.longtailvideo.com/botr/system-api/
 *
 * LICENSE:
 *
 * Copyright 2012 LongTail Ad Solutions
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Services
 * @package   BotrAPI
 * @author    Sergey Lashin <support@jwplayer.com>
 * @copyright 2012 LongTail Ad Solutions
 * @license   BSD 3-Clause License
 * @link      https://github.com/silverorange/botr-api
 */

/**
 * PHP client library for Bits on the Run System API
 *
 * For the System API documentation see:
 * http://developer.longtailvideo.com/botr/system-api/
 *
 * @category  Services
 * @package   BotrAPI
 * @author    Sergey Lashin <support@jwplayer.com>
 * @copyright 2012 LongTail Ad Solutions
 * @license   BSD 3-Clause License
 * @link      https://github.com/silverorange/botr-api
 */
class BotrAPI
{
    /**
     * API version to sue
     *
     * @var string
     */
    private $_version = '1.4';

    /**
     * API endpoint
     *
     * @var string
     */
    private $_url = 'http://api.bitsontherun.com/v1';

    /**
     * HTTP library to use
     *
     * Either 'fopen' or 'curl'.
     *
     * @var string
     */
    private $_library;

    /**
     * API key
     *
     * @var string
     */
    private $_key;

    /**
     * API secret
     *
     * @var string
     */
    private $_secret;

    /**
     * Creates a new BotR API client
     *
     * @param string $key    the API key to use.
     * @param string $secret the API secret to use.
     */
    public function __construct($key, $secret)
    {
        $this->_key = $key;
        $this->_secret = $secret;

        // Determine which HTTP library to use:
        // check for cURL, else fall back to file_get_contents
        if (function_exists('curl_init')) {
            $this->_library = 'curl';
        } else {
            $this->_library = 'fopen';
        }
    }

    /**
     * Gets the API version being used
     *
     * @return string the API version being used.
     */
    public function version()
    {
        return $this->_version;
    }

    /**
     * Encodes data using a RFC 3986 complient rawurlencode()
     *
     * This is only required for phpversion() <= 5.2.7RC1
     * See http://www.php.net/manual/en/function.rawurlencode.php#86506
     *
     * @param array|string $input the input data
     *
     * @return array|string the encoded data.
     */
    private function _urlencode($input)
    {
        if (is_array($input)) {
            return array_map(array('_urlencode'), $input);
        } else if (is_scalar($input)) {
            return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($input)));
        } else {
            return '';
        }
    }

    /**
     * Signs API call arguments
     *
     * @param array $args the arguments to sign
     *
     * @return string the signature.
     */
    private function _sign(array $args)
    {
        ksort($args);
        $sbs = "";
        foreach ($args as $key => $value) {
            if ($sbs != "") {
                $sbs .= "&";
            }
            // Construct Signature Base String
            $sbs .= $this->_urlencode($key) . "=" . $this->_urlencode($value);
        }

        // Add shared secret to the Signature Base String and generate the signature
        $signature = sha1($sbs . $this->_secret);

        return $signature;
    }

    /**
     * Adds required api_* arguments
     *
     * @param array $args the existing arguments.
     *
     * @return updated arguments array containing all required arguments.
     */
    private function _args(array $args)
    {
        $args['api_nonce'] = str_pad(mt_rand(0, 99999999), 8, STR_PAD_LEFT);
        $args['api_timestamp'] = time();

        $args['api_key'] = $this->_key;

        if (!array_key_exists('api_format', $args)) {
            // Use the serialised PHP format,
            // otherwise use format specified in the call() args.
            $args['api_format'] = 'php';
        }

        // Add API kit version
        $args['api_kit'] = 'php-' . $this->_version;

        // Sign the array of arguments
        $args['api_signature'] = $this->_sign($args);

        return $args;
    }

    /**
     * Gets the full API URL to use for a call
     *
     * @param string $call the API call to make.
     * @param array  $args optional. The call arguments.
     *
     * @return string the full API URL to use.
     */
    protected function getCallURL($call, array $args = array())
    {
        $url = $this->_url . $call . '?' . http_build_query($this->_args($args), "", "&");
        return $url;
    }

    /**
     * Makes an API call
     *
     * @param string $call the API call to make.
     * @param array  $args optional. The call arguments.
     *
     * @return array the response object.
     */
    public function call($call, array $args = array())
    {
        $url = $this->getCallURL($call, $args);

        $response = null;
        switch($this->_library) {
        case 'curl':
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            break;
        default:
            $response = file_get_contents($url);
            break;
        }

        $unserialized_response = @unserialize($response);

        return $unserialized_response ? $unserialized_response : $response;
    }

    /**
     * Uploads a file to BotR
     *
     * @param array  $upload_link information about the file to upload.
     * @param string $file_path   path to the file to upload.
     * @param string $api_format  optional. The requested response format.
     *                            Defaults to PHP.
     *
     * @return array the response object.
     */
    public function upload(array $upload_link, $file_path, $api_format = "php")
    {
        $url = $upload_link['protocol'] . '://' . $upload_link['address'] . $upload_link['path'] .
            "?key=" . $upload_link['query']['key'] . '&token=' . $upload_link['query']['token'] .
            "&api_format=" . $api_format;

        $post_data = array("file" => "@" . $file_path);
        $response = null;
        switch ($this->_library) {
        case 'curl':
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            $response = curl_exec($curl);
            $err_no = curl_errno($curl);
            $err_msg = curl_error($curl);
            curl_close($curl);
            break;
        default:
            $response = "Error: No cURL library";
            break;
        }

        if ($err_no == 0) {
            return unserialize($response);
        } else {
            return "Error #" . $err_no . ": " . $err_msg;
        }
    }
}

?>
