<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\RateList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class RateImporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $locations;
    protected $location_type;
    protected $types;
    protected $shifts;
    protected $path;

    /**
     * Create a new job instance.
     */
    public function __construct($locations, $location_type, $types, $shifts, $path)
    {
        $this->locations = $locations;
        $this->location_type = $location_type;
        $this->types = $types;
        $this->path = $path;
        $this->shifts = $shifts;
    }

    public function handle()
    {
        $location_type = $this->location_type;
        $locations = $this->locations ?? [];
        $types = $this->types;
        $shifts = $this->shifts;
        $path = $this->path;

        // Check if the file is uploaded
        try {
            Log::channel('callvcal')->info('CSV processing : ');
            $csv = Reader::createFromPath($path, 'r');

            // Set the header offset to 0 (indicating the first row contains headers)
            $csv->setHeaderOffset(0);

            // Get the headers (keys)
            $headers = $csv->getHeader();
            Log::info('CSV Headers: ' . json_encode($headers));

            // Initialize an array to store records (values)
            $records = [];

            // Loop through each record in the CSV
            foreach ($csv->getRecords() as $record) {
                // Skip empty records
                if ($this->isRecordEmpty($record)) {
                    continue;
                }

                // Append each non-empty record to the records array
                $records[] = $record;
            }
            // $records are fats
            for ($h = 1; $h < count($headers); $h++) {
                Log::channel('callvcal')->info('h: ' . json_encode($h));
                for ($r = 0; $r < count($records); $r++) {
                    Log::channel('callvcal')->info('r: ' . json_encode($r));
                    $snf = $headers[$h];
                    $fat = ($records[$r])[$headers[0]];
                    $rate = ($records[$r])[$headers[$h]];

                    if ($location_type == '0') {
                        $locations = Location::all()->pluck('location_id', 'location_id');
                    }
                    foreach ($locations as $location) {
                        Log::channel('callvcal')->info('location: ' . json_encode($location));
                        // foreach ($shifts as $shift) {
                        //     Log::channel('callvcal')->info('shift: ' . json_encode($shift));
                        //     foreach ($types as $type) {
                        //         Log::channel('callvcal')->info('type: ' . json_encode($type));
                        //         $search = [
                        //             'snf' => $snf,
                        //             'fat' => $fat,
                        //             'location_id' => $location,
                        //             'shift' => $shift,
                        //             'type' => $type,
                        //         ];
                        //         RateList::updateOrCreate(
                        //             $search,
                        //             [
                        //                 'rate' => $rate
                        //             ]
                        //         );
                        //         Log::channel('callvcal')->info('search: ' . json_encode($search));
                        //     }
                        // }



                        foreach ($shifts as $shift) {
                            $search = [
                                'snf' => $snf,
                                'fat' => $fat,
                                'location_id' => $location,
                                'shift' => $shift,

                            ];
                            $model = RateList::updateOrCreate(
                                $search,
                                [
                                    'rate' => $rate
                                ]
                            );
                            foreach ($types as $type) {
                                $model->{$type} = $rate;
                            }
                            $model->save();
                        }

                    }
                }
            }

            // Log both headers and records
            // Log::channel('callvcal')->info('CSV Headers: ' . json_encode($headers));
            // Log::channel('callvcal')->info('CSV Records: ' . json_encode($records));

            // Success message
        } catch (\Exception $e) {
            // Handle exceptions
            Log::error('CSV processing failed: ' . $e->getMessage());
        }

        return back();
    }
    /**
     * Helper function to check if a CSV record is empty.
     * This function assumes the record is an associative array.
     */
    private function isRecordEmpty($record)
    {
        foreach ($record as $field) {
            if (!empty(trim($field))) {
                return false; // Found a non-empty field
            }
        }
        return true; // All fields are empty
    }
}
