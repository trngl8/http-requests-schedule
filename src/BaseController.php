<?php

namespace App;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(array $data): Response
    {
        $db = new \SQLite3('../var/requests.table.db', SQLITE3_OPEN_READONLY);

        $db->enableExceptions(true);

        $statement = $db->prepare('SELECT * FROM requests WHERE code >= ?');
        $statement->bindValue(1, 0);

        $list = [];
        $messages = $statement->execute();

        while ($item = $messages->fetchArray(SQLITE3_ASSOC)) {
            $list[] = $item;
        }
        $db->close();

        return new Response($this->render('index.html.php', array_merge($data, ['jobs' => $list])), 200);
    }

    public function run(Request $request): Response
    {
        $url = $request->get('url');
        if ($url && !$this->validateUrl($url)) {
            return new Response($this->render('error.html.php', ['error' => sprintf('Invalid URL %s', $url)]), 400);
        }
        return new RedirectResponse('/#redirected', 302);
    }

    private function render(string $template, array $data = []): string
    {
        extract($data);
        ob_start();
        include __DIR__ . '/../templates/' . $template;
        return ob_get_clean();
    }

    private function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}