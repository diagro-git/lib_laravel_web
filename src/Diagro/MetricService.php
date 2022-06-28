<?php
namespace Diagro\Web\Diagro;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class MetricService
{


    public int $started_at;

    public string $request_id;

    public ?int $ended_at = null;

    public ?Response $response = null;


    public function __construct(
        public Request $request,
        public ?int $user_id = null,
        public ?int $company_id = null,
        public ?string $parent_request_id = null,
    )
    {
        $this->started_at = hrtime(true);
        $this->request_id = Str::uuid()->toString();
    }

    public function stop(Response $response)
    {
        $this->response = $response;
        $this->ended_at = hrtime(true);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'request_id' => $this->request_id,
            'parent_request_id' => $this->parent_request_id,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'request' => [
                'request' => $this->request->request->all(),
                'uri' => $this->request->getRequestUri(),
                'host' => $this->request->getSchemeAndHttpHost(),
                'cookies' => $this->request->cookies->all(),
                'headers' => $this->request->headers->all(),
                'query' => $this->request->query->all(),
                'method' => $this->request->method()
            ],
            'response' => [
                'status' => $this->response->getStatusCode(),
                'headers' => $this->response->headers->all(),
                'first_100_bytes' => substr($this->response->getContent(), 0, 100),
                'last_100_bytes' => substr($this->response->getContent(), -100),
            ]
        ];
    }


}