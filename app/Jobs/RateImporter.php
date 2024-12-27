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

            // Process records in small chunks
            $chunkSize = 10; // Define a manageable chunk size
            $chunks = array_chunk($filteredRecords, $chunkSize);

            foreach ($chunks as $index => $chunk) {
                Log::info("Processing chunk " . ($index + 1) . " of " . count($chunks) . ".");
                $this->processChunk($chunk, $headers, $locations, $types, $shifts);
            }

            Log::info('RateImporter completed successfully.');
        } catch (\Exception $e) {
            Log::error('RateImporter failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    /**
     * Process a chunk of records.
     */
    private function processChunk($chunk, $headers, $locations, $types, $shifts)
    {
        $data = [];
        $chunkSize = count($chunk);

        try {
            foreach ($chunk as $index => $record) {
                for ($h = 1; $h < count($headers); $h++) {
                    $snf = $headers[$h];
                    $fat = $record[$headers[0]];
                    $rate = $record[$headers[$h]];

                    foreach ($locations as $location) {
                        if(isset($location)&&($location!='')){
                            foreach ($shifts as $shift) {
                                $row = [
                                    'snf' => (float)$snf,
                                    'fat' => (float)$fat,
                                    'location_id' => $location,
                                    'shift' => (string)$shift,
                                    'rate' => (float)$rate,
                                ];
    
                                foreach ($types as $type) {
                                    $row[$type] = (float)$rate;
                                }
    
                                $data[] = $row;
                            }
                        }
                    }
                }

                // Log progress within chunk
                if ($index % 10 === 0) {
                    Log::info("Processed record {$index}/{$chunkSize} in current chunk.");
                }
            }

            // Use bulk insert or update
            $this->bulkUpsert($data);

            Log::info("Successfully processed a chunk of {$chunkSize} records.");
        } catch (\Throwable $e) {
            Log::error("Error processing chunk: {$e->getMessage()}", [
                'exception' => $e,
                'chunk' => $chunk,
            ]);
        }
    }

    /**
     * Perform bulk upsert for rates.
     */
   private function bulkUpsert($data)
{
    // Collect all possible fields
    $defaultUpdatableFields = ['rate', 'cow', 'buffalo', 'mixed'];
    
    // Determine fields to update dynamically
    $updateFields = [];
    foreach ($data as $row) {
        foreach ($defaultUpdatableFields as $field) {
            if (array_key_exists($field, $row)) {
                $updateFields[] = $field;
            }
        }
    }

    // Remove duplicate fields
    $updateFields = array_unique($updateFields);

    // Define unique keys for matching records
    $uniqueKeys = ['fat', 'location_id', 'shift'];

    // Perform the upsert operation
    RateList::upsert($data, $uniqueKeys, $updateFields);

    Log::info('Bulk upserted ' . count($data) . ' records with fields: ' . implode(', ', $updateFields));
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
