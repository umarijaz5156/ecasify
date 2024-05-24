<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DateTimeZone;
use App\Models\Timezone;
use DateTime;
class TimezonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        foreach ($timezones as $timezone) {
            $utcOffset = (new DateTime('now', new DateTimeZone($timezone)))->format('P');
            Timezone::create([
                'timezone' => $timezone,
                'utc_offset' => $utcOffset,
            ]);
        }
    }
}
