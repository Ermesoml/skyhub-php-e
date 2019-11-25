<?php
/**
 * B2W Digital - Companhia Digital
 *
 * Do not edit this file if you want to update this SDK for future new versions.
 * For support please contact the e-mail bellow:
 *
 * sdk@e-smart.com.br
 *
 * @category  SkyHub
 * @package   SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br).
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */

namespace SkyHub\Api\Service;

use GuzzleHttp\Client as HttpClient;
use SkyHub\Api;
use SkyHub\Api\Helpers;
use SkyHub\Api\Handler\Response\HandlerDefault;
use SkyHub\Api\Log\Loggerable;
use SkyHub\Api\Log\TypeInterface\Request;
use SkyHub\Api\Log\TypeInterface\Response;
use SkyHub\Api\Handler\Response\HandlerException;

/**
 * Class ServiceAbstract
 *
 * @package SkyHub\Api\Service
 */
abstract class ServiceAbstract implements ServiceInterface
{
    use Loggerable, Helpers;

    /**
     * @var string
     */
    const REQUEST_METHOD_GET = 'GET';

    /**
     * @var string
     */
    const REQUEST_METHOD_POST = 'POST';

    /**
     * @var string
     */
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * @var string
     */
    const REQUEST_METHOD_HEAD = 'HEAD';

    /**
     * @var string
     */
    const REQUEST_METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    const REQUEST_METHOD_PATCH = 'PATCH';

    /**
     * @var string
     */
    const DEFAULT_SERVICE_BASE_URI = 'https://api.skyhub.com.br';

    /** @var HttpClient */
    protected $client = null;

    /** @var array */
    protected $headers = [];

    /** @var int */
    protected $timeout = 15;

    /** @var int */
    protected $requestId = null;

    /**
     * @var ClientBuilderInterface
     */
    private $clientBuilder;

    /**
     * @var OptionsBuilderInterface
     */
    private $optionsBuilder;

    /**
     * ServiceAbstract constructor.
     *
     * @param null                        $baseUri
     * @param array                       $headers
     * @param array                       $options
     * @param bool                        $log
     * @param ClientBuilderInterface|null $clientBuilder
     */
    public function __construct(
        $baseUri = null,
        array $headers = [],
        array $options = [],
        ClientBuilderInterface $clientBuilder = null,
        OptionsBuilderInterface $optionsBuilder = null
    ) {
        if (null === $clientBuilder) {
            $this->clientBuilder = new ClientBuilder();
        }

        if (null === $optionsBuilder) {
            $this->optionsBuilder = new OptionsBuilder();
        }

        $this->optionsBuilder
            ->addOptions($options)
            ->getHeadersBuilder()
            ->addHeaders($headers);

        $this->prepareHttpClient($baseUri);

        return $this;
    }

    /**
     * Returns the default base URI.
     *
     * @return string
     */
    public function getDefaultBaseUri()
    {
        return self::DEFAULT_SERVICE_BASE_URI;
    }

    /**
     * @param bool $renew
     *
     * @return int
     */
    public function getRequestId($renew = false)
    {
        if (empty($this->requestId) || $renew) {
            $this->requestId = rand(1000000000000, 9999999999999);
        }

        return $this->requestId;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param null   $body
     * @param array  $options
     *
     * @return Api\Handler\Response\HandlerInterfaceException|Api\Handler\Response\HandlerInterfaceSuccess
     */
    public function request($method, $uri, $body = null, array $options = [], $debug = false)
    {
        $this->optionsBuilder
            ->addOptions($options)
            ->setTimeout($this->getTimeout())
            ->setDebug((bool) $debug)
            ->setBody($body)
            ->getHeadersBuilder()
            ->addHeaders($this->headers);

        try {
            /** Log the request before sending it. */
            $logRequest = new Request(
                $this->getRequestId(),
                $method,
                $uri,
                $body,
                $this->protectedHeaders($this->headers),
                $this->protectedOptions($options)
            );

            $this->logger()->logRequest($logRequest);

            /** @var \Psr\Http\Message\ResponseInterface $request */
            $response = $this->httpClient()->request($method, $uri, $this->optionsBuilder->build());

            /** @var Api\Handler\Response\HandlerInterfaceSuccess $responseHandler */
            $responseHandler = new HandlerDefault($response);

            /** Log the request response. */
            $logResponse = $this->getLoggerResponse()->importResponseHandler($responseHandler);
        } catch (\GuzzleHttp\Exception\ClientException $clientException) {
            /** Service Request Exception */
            $responseHandler = new HandlerException($clientException);

            $logResponse = $this->getLoggerResponse()
                ->importResponseExceptionHandler($responseHandler);
        } catch (\Exception $exception) {
            /** Service Request Exception */
            $responseHandler = new HandlerException($exception);

            $logResponse = $this->getLoggerResponse()
                ->importResponseExceptionHandler($responseHandler);
        }

        $this->clear();
        $this->logger()->logResponse($logResponse);

        return $responseHandler;
    }

    /**
     * This method clears the unnecessary information after a request.
     *
     * @return $this
     */
    protected function clear()
    {
        $this->clearRequestId();

        return $this;
    }

    /**
     * @return $this
     */
    protected function clearRequestId()
    {
        $this->requestId = null;

        return $this;
    }

    /**
     * @param string|array $bodyData
     * @param array        $options
     *
     * @return array
     */
    protected function prepareRequestBody($bodyData, array &$options = [])
    {
        $options['body'] = $bodyData;
        return $options;
    }

    /**
     * A private __clone method prevents this class to be cloned by any other class.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * A private __wakeup method prevents this object to be unserialized.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    /**
     * @return HttpClient
     */
    protected function httpClient()
    {
        return $this->client;
    }

    /**
     * @param null  $baseUri
     * @param array $defaults
     *
     * @return HttpClient
     */
    protected function prepareHttpClient($baseUri = null)
    {
        if (empty($baseUri)) {
            $baseUri = $this->getDefaultBaseUri();
        }

        if (null === $this->client) {
            $this->client = $this->clientBuilder->build($baseUri);
        }

        return $this->client;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return (array) $this->headers;
    }

    /**
     * @param array $headers
     * @param bool  $append
     *
     * @return $this|ServiceInterface
     */
    public function setHeaders(array $headers = [])
    {
        $this->optionsBuilder
            ->getHeadersBuilder()
            ->addHeaders($headers);

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return (int) $this->timeout;
    }

    /**
     * @param integer $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;

        return $this;
    }

    /**
     * @param $options
     *
     * @return mixed
     */
    protected function protectedOptions($options)
    {
        $headers = $this->arrayExtract($options, 'headers');

        if (empty($headers)) {
            return $options;
        }

        $headers = $this->protectedHeaders($headers);
        $options['headers'] = $headers;

        return $options;
    }

    /**
     * @return array
     */
    protected function protectedHeaders(array $headers = [])
    {
        if (empty($headers)) {
            $headers = $this->headers;
        }

        if (isset($headers[Api::HEADER_USER_EMAIL])) {
            $headers[Api::HEADER_USER_EMAIL] = $this->protectString($headers[Api::HEADER_USER_EMAIL]);
        }

        if (isset($headers[Api::HEADER_API_KEY])) {
            $headers[Api::HEADER_API_KEY] = $this->protectString($headers[Api::HEADER_API_KEY]);
        }

        if (isset($headers[Api::HEADER_ACCOUNT_MANAGER_KEY])) {
            $headers[Api::HEADER_ACCOUNT_MANAGER_KEY] = $this->protectString($headers[Api::HEADER_ACCOUNT_MANAGER_KEY]);
        }

        return $headers;
    }

    /**
     * @return \SkyHub\Api\Log\TypeInterface\TypeResponseInterface
     */
    protected function getLoggerResponse()
    {
        return new Response($this->getRequestId());
    }
}
