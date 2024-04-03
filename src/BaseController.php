<?php

namespace App;

use App\Exception\TransportException;
use App\Exception\ValidatorException;
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
        $db = new \SQLite3('../var/requests.db', SQLITE3_OPEN_READONLY);

        $db->enableExceptions(true);

        $statement = $db->prepare('SELECT req.*, res.code, res.body FROM requests req LEFT JOIN responses res ON req.id = res.request_id WHERE req.method = ?');
        $statement->bindValue(1, 'GET');

        $list = [];
        $messages = $statement->execute();

        while ($item = $messages->fetchArray(SQLITE3_ASSOC)) {
            $list[] = $item;
        }
        $db->close();

        return new Response($this->render('index.html.php', array_merge($data, ['jobs' => $list])), 200);
    }

    /**
     * @throws TransportException
     * @throws ValidatorException
     */
    public function run(Request $request): Response
    {
        //TODO: validate $request if exists
        try {
            $handler = new QueueHandler();
            $handler->run();
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        return new RedirectResponse('/#redirected', 302);
    }

    public function add(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            return new Response($this->render('add.html.php'));
        }

        $errors = $this->validate($request);

        if (count($errors) > 0) {
            return new Response($this->render('add.html.php', ['errors' => $errors]));
        }

        $db = new \SQLite3('../var/requests.db', SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);
        $db->exec('BEGIN');
        $statement = $db->prepare('INSERT INTO requests(method, url) VALUES(:method, :url)');
        $statement->bindValue(':url', $request->request->get('url'));
        $statement->bindValue(':method', $request->request->get('method'));
        $statement->execute();
        $db->exec('COMMIT');
        $db->close();

        return new RedirectResponse('/#added', 302);
    }

    public function result(Request $request): Response
    {
        $url = $request->get('url');
        $db = new \SQLite3('../var/requests.db', SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);
        $statement = $db->prepare('SELECT * FROM requests WHERE url=?');
        $statement->bindValue(1, $url);
        $result = $statement->execute();
        $item = $result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return new Response($this->render('result.html.php', ['item' => $item]));
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

    private function validate(Request $request): array
    {
        $errors = [];
        if (!$request->request->get('url')) {
            $errors['url'] = 'URL is required';
        }
        if (!$request->request->get('method')) {
            $errors['method'] = 'Method is required';
        }
        if (!in_array($request->request->get('method') , ['GET', 'POST'])) {
            $errors['method'] = 'Invalid method';
        }
        if (!filter_var($request->request->get('url'), FILTER_VALIDATE_URL)) {
            $errors['url'] = 'Invalid URL';
        }
        return $errors;
    }
}
