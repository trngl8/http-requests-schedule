<?php

namespace App;

use App\Exception\{ValidatorException, TransportException};

class HttpClient implements HttpClientInterface
{
    private TransportInterface $transport;

    private int $status;

    private array $availableMethods = ['GET', 'POST'];

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @throws ValidatorException|TransportException
     */
    public function request(string $method, string $url, array $data = []): string
    {
        // TODO: validate function
        if (!in_array($method, $this->availableMethods)) {
            throw new ValidatorException('method', $method);
        }

        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new ValidatorException('url', $url);
        }

        $this->transport
            ->addOption(CURLOPT_URL, $url)
            ->addOption(CURLOPT_RETURNTRANSFER, true)
        ;

        if ($method === 'GET') {
            $this->transport
                ->addOption(CURLOPT_FOLLOWLOCATION, true)
                ->addOption(CURLOPT_HTTPGET, true)
            ;
        }

        if ($method === 'POST') {
            $this->transport
                ->addOption(CURLOPT_POST, true)
                ->addOption(CURLOPT_POSTFIELDS, http_build_query($data))
            ;
        }

        $result = $this->transport->execute();

        if (!$result) {
            $erno = $this->transport->getErrno();
            $error = $this->transport->getError();
            $this->transport->close();
            throw new TransportException(sprintf('Curl transport error %d. %s on url "%s"', $erno, $error, $url));
        }

        $this->status = $this->transport->getInfo(CURLINFO_HTTP_CODE);

        return $result;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getHeader(string $name): string
    {
        if (array_key_exists($name, $this->getHeaders())) {
            return $this->getHeaders()[$name];
        }

        return '';
    }

    public function getBody(): string
    {
        return '';
    }
}
