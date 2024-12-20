<?php

namespace App\Admin\Forms;

use App\Models\RateList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAdmin\Admin\Widgets\Form;

class RateChart extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Rates Chart';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());

        admin_success('Processed successfully.');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('fat', "FAT")->rules('required');
        $this->text('snf', "SNF")->rules('required');
        $this->text('rate', "Rate")->rules('required');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data(): object
    {
        $location_id = auth()->user()->location_id;

        // Fetch rates for the user's location
        $rates = RateList::where('location_id', $location_id)->get();

        

        // Return the rates for the location
        return $rates;
    }
    public function check($location_id)
    {
        // Fetch rates count for the user's location
        $ratesCount = RateList::where('location_id', $location_id)->count();
        Log::channel('callvcal')->info('rates: ' . $ratesCount);
    
        // Check if no rates exist for the location
        if ($ratesCount == 0) {
        Log::channel('callvcal')->info('importing default rats for : ' . $location_id);
        // Fetch default rates where location_id is "New Rate chart"
            RateList::where('location_id', 'New Rate chart')
                ->chunk(100, function ($defaultRates) use ($location_id) {
                    foreach ($defaultRates as $defaultRate) {
                        // Replicate the model and set the new location_id
                        $newRate = $defaultRate->replicate();
                        $newRate->location_id = $location_id;
    
                        // Save the new rate
                        $newRate->save();
    
                        // Log the created rate
                    }
                });

                Log::channel('callvcal')->info('imported default rats for : ' . $location_id. ' Is : '.RateList::where('location_id', $location_id)->count());

        }
    }
    
}
