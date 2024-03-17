<?php

namespace App;

interface HttpStatusInterface
{
    public function getStatusCode(): int;
}