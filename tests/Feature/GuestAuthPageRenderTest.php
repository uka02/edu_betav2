<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAuthPageRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_auth_pages_include_their_main_stylesheet_markup(): void
    {
        $styleBlockPattern = '/<style>\s*\*, \*::before, \*::after \{ box-sizing: border-box; margin: 0; padding: 0; \}/';
        $viteStylesheetPattern = '/<link[^>]+href="[^"]+\/build\/assets\/[^"]+\.css"/';

        foreach ([
            route('home'),
            route('login'),
            route('signup'),
            route('signup.learner'),
            route('signup.educator'),
        ] as $url) {
            $response = $this->get($url)->assertOk();

            $content = $response->getContent();

            $this->assertTrue(
                preg_match($styleBlockPattern, $content) === 1
                || preg_match($viteStylesheetPattern, $content) === 1,
                "Expected {$url} to include either an inline style block or a built stylesheet link."
            );
        }
    }
}
