<?php

namespace Tests\Feature\Services;

use Tests\TestCase;

class DiscogsOauthServiceTest extends TestCase
{
    public function test_it_will_redirect_user_to_discogs_authorization_page()
    {
        $response = $this->get(route('oauth.create', [ 'provider' => 'discogs' ]));
        $response->assertRedirect();
    }
}
