<?php

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Kernel;
use Twig\Environment as TemplatesEngine;

class KernelTest extends TestCase
{
    public function testKernel()
    {
        $kernel = new Kernel();
        $this->assertInstanceOf(TemplatesEngine::class, $kernel->getTemplateEngine());
        $this->assertEquals(0, $kernel->getDatabase()->lastInsertRowID());
        $this->assertStringEndsWith('/src/../', $kernel->getSourceFolder());
    }
}
