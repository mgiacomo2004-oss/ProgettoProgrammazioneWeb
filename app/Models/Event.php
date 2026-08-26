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
        return $this->event_date->isBefore(today());
    }

    public function isCancelled() 
    {
        return $this->event_date->isToday() && $this->users()->count() === 0;
    }

    public function isInProgress()
    {
        return $this->event_date->isToday() 
            && $this->users()->count() > 0;
    }

    public function isClosed()
    {
        return !$this->isFinished()
            && !$this->isCancelled()
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
            && !$this->isCancelled()
            && !$this->isClosed()
            && !$this->isFull()
            && !$this->isInProgress();
    }

    public function getStatus()
    {
        if ($this->isFinished()) {
            return 'finished';
        }

        if ($this->isCancelled()) {
            return 'cancelled';
        }

        if ($this->isInProgress()) {
            return 'in_progress';
        }

        if ($this->isClosed()) {
            return 'closed';
        }
        
        if ($this->isFull()) {
            return 'full';
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
            'cancelled' => 'ANNULLATO',
            'full' => 'PIENO',
            'closed' => 'CHIUSO',
            'in_progress' => 'IN CORSO',
            default => 'APERTO',
        };
    }

    public function statusColor()
    {
        return match ($this->getStatus()) {
            'finished' => 'text-gray-600',
            'cancelled' => 'text-gray-500',
            'full' => 'text-red-600',
            'closed' => 'text-orange-600',
            'in_progress' => 'text-blue-500',
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
