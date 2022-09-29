<?php

namespace Raphaborralho\Search;

use Illuminate\Support\Facades\Http;

class Search
{
    /**
     * Returns all hotels nearby from location
     * @param $latitude
     * @param $longitude
     * @param null $orderby
     * @return \Illuminate\Support\Collection
     */
    public static function getNearbyHotels($latitude, $longitude, $orderby = null): \Illuminate\Support\Collection
    {
        $data = self::dataSource()
            ->map(function ($item) use ($latitude, $longitude) {
                $item['pricepernight'] = end($item);
                $item['proximity'] = self::vincentyGreatCircleDistance(
                    (float) $latitude,
                    (float) $longitude,
                    (float) $item[1],
                    (float) $item[2]
                );
                return $item;
            })
            ->filter();
        if ($orderby === 'pricepernight') {
            return $data->sortBy('pricepernight')
                ->transform(function ($item) {
                    return $item[0].', '.number_format($item['proximity'], 2).' KM, '.$item['pricepernight'].' EUR';
                })
                ->values();
        } else {
            return $data->sortBy('proximity')
                ->transform(function ($item) {
                    return $item[0].', '.number_format($item['proximity'], 2).' KM, '.$item['pricepernight'].' EUR';
                })
                ->values();
        }
    }

    /**
     * Generate data source
     *
     * @return \Illuminate\Support\Collection|string
     */
    private static function dataSource(): string|\Illuminate\Support\Collection
    {
        try {
            $datasource = collect();
            $source = config('search.source');
            if (count($source)) {
                foreach ($source as $url) {
                    $datasource->add(Http::get($url)->collect('message'));
                }
            }
            return $datasource->flatten(1);
        } catch (\Throwable $throwable) {
            return 'no dataset';
        }
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Vincenty formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param int $earthRadius Mean earth radius in [m]
     * @return float|int Distance between points in [m] (same as earthRadius)
     */
    private static function vincentyGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371
    ): float|int {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}
