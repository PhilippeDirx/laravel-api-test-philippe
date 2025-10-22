<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Appointment;
use App\Models\User;
use App\Repositories\UsersRepository;
use App\Resources\userResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return (new UsersRepository())->fetch($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $request->validate($request->rules());

        $result = $request->save();

        if ($result instanceof User)
            return response()
                ->json(['users' => [(new UserResource($result))->toArray()]])
                ->setStatusCode(201);

        return response()
            ->json([
            'users' => collect($result)->map(function ($user) {
                return (new userResource($user))->toArray();
            })
        ])->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return (new UserResource($user))->detail();
    }
}
