<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'event_date',
        'registration_deadline',
        'max_participants',
        'cost'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'registration_deadline' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function isFinished()
    {
        return $this->event_date->isPast();
    }

    public function isClosed()
    {
        return !$this->isFinished()
            && $this->registration_deadline
            && $this->registration_deadline->isPast();
    }

    public function isFull()
    {
        return $this->users()->count() >= $this->max_participants;
    }

    public function isOpen()
    {
        return !$this->isFinished()
            && !$this->isClosed()
            && !$this->isFull();
    }

    public function getStatus()
    {
        if ($this->isFinished()) {
            return 'finished';
        }

        if ($this->isFull()) {
            return 'full';
        }

        if ($this->isClosed()) {
            return 'closed';
        }

        return 'open';
    }
    public function availableSpots()
    {
        return $this->max_participants - $this->users()->count();
    }

    public function displayStatus()
    {
        return match ($this->getStatus()) {
            'finished' => 'CONCLUSO',
            'full' => 'PIENO',
            'closed' => 'CHIUSO',
            default => 'APERTO',
        };
    }

    public function statusColor()
    {
        return match ($this->getStatus()) {
            'finished' => 'text-gray-600',
            'full' => 'text-red-600',
            'closed' => 'text-orange-600',
            default => 'text-green-600',
        };
    }

    public function formattedEventDate()
    {
        return $this->event_date->format('d/m/Y');
    }

    public function formattedRegistrationDeadline()
    {
        return $this->registration_deadline?->format('d/m/Y');
    }

}
