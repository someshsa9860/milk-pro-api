<?php

namespace App\Admin\Forms;

use App\Jobs\RateImporter;
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
        $location_type = $request->location_type;
        $locations = $request->locations ?? [];
        $types = $request->types;
        $shifts = $request->shifts;
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
                $csvFile = $request->file('file');

                $newFilePath =  time() . '_.' . $csvFile->getClientOriginalExtension();  // Optional: Use timestamp to avoid duplicate names

                // Store the file in the storage/app/csv directory (or any other directory you specify)
                $csvFile->move(public_path('csv'), $newFilePath);
                // $csvFile->storeAs('csv', public_path($newFilePath));  // stores file in storage/app/csv/

                // To access the file later, you can use the following path:
                $path = public_path('csv' . '/'.$newFilePath);



                // $locations,$location_type,$types,$shifts,$headers,$records
                RateImporter::dispatchAfterResponse($locations, $location_type, $types, $shifts,  $path);
                admin_toastr('CSV processed successfully. Please wait for 10 min, we are working in background', 'success');
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
     * Build a form here.
     */
    public function form()
    {

        $this->radio('location_type', 'Locations')->options([
            '0' => 'All',
            '1' => 'Choose'
        ])->when(1, function (Form $form) {
            $this->multipleSelect('locations', "Choose Location")->options(Location::all()->pluck('location_id', 'location_id'));
        })->required();
        $this->checkbox('types', "Milk Type")->options([
            'cow' => "Cow",
            'buffalo' => "Buffalo",
            'mixed' => "Mixed",
        ]);
        $this->checkbox('shifts', "Shift")->options([
            'morning' => "Morning",
            'evening' => "Evening",
        ]);


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
