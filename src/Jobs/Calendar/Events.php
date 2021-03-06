<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017, 2018  Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Seat\Eveapi\Jobs\Calendar;

use Seat\Eveapi\Jobs\EsiBase;
use Seat\Eveapi\Models\Calendar\CharacterCalendarEvent;

/**
 * Class Events
 * @package Seat\Eveapi\Jobs\Calendar
 */
class Events extends EsiBase
{
    /**
     * @var string
     */
    protected $method = 'get';

    /**
     * @var string
     */
    protected $endpoint = '/characters/{character_id}/calendar/';

    /**
     * @var string
     */
    protected $version = 'v1';

    /**
     * @var array
     */
    protected $tags = ['character', 'events'];

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {

        $events = $this->retrieve([
            'character_id' => $this->getCharacterId(),
        ]);

        collect($events)->each(function ($event) {

            CharacterCalendarEvent::firstOrNew([
                'character_id' => $this->getCharacterId(),
                'event_id'     => $event->event_id,
            ])->fill([
                'event_date'     => carbon($event->event_date),
                'title'          => $event->title,
                'importance'     => $event->importance,
                'event_response' => $event->event_response,
            ])->save();
        });
    }
}