<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;
use Mockery;

class DiscogsOauthServiceTest extends TestCase
{
    public function test_it_will_redirect_user_to_discogs_authorization_page()
    {
        $providerMock = Mockery::mock(Provider::class);
        $providerMock->shouldReceive('redirect')->once()->andReturn(redirect('http://example.com'));

        // Mock di Socialite per restituire il provider mockato
        Socialite::shouldReceive('driver')
            ->with('discogs')
            ->once()
            ->andReturn($providerMock);

        $response = $this->get(route('oauth.create', [ 'provider' => 'discogs' ]));
        $response->assertRedirect();
    }
}
