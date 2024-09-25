<?php

namespace App\Admin\Forms;

use App\Models\Location;
use App\Models\RateList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use OpenAdmin\Admin\Widgets\Form;
use Symfony\Component\Translation\Loader\CsvFileLoader;

class RateImportForm extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Import CSV';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        // Check if the file is uploaded
        if ($request->hasFile('file')) {
            try {
                // Get the uploaded file's temporary path
                $csvFile = $request->file('file')->getPathname();

                // Debugging: Check if the file exists and is readable
                if (!file_exists($csvFile)) {
                    Log::error("File does not exist at: $csvFile");
                    admin_toastr('Uploaded file not found.', 'error');
                    return back();
                }

                if (!is_readable($csvFile)) {
                    Log::error("File is not readable: $csvFile");
                    admin_toastr('Uploaded file is not readable.', 'error');
                    return back();
                }

                // Optional: Check the contents of the file (log first 500 characters)
                $contents = file_get_contents($csvFile);
                Log::info("CSV file contents: " . substr($contents, 0, 500)); // Log the first 500 characters for debugging

                // Create a CSV reader instance from the file path
                $csv = Reader::createFromPath($csvFile, 'r');

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

                //headers are SNF
                // $records are fats
                for ($h = 1; $h < count($headers); $h++) {
                    for ($r = 0; $r < count($records); $r++) {
                        $snf = $headers[$h];
                        $fat = ($records[$r])[$headers[0]];
                        $rate = ($records[$r])[$headers[$h]];
                        $model = RateList::updateOrCreate(
                            [
                                'snf' => $snf,
                                'fat' => $fat,
                                'location_id'=>$request->location_id
                            ],
                            [
                                'rate' => $rate
                            ]
                        );
                        Log::channel('callvcal')->info('CSV model: ' . json_encode($model));
                    }
                }

                // Log both headers and records
                // Log::channel('callvcal')->info('CSV Headers: ' . json_encode($headers));
                // Log::channel('callvcal')->info('CSV Records: ' . json_encode($records));

                // Success message
                admin_toastr('CSV processed successfully.', 'success');
            } catch (\Exception $e) {
                // Handle exceptions
                Log::error('CSV processing failed: ' . $e->getMessage());
                admin_toastr('Failed to process the CSV file.', 'error');
            }
        } else {
            admin_toastr('No file uploaded.', 'error');
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
    /**
     * Build a form here.
     */
    public function form()
    {
        $this->select('location_id', "Choose Location")->options(Location::all()->pluck('location_id', 'location_id'))->required();
        $this->file('file', 'Select CSV File')->rules('file|mimes:csv,xls,xlsx')->required();
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [];
    }
}
