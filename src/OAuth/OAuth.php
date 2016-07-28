<?php

namespace HappyDemon\UitDatabank\OAuth;

class OAuth
{
    /**
     * All OAuth 1.0 requests use the same basic algorithm for creating a
     * signature base string and a signature. The signature base string is
     * composed of the HTTP method being used, followed by an ampersand ("&")
     * and then the URL-encoded base URL being accessed, complete with path
     * (but not query parameters), followed by an ampersand ("&"). Then, you
     * take all query parameters and POST body parameters (when the POST body is
     * of the URL-encoded type, otherwise the POST body is ignored), including
     * the OAuth parameters necessary for negotiation with the request at hand,
     * and sort them in lexicographical order by first parameter name and then
     * parameter value (for duplicate parameters), all the while ensuring that
     * both the key and the value for each parameter are URL encoded in
     * isolation. Instead of using the equals ("=") sign to mark the key/value
     * relationship, you use the URL-encoded form of "%3D". Each parameter is
     * then joined by the URL-escaped ampersand sign, "%26".
     *
     * @param  string $url        The URL.
     * @param  string $method     The method to use.
     * @param  array  $parameters The parameters.
     * @return string
     */
    public static function calculateBaseString($url, $method, array $parameters = null)
    {
        // redefine
        $url = (string) $url;
        $parameters = (array) $parameters;

        // init var
        $chunks = array();

        // sort parameters by key
        uksort($parameters, 'strcmp');

        // loop parameters
        foreach ($parameters as $key => $value) {
            // sort by value
            if (is_array($value)) {
                $parameters[$key] = natsort($value);
            }
        }

        // process queries
        foreach ($parameters as $key => $value) {
            // only add if not already in the url
            if (substr_count($url, $key . '=' . $value) == 0) {
                $chunks[] = self::urlencode_rfc3986($key) . '%3D' .
                            self::urlencode_rfc3986($value);
            }
        }

        // buils base
        $base = $method . '&';
        $base .= urlencode($url);
        $base .= (substr_count($url, '?')) ? '%26' : '&';
        $base .= implode('%26', $chunks);
        $base = str_replace('%3F', '&', $base);

        // return
        return $base;
    }

    /**
     * URL-encode method for internal use
     *
     * @param  mixed $value The value to encode.
     * @return string
     */
    private static function urlencode_rfc3986($value)
    {
        if (is_array($value)) {
            return array_map(array(__CLASS__, 'urlencode_rfc3986'), $value);
        } else {
            $search = array('+', ' ', '%7E', '%');
            $replace = array('%20', '%20', '~', '%25');

            return str_replace($search, $replace, urlencode($value));
        }
    }

    /**
     * Build the Authorization header
     * @later: fix me
     *
     * @param  array  $parameters The parameters.
     * @param  string $url        The URL.
     * @return string
     */
    public static function calculateHeader(array $parameters, $url)
    {
        // redefine
        $url = (string) $url;

        // divide into parts
        $parts = parse_url($url);

        // init var
        $chunks = array();

        // process queries
        foreach ($parameters as $key => $value) {
            $chunks[] = str_replace(
                '%25',
                '%',
                self::urlencode_rfc3986($key) . '="' . self::urlencode_rfc3986($value) . '"'
            );
        }

        // build return
        $return = 'Authorization: OAuth realm="' . $parts['scheme'] . '://' .
                  $parts['host'];
        if (isset($parts['path'])) {
            $return .= $parts['path'];
        }
        $return .= '", ';
        $return .= implode(',', $chunks);

        // prepend name and OAuth part
        return $return;
    }

    /**
     * Build the signature for the data
     *
     * @param  string $key  The key to use for signing.
     * @param  string $data The data that has to be signed.
     * @return string
     */
    public static function hmacsha1($key, $data)
    {
        return base64_encode(hash_hmac('SHA1', $data, $key, true));
    }
}
