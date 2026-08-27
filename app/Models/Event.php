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
        'start_time',
        'end_time',
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
        return now()->greaterThanOrEqualTo($this->endDateTime());
    }

    public function isCancelled() 
    {
        return now()->greaterThanOrEqualTo($this->startDateTime())
            && now()->lessThan($this->endDateTime())
            && $this->users()->count() === 0;
    }

    public function isInProgress()
    {
        return now()->greaterThanOrEqualTo($this->startDateTime())
            && now()->lessThan($this->endDateTime())
            && $this->users()->count() > 0;
    }

    public function isClosed()
    {
        return !$this->isFinished()
            && !$this->isCancelled()
            && $this->registration_deadline
            && now()->greaterThanOrEqualTo($this->registration_deadline->copy()->startOfDay())
            && now()->lessThan($this->startDateTime());
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

    private function startDateTime()
    {
        return $this->event_date->copy()->setTimeFromTimeString($this->start_time);
    }

    private function endDateTime()
    {
        return $this->event_date->copy()->setTimeFromTimeString($this->end_time);
    }

}
