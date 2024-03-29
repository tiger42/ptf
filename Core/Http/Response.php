<?php

namespace Ptf\Core\Http;

/**
 * Encapsulates the server response.
 */
class Response
{
    /** @var array  The headers to send */
    protected $headers;

    /** @var string  The content to send */
    protected $content;

    /**
     * Initialize the member variables.
     */
    public function __construct()
    {
        $this->headers = [];
        $this->content = null;
    }

    /**
     * Set the given header.
     *
     * @param string $header        The header to set
     * @param int    $responseCode  The response code to send with the header
     *
     * @return Response  The response object (for fluent interface)
     */
    public function setHeader(string $header, int $responseCode = null): self
    {
        $this->headers[$header] = $responseCode;

        return $this;
    }

    /**
     * Set a "404 Not Found" header.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function set404Header(): self
    {
        return $this->setHeader('HTTP/1.0 404 Not Found');
    }

    /**
     * Set a "Location" header.
     *
     * @param string $url           The URL to redirect to
     * @param int    $responseCode  The redirect response code
     *
     * @return Response  The response object (for fluent interface)
     */
    public function setRedirectHeader($url, $responseCode = 302): self
    {
        return $this->setHeader('Location: ' . $url, $responseCode);
    }

    /**
     * Set a "Content-type" header.
     *
     * @param string $contentType  The content type to set
     *
     * @return Response  The response object (for fluent interface)
     */
    public function setContentTypeHeader($contentType): self
    {
        return $this->setHeader('Content-type: ' . $contentType);
    }

    /**
     * Set a JSON content type header.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function setJsonHeader(): self
    {
        return $this->setContentTypeHeader('application/json');
    }

    /**
     * Set a header to bypass the browser cache.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function setNoCacheHeader(): self
    {
        return $this->setHeader('Cache-Control: no-cache, must-revalidate')
            ->setHeader('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Get all headers that will be sent.
     *
     * @return array  The headers to be sent
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Clear all set headers.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function clearHeaders(): self
    {
        $this->headers = [];
        header_remove();

        return $this;
    }

    /**
     * Set the content to send.
     *
     * @param string $content  The content to set
     *
     * @return Response The response object (for fluent interface)
     */
    public function setContent(string $content = null): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the content which will be sent.
     *
     * @return string  The content to be sent
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Return whether any content has been set.
     *
     * @return bool Has the content been set?
     */
    public function hasContent(): bool
    {
        return $this->content !== null;
    }

    /**
     * Send the set headers to the client.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function sendHeaders(): self
    {
        foreach ($this->headers as $header => $responseCode) {
            $responseCode = $responseCode ?? 0;
            header($header, true, $responseCode);
        }

        return $this;
    }

    /**
     * Send the set content to the client.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function sendContent(): self
    {
        echo $this->content;

        return $this;
    }

    /**
     * Send the set headers and the content.
     *
     * @return Response  The response object (for fluent interface)
     */
    public function send(): self
    {
        return $this->sendHeaders()->sendContent();
    }
}
