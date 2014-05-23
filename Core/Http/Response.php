<?php

namespace Ptf\Core\Http;

/**
 * Encapsulates the server response
 */
class Response
{
    /**
     * The headers to send
     * @var array
     */
    protected $headers;
    /**
     * The content to send
     * @var string
     */
    protected $content;

    /**
     * Initialize the member variables
     */
    public function __construct()
    {
        $this->headers = [];
        $this->content = null;
    }

    /**
     * Set the given header
     *
     * @param   string $header              The header to set
     * @param   integer $responseCode       The response code to send with the header
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function setHeader($header, $responseCode = null)
    {
        $this->headers[$header] = $responseCode;

        return $this;
    }

    /**
     * Set a "404 Not Found" header
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function set404Header()
    {
        return $this->setHeader('HTTP/1.0 404 Not Found');
    }

    /**
     * Set a "Location" header
     *
     * @param   string $url                 The URL to redirect to
     * @param   integer $responseCode       The redirect response code
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function setRedirectHeader($url, $responseCode = 302)
    {
        return $this->setHeader('Location: ' . $url, $responseCode);
    }

    /**
     * Set a "Content-type" header
     *
     * @param   string $contentType         The content type to set
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function setContentTypeHeader($contentType)
    {
        return $this->setHeader('Content-type: ' . $contentType);
    }

    /**
     * Set a JSON content type header
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function setJsonHeader()
    {
        return $this->setContentTypeHeader('application/json');
    }

    /**
     * Set a header to bypass the browser cache
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function setNoCacheHeader()
    {
        return $this->setHeader('Cache-Control: no-cache, must-revalidate')
            ->setHeader('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Clear all set headers
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function clearHeaders()
    {
        $this->headers = [];

        return $this;
    }

    /**
     * Set the content to send
     *
     * @param   string $content             The content to set
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Return whether any content has been set
     *
     * @return  boolean                     Has the content been set?
     */
    public function hasContent()
    {
        return isset($this->content);
    }

    /**
     * Send the set headers to the client
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function sendHeaders()
    {
        foreach ($this->headers as $header => $responseCode) {
            header($header, true, $responseCode);
        }
        return $this;
    }

    /**
     * Send the set content to the client
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function sendContent()
    {
        echo $this->content;

        return $this;
    }

    /**
     * Send the set headers and the content
     *
     * @return  \Ptf\Core\Http\Response     The response object (for fluent interface)
     */
    public function send()
    {
        return $this->sendHeaders()->sendContent();
    }

}
