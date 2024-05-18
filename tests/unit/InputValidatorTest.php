<?php

namespace App\Tests\Unit;

use App\InputValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class InputValidatorTest extends TestCase
{
    public function testInputValid(): void
    {
        $request = new Request();
        $target = new InputValidator();
        $result = $target->validate($request->request);
        $this->assertNotEmpty($result);
    }
}
