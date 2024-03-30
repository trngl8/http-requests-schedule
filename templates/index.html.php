<?php
/** @var string $title */
/** @var array $jobs */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="logo" viewBox="0 0 118 94">
        <title><?php echo $title; ?></title>

        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
            <path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
        </svg>
    </symbol>
</svg>

<div class="col-lg-8 mx-auto p-4 py-md-5">
    <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
        <a href="/" class="d-flex align-items-center text-body-emphasis text-decoration-none">
            <svg class="bi me-2" width="40" height="32"><use xlink:href="#logo"/></svg>
            <span class="fs-4"><?php echo $title; ?></span>
        </a>
    </header>

    <main>
        <h1 class="text-body-emphasis">http jobs</h1>
        <p class="fs-5 col-md-8 mb-5">Here is a queue ot the planned HTTP requests</p>
        <?php if ($jobs): ?>
            <form method="post" action="/run" class="mb-1">
                <input type="hidden" id="action" name="action" value="run">
                <button type="submit" class="btn btn-primary btn-lg px-4">RUN</button>
            </form>
            <table class="table" aria-describedby="http-jobs">
                <thead>
                <tr>
                    <th scope="col">Method</th>
                    <th scope="col">URL</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Code</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $colors = [
                        200 => 'success',
                        404 => 'secondary',
                        500 => 'warning',
                    ];
                    $job['status_color'] = in_array($job['code'], array_keys($colors)) ? $colors[$job['code']] : 'primary';
                    ?>
                <tr>
                    <th scope="row"><?=$job['method'];?></th>
                    <td><?=$job['url'];?></td>
                    <td><?=$job['created_at'];?></td>
                    <td>
                        <?php if ($job['code'] > 0) : ?>
                        <span class="badge text-bg-<?=$job['status_color'] ?>"><?=$job['code'];?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($job['code'] > 0) : ?>
                        <a class="btn btn-sm btn-outline-primary" href="/result?url=<?=$job['url'];?>">Result</a></td>
                        <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="fs-5 col-md-8">No jobs found</p>
        <?php endif; ?>
        <a href="/add" class="btn btn-outline-primary">Add new</a>


    </main>
    <footer class="pt-5 my-5 text-body-secondary border-top">
        Created by The Team &middot; &copy; 2024
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
