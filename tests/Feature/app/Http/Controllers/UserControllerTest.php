<?php

use App\Models\Appointment;
use App\Models\User;



describe("GET all requests", function() {
    beforeEach(function() {
        getDolores();
    });

    it('returns a paginated list of users', function () {
        $response = checksGetAll(route('users.index'), false);
        expect($response->json('data'))->toHaveCount(15);

        checksGetAll(route('users.index'), true);
    });

    it('supports a text-search on name', function() {
        $input = 'dol';
        $route = route('users.index', ['name' => $input]);
        $response = checksGetAll($route, false);
        $results = collect($response->json('data'));
        $results->each(function ($item) {
            expect(strtolower($item['name']))->toContain('dol');
        });
        checksGetAll($route, true);
    });

    it('supports an exact-search on email', function() {
        $input = 'dol@easi.net';

        $route = route('users.index', ['email' => $input]);
        $response = checksGetAll($route, false);
        $results = collect($response->json('data'));
        expect($results)->toHaveCount(1);
        $dolores = $results[0];
        expect($dolores['email'])->toEqual($input);
        checksGetAll($route, true);
    });

    function checksGetAll($requestRoute, $performance) {
        $start = hrtime(true);

        $response = test()->getJson($requestRoute);
        $end = hrtime(true);
        $response->assertStatus(200);
        $payload = $response->json();

        $performanceRes = macroToMilliSeconds($end - $start);
        expect($payload)->toBeArray()
            ->toHaveKeys([
                "data",
                "per_page",
                "current_page",
            ]);
        if($performance) {
            expect($performanceRes)->toBeLessThan(50);
        } else {
            expect($performanceRes)->toBeLessThan(500);
        }
        return $response;
    }
});

describe("Get One tests", function() {
    it("Fetches the correct user, with detailed information in a performant way", function() {
        $dolores = getDolores();
        $route = route('users.show', ['user' => $dolores->id]);

        $start = hrtime(true);

        $response = $this->getJson($route);
        $end = hrtime(true);
        $response->assertStatus(200);
        $payload = $response->json();

        expect($payload)->toHaveKeys(["id", "name", "email", "created_at", "appointments"])
            ->and($payload["id"])->toEqual($dolores->id)
            ->and($payload["email"])->toEqual($dolores->email)
            ->and($payload["appointment_history"])->toHaveCount(10)
            ->and($payload["next_appointments"])->toHaveCount(10);

        $performanceRes = macroToMilliSeconds($end - $start);
        expect($performanceRes)->toBeLessThan(30);
    });
});

describe("Creation tests", function() {
    it('successfully creates a user for valid input', function(User $user) {
        $initialCount = User::count();

        $response = $this->postJson(route('users.store'), $user->getAttributes());
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', ["email" => $user->email, "name" => $user->name]);
        $this->assertDatabaseCount("users", $initialCount + 1);
    })->with([
        fn() => User::factory()->make(),
        fn() => User::factory()->make(),
        fn() => User::factory()->make(),
    ]);

    it('gives a validation error for weak passwords', function(User $user) {
        $response = $this->postJson(route('users.store'), $user->getAttributes());
        $response->assertStatus(422);

        $res = $response->json();

        expect($res["errors"])->toHaveKey('password');

    })->with([
        fn() => User::factory()->make(["password" => "kort"]),
        fn() => User::factory()->make(["password" => "langeeeeeer"]),
        fn() => User::factory()->make(["password" => "Langeeeeeer"]),
        fn() => User::factory()->make(["password" => "Langeeeeeer1234"]),
        //fn() => User::factory()->make(),
    ]);

    it('creates user in a performant matter', function(\Illuminate\Support\Collection $users) {
        $initialCount = User::count();
        $users->makeVisible(["password"]);

        $start = hrtime(true);
        $response = $this->postJson(route('users.store'), ["users" => $users]);
        $end = hrtime(true);
        $performanceRes = macroToMilliSeconds($end - $start);

        expect($performanceRes)->toBeLessThan(100);
        $response->assertStatus(201);
        foreach ($users as $user) {
            $this->assertDatabaseHas("users", ["email" => $user->email, "name" => $user->name]);
        }
        $this->assertDatabaseCount("users", $initialCount + 100);
    })->with([
        fn() => User::factory(100)->make(["password" => "SuperStrongPassword123é!§è"])
    ]);
});



function macroToMilliSeconds($seconds) {
    return $seconds/1e+6;
}

function getDolores() {
    return User::factory()
        ->has(Appointment::factory()->past()->count(10))
        ->has(Appointment::factory()->future()->count(10))
        ->create([
            "name" => "Dolores",
            "email" => "dol@easi.net"
        ]);
}
