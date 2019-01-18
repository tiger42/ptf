<?php

namespace Ptf\Core\Http;

/**
 * Encapsulates the server request.
 */
class Request
{
    /**
     * Get the remote IP address.
     *
     * @return string  The remote address
     */
    public function getRemoteAddr(): string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $addresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            return trim($addresses[0]);
        }

        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    }

    /**
     * Get the used HTTP protocol.
     *
     * @return string  The used protocol ("HTTP" or "HTTPS")
     */
    public function getProtocol(): string
    {
        return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
            ? 'HTTPS' : 'HTTP';
    }

    /**
     * Get the HTTP host name.
     *
     * @return string  The host name
     */
    public function getHost(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    /**
     * Get the request URI.
     *
     * @return string  The request URI
     */
    public function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }

    /**
     * Get the request method.
     *
     * @return string  The request method
    */
    public function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }

    /**
     * Determine whether the request was initiated via Ajax.
     *
     * @return bool  Is this request an Ajax request?
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Get all GET variables.
     *
     * @param string $namespace  Get only variables from this namespace, if given
     *
     * @return array  The GET variables
     */
    public function getGetVars(string $namespace = null): array
    {
        return $namespace !== null ? $this->getNamespacedVars($_GET, $namespace) : $_GET;
    }

    /**
     * Get all POST variables.
     *
     * @param string $namespace  Get only variables from this namespace, if given
     *
     * @return array  The POST variables
     */
    public function getPostVars(string $namespace = null): array
    {
        return $namespace !== null ? $this->getNamespacedVars($_POST, $namespace) : $_POST;
    }

    /**
     * Get all cookie values.
     *
     * @param string $namespace  Get only values from this namespace, if given
     *
     * @return array  The cookie values
     */
    public function getCookieValues(string $namespace = null): array
    {
        return $namespace !== null ? $this->getNamespacedVars($_COOKIE, $namespace) : $_COOKIE;
    }

    /**
     * Get all request variables.
     *
     * @param string $namespace  Get only variables from this namespace, if given
     *
     * @return array  The request variables
     */
    public function getRequestVars(string $namespace = null): array
    {
        return $namespace !== null ? $this->getNamespacedVars($_REQUEST, $namespace) : $_REQUEST;
    }

    /**
     * Get the value of the given GET variable.
     *
     * @param string $name  The name of the variable to get the value of
     *
     * @return string  The GET value
     */
    public function getGetVar(string $name): ?string
    {
        return $_GET[$name] ?? null;
    }

    /**
     * Get the value of the given POST variable.
     *
     * @param string $name  The name of the variable to get the value of
     *
     * @return string  The POST value
     */
    public function getPostVar(string $name): ?string
    {
        return $_POST[$name] ?? null;
    }

    /**
     * Get the value of the given cookie.
     *
     * @param string $name  The name of the cookie to get the value of
     *
     * @return string  The cookie value
     */
    public function getCookieValue(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Get the value of the given request variable.
     *
     * @param string $name  The name of the variable to get the value of
     *
     * @return string  The request value
     */
    public function getRequestVar(string $name): ?string
    {
        return $_REQUEST[$name] ?? null;
    }

    /**
     * Get the Accept-Language header field.
     *
     * @return array  Array of languages and their priority, ordered by priority from high to low
     */
    public function getAcceptLanguage(): array
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
     * Return the most preferred language.
     *
     * @param bool $withoutCountry  Remove the country part from the language string, if present?
     *
     * @return string  The most preferred language
     */
    public function getPreferredLanguage(bool $withoutCountry = false): ?string
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
     * Extract namespaced variables from the given array.
     *
     * @param array  $data       The array to fetch the variables from
     * @param string $namespace  The namespace of the variables to search for
     *
     * @return array  The variables of the given namespace (namespace prefix stripped from the key!)
     */
    private function getNamespacedVars(array $data, string $namespace): array
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
