<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test the application root redirects to login for unauthenticated users.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Root route redirects unauthenticated users to login
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
