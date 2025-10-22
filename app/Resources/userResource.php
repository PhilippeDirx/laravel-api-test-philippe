<?php

namespace App\Resources;

use App\Models\User;

class userResource
{
    /** @var User */
    private $user;

    /**
     * Constructor for the resource.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the user variables, public to the API.
     * @return array
     */
    public function toArray(): array
    {
        return $this->user->only([
            'id', 'name', 'email'
        ]);
    }

    /**
     * Returns the User for detail view, including appointments.
     * @return array
     */
    public function detail(): array
    {
        return array_merge($this->toArray(),
            [
                'created_at' => $this->user->created_at,
                'appointments' => $this->appointments()->toArray(),
                'appointment_history' => $this->pastAppointments()->toArray(),
                'next_appointments' => $this->futureAppointments()->toArray(),
            ]);
    }

    /**
     * Returns the appointments for the user, mapped via an AppointmentResource.
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    protected function appointments()
    {
        return $this->user->appointments()
            ->limit(10)
            ->get()
            ->map(function($appointment) {
            return (new AppointmentResource($appointment))->toArray();
        });
    }

    /**
     * Returns the appointments for the user, mapped via an AppointmentResource.
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    protected function pastAppointments()
    {
        return $this->user->pastAppointments()
            ->limit(10)
            ->orderBy('event_date', 'DESC')
            ->get()
            ->map(function($appointment) {
            return (new AppointmentResource($appointment))->toArray();
        });
    }

    /**
     * Returns the appointments for the user, mapped via an AppointmentResource.
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    protected function futureAppointments()
    {
        return $this->user->futureAppointments()
            ->limit(10)
            ->orderBy('event_date', 'ASC')
            ->get()
            ->map(function($appointment) {
            return (new AppointmentResource($appointment))->toArray();
        });
    }

}