<?php

namespace App\Controller;

use App\CurlTransport;
use App\Factory\Logger;
use App\HttpClient;
use App\InputValidator;
use App\QueueHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends BaseController
{
    public function index(Request $request): Response
    {
        $list = $this->database->fetch('requests', ['method' => 'GET']);

        return new Response($this->render('index.html.twig', ['jobs' => $list]), 200);
    }

    public function add(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            return new Response($this->render('add.html.twig'));
        }

        $validator = new InputValidator();
        $errors = $validator->validate($request->request);

        if (count($errors) > 0) {
            return new Response($this->render('add.html.twig', ['errors' => $errors]));
        }

        $this->database->insert('requests', [
            'method' => $request->request->get('method'),
            'url' => $request->request->get('url'),
        ]);

        return new RedirectResponse('/#added', 302);
    }

    public function run(Request $request): Response
    {
        $transport = new CurlTransport();
        $logger = Logger::create('queue');
        $httpClient = new HttpClient($transport);
        try {
            $handler = new QueueHandler($this->database, $httpClient, $logger);
            $handler->run();
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        return new RedirectResponse('/#redirected', 302);
    }

    public function result(Request $request): Response
    {
        $url = $request->get('url');

        $item = $this->database->fetch('requests', ['url' => $url]);

        return new Response($this->render('result.html.twig', ['item' => $item]));
    }
}
