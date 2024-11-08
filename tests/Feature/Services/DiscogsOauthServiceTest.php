<?php

namespace Tests\Feature\Services;

use App\Models\User;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;

class DiscogsOauthServiceTest extends TestCase
{
    public function test_it_will_redirect_user_to_discogs_authorization_page()
    {
        $this->mockSocialiteRedirect();

        $response = $this->actingAs(User::factory()->create())
            ->get(route('discogs.create'));
        $response->assertRedirect();
    }

    public function test_it_stores_discogs_user_data_and_redirects_back()
    {
        $this->mockSocialiteCallback();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('discogs.store.post'))
            ->assertRedirect();

        $this->assertDatabaseHas('services', [
            'user_id' => $user->id,
            'type' => 'discogs',
            'meta->id' => '12345',
            'meta->nickname' => 'test_nickname',
            'meta->token' => 'fake_token',
            'meta->tokenSecret' => 'fake_token_secret',
        ]);
    }

    /**
     * @return void
     */
    private function mockSocialiteRedirect(): void
    {
        $providerMock = Mockery::mock(Provider::class);
        $providerMock->shouldReceive('redirect')->once()->andReturn(redirect('http://example.com'));

        Socialite::shouldReceive('driver')
            ->with('discogs')
            ->once()
            ->andReturn($providerMock);
    }

    /**
     * @return void
     */
    private function mockSocialiteCallback(): void
    {
        $socialiteUserMock = Mockery::mock(SocialiteUser::class);
        $socialiteUserMock->shouldReceive('getId')->andReturn('12345');
        $socialiteUserMock->shouldReceive('getNickname')->andReturn('test_nickname');
        $socialiteUserMock->token = 'fake_token';
        $socialiteUserMock->tokenSecret = 'fake_token_secret';

        $providerMock = Mockery::mock(Provider::class);
        $providerMock->shouldReceive('user')->once()->andReturn($socialiteUserMock);

        Socialite::shouldReceive('driver')
            ->with('discogs')
            ->once()
            ->andReturn($providerMock);
    }
}
