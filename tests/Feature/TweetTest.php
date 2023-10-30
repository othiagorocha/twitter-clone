
<?php

use App\Http\Livewire\Timeline;
use App\Http\Livewire\Tweet\Create;
use App\Models\Tweet;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Livewire\livewire;

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

test('body is required', function () {
  actingAs(User::factory()->create());

  livewire(name: Create::class)
    ->set('body', null)
    ->call(method: 'tweet')
    ->assertHasErrors(['body' => 'required']);
});

test('the tweet body should have a max length of 140 characters', function () {
  actingAs(User::factory()->create());

  livewire(name: Create::class)
    ->set('body', str_repeat(string: 'a', times: 141))
    ->call(method: 'tweet')
    ->assertHasErrors(['body' => 'max']);
});


it('should show the tweet on the timeline', function () {
  $user = User::factory()->create();

  actingAs($user);

  livewire(Create::class)
    ->set('body', 'This is my first tweet')
    ->call('tweet')
    ->assertEmitted('tweet::created');

  livewire(Timeline::class)
    ->assertSee('This is my first tweet');
});
