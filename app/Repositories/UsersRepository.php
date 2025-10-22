<?php

namespace App\Repositories;

use App\Models\User;
use App\Resources\userResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UsersRepository
{
    /** @var string The resource that is used for results of this repository. */
    private $resource = UserResource::class;

    public function fetch(Request $request)
    {
        $query = $this->query();

        if ($name = $request->get('name')) {
            $query->where('name', 'like', "%$name%");
        }

        if ($email = $request->get('email')) {
            $query->where('email', 'like', "%$email%");
        }

        return $query->paginate(page: $request->get('page'))
            ->through(function ($user) {
            return (new $this->resource($user))->toArray();
        });
    }



    /**
     * Returns a query instance for User model.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query() : Builder
    {
        return User::query();
    }


}