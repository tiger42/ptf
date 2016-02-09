<?php

namespace Ptf\Core\Http;

/**
 * Encapsulates the server request
 */
class Request
{
    /**
     * Get the remote IP address
     *
     * @return  string                      The remote address
     */
    public function getRemoteAddr()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $addresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addresses[0]);
        }
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * Get the used HTTP protocol
     *
     * @return  string                      The used protocol ("HTTP" or "HTTPS")
     */
    public function getProtocol()
    {
        return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
            ? 'HTTPS' : 'HTTP';
    }

    /**
     * Get the HTTP host name
     *
     * @return  string                      The host name
     */
    public function getHost()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    }

    /**
     * Get the request URI
     *
     * @return  string                      The request URI
     */
    public function getRequestUri()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    }

    /**
     * Get the request method
     *
     * @return  string                      The request method
    */
    public function getRequestMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    }

    /**
     * Was the request initiated by Ajax?
     *
     * @return  boolean                     Is this request an Ajax request?
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Get all GET variables
     *
     * @param   string $namespace           Get only variables from this namespace, if given
     * @return  array                       The GET variables
     */
    public function getGetVars($namespace = null)
    {
        if ($namespace !== null) {
            return $this->getNamespacedVars($_GET, $namespace);
        }
        return $_GET;
    }

    /**
     * Get all POST variables
     *
     * @param   string $namespace           Get only variables from this namespace, if given
     * @return  array                       The POST variables
     */
    public function getPostVars($namespace = null)
    {
        if ($namespace !== null) {
            return $this->getNamespacedVars($_POST, $namespace);
        }
        return $_POST;
    }

    /**
     * Get all cookie values
     *
     * @param   string $namespace           Get only values from this namespace, if given
     * @return  array                       The cookie values
     */
    public function getCookieValues($namespace = null)
    {
        if ($namespace !== null) {
            return $this->getNamespacedVars($_COOKIE, $namespace);
        }
        return $_COOKIE;
    }

    /**
     * Get all request variables
     *
     * @param   string $namespace           Get only variables from this namespace, if given
     * @return  array                       The request variables
     */
    public function getRequestVars($namespace = null)
    {
        if ($namespace !== null) {
            return $this->getNamespacedVars($_REQUEST, $namespace);
        }
        return $_REQUEST;
    }

    /**
     * Get the value of the given GET variable
     *
     * @param   string $name                The name of the variable to get the value of
     * @return  string                      The GET value
     */
    public function getGetVar($name)
    {
        return isset($_GET[$name]) ? $_GET[$name] : null;
    }

    /**
     * Get the value of the given POST variable
     *
     * @param   string $name                The name of the variable to get the value of
     * @return  string                      The POST value
     */
    public function getPostVar($name)
    {
        return isset($_POST[$name]) ? $_POST[$name] : null;
    }

    /**
     * Get the value of the given cookie
     *
     * @param   string $name                The name of the cookie to get the value of
     * @return  string                      The cookie value
     */
    public function getCookieValue($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    /**
     * Get the value of the given request variable
     *
     * @param   string $name                The name of the variable to get the value of
     * @return  string                      The request value
     */
    public function getRequestVar($name)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
    }

    /**
     * Get the Accept-Language header field
     *
     * @return  array                       Array of languages and their priority, ordered by priority from high to low
     */
    public function getAcceptLanguage()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return [];
        }

        $acceptLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        // Example: "de-de, de;q=0.8, en-us;q=0.5, en;q=0.3, *;q=0.6"

        $languages = [];
        foreach ($acceptLangs as $acceptLang) {
            $parts = explode(';', trim($acceptLang));
            if (!isset($parts[1])) {
                $languages[strtolower($parts[0])] = '1';
            } else {
                $q = explode('=', $parts[1]);
                if ($q[1] == '0') {
                    continue;
                }
                $languages[strtolower($parts[0])] = $q[1];
            }
        }
        asort($languages);
        $languages = array_reverse($languages, true);

        return $languages;
    }

    /**
     * Return the most preferred language
     *
     * @param   boolean $withoutCountry     Remove the country part from the language string, if present?
     * @return  string                      The most preferred language
     */
    public function getPreferredLanguage($withoutCountry = false)
    {
        $acceptLang = $this->getAcceptLanguage();
        $prefLang   = key($acceptLang);

        if ($withoutCountry && $prefLang !== null) {
            $parts = explode('-', $prefLang);
            $prefLang = $parts[0];
        }

        return $prefLang;
    }

    /**
     * Extract namespaced variables from the given array
     *
     * @param   array $data                 The array to fetch the variables from
     * @param   string $namespace           The namespace of the variables to search for
     * @return  array                       The variables of the given namespace (namespace prefix stripped from the key!)
     */
    private function getNamespacedVars(array $data, $namespace)
    {
        $vars = [];
        foreach ($data as $key => $value) {
            if (strpos($key, $namespace . ':') === 0) {
                $vars[substr($key, strlen($namespace . ':'))] = $value;
            }
        }
        return $vars;
    }
}
