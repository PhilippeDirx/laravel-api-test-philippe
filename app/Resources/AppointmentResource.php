<?php

namespace App\Resources;

use App\Models\Appointment;

class AppointmentResource
{
    /** @var Appointment */
    private $appointment;

    /**
     * Construct a new instance of the Resource
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Returns the public properties of the appointment for API response purposes.
     * @return array
     */
    public function toArray() : array
    {
        return $this->appointment->only([
            'id', 'title', 'event_date', 'description',
        ]);
    }
}