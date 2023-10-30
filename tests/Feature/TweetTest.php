
<?php

use App\Http\Livewire\Tweet\Create;
use App\Models\Tweet;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\callback;

it('should be able to create a tweet', function () {
  $user = User::factory()->create();

  actingAs($user);

  livewire(Create::class)
    ->set('body', 'This is my first tweet')
    ->call('tweet')
    ->assertEmitted(value: 'tweet::created');

  assertDatabaseCount(table: 'tweets', count: 1);

  expect(Tweet::first())
    ->body->toBe(expected: 'This is my first tweet')
    ->created_by->toBe($user->id);
});

todo('should make sure that only authenticated users can tweet', function () {
  livewire(name: Create::class)
    ->set('body', 'This is my first tweet')
    ->call(method: 'tweet')
    ->assertForbidden();

  actingAs(User::factory()->create());

  livewire(name: Create::class)
    ->set('body', 'This is my first tweet')
    ->call(method: 'tweet')
    ->assertEmitted(value: 'tweet::created');
});

todo(description: 'body is required');

todo(description: 'the tweet body should have a max length of 140 characters');

todo(description: 'should show the tweet on the timeline');
