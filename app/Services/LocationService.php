<?php

namespace App\Services;

use App\Models\Location;

class LocationService
{
    public function store(array $data): Location
    {
        $data['description'] = $this->sanitizeDescription($data['description'] ?? null);
        return Location::create($data);
    }

    public function update(Location $location, array $data): Location
    {
        if (array_key_exists('description', $data)) {
            $data['description'] = $this->sanitizeDescription($data['description']);
        }
        $location->update($data);
        return $location->fresh('campus');
    }

    public function delete(Location $location): void
    {
        $location->delete();
    }

    private function sanitizeDescription(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }
        $allowed = '<p><b><strong><i><em><u><ul><ol><li><br>';
        $clean = strip_tags($html, $allowed);
        return preg_replace('/<(\w+)[^>]*>/', '<$1>', $clean);
    }
}
