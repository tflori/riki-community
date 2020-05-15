<?php

namespace Test\Http;

class NotFoundTest extends TestCase
{
    /** @test */
    public function returnsThe404Page()
    {
        $response = $this->get('/any/route');

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('<h4>File Not Found</h4>', $response->getBody()->getContents());
    }

    /** @test */
    public function returnsJsonWhenRequested()
    {
        $response = $this->get('/any/route', [], ['Accept' => 'application/json']);

        self::assertSame(404, $response->getStatusCode());
        $content = json_decode($response->getBody()->getContents(), true);
        self::assertArraySubset(['reason' => 'File Not Found'], $content);
    }
}
