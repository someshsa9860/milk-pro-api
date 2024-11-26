<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\RateList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $location_type = $this->location_type;
            $locations = $this->getLocations($location_type);
            $types = $this->types;
            $shifts = $this->shifts;

            $csv = Reader::createFromPath($this->path, 'r');
            $csv->setHeaderOffset(0);

            // Retrieve headers and records
            $headers = $csv->getHeader();
            $records = iterator_to_array($csv->getRecords(), false);

            // Filter and validate records
            $filteredRecords = array_filter($records, fn($record) => !$this->isRecordEmpty($record));
            Log::info('Total valid records: ' . count($filteredRecords));

            // Process records in batches
            $batchSize = 100; // Adjust based on your dataset
            $chunks = array_chunk($filteredRecords, $batchSize);

            foreach ($chunks as $chunk) {
                $this->processChunk($chunk, $headers, $locations, $types, $shifts);
            }

            Log::info('RateImporter completed successfully.');
        } catch (\Exception $e) {
            Log::error('RateImporter failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a chunk of records.
     */
    private function processChunk($chunk, $headers, $locations, $types, $shifts)
    {
        $data = [];
        foreach ($chunk as $record) {
            for ($h = 1; $h < count($headers); $h++) {
                $snf = $headers[$h];
                $fat = $record[$headers[0]];
                $rate = $record[$headers[$h]];

                foreach ($locations as $location) {
                    foreach ($shifts as $shift) {
                        $row = [
                            'snf' => $snf,
                            'fat' => $fat,
                            'location_id' => $location,
                            'shift' => $shift,
                            'rate' => $rate,
                        ];

                        foreach ($types as $type) {
                            $row[$type] = $rate;
                        }

                        $data[] = $row;
                    }
                }
            }
        }

        // Use bulk insert or update
        $this->bulkUpsert($data);
    }

    /**
     * Perform bulk upsert for rates.
     */
    private function bulkUpsert($data)
    {
        // Define unique keys for upsert
        $uniqueKeys = ['snf', 'fat', 'location_id', 'shift'];

        // Perform bulk upsert
        RateList::upsert($data, $uniqueKeys, ['rate']);
        Log::info('Processed a batch of ' . count($data) . ' rates.');
    }

    /**
     * Retrieve locations based on location type.
     */
    private function getLocations($location_type)
    {
        if ($location_type == '0') {
            return Location::all()->pluck('location_id')->toArray();
        }
        return $this->locations ?? [];
    }

    /**
     * Helper function to check if a CSV record is empty.
     */
    private function isRecordEmpty($record)
    {
        foreach ($record as $field) {
            if (!empty(trim($field))) {
                return false;
            }
        }
        return true;
    }
}
