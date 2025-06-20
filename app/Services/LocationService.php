<?php

namespace App\Services;

use App\Models\Location;

class LocationService
{
    public function store(array $data): Location
    {
        return Location::create($data);
    }

    public function update(Location $location, array $data): Location
    {
        $location->update($data);
        return $location;
    }

    public function delete(Location $location): void
    {
        $location->delete();
    }
}
