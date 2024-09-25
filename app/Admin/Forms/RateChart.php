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

        // Check if the $rates collection is empty
        if ($rates->isEmpty()) {
            // Fetch default rates where location_id is null
            $defaultRates = RateList::whereNull('location_id')->get();


            // Loop through the default rates and create new rates for the user's location
            foreach ($defaultRates as $defaultRate) {
                RateList::create([
                    'rate' => $defaultRate->rate,
                    'snf' => $defaultRate->snf,
                    'fat' => $defaultRate->fat,
                    'location_id'=>$location_id
                ]);
            }

            // Fetch the newly created rates after updating
            $rates = RateList::where('location_id', $location_id)->get();
        }

        // Return the rates for the location
        return $rates;
    }
    public function check($location_id)
    {

        // Fetch rates for the user's location
        $rates = RateList::where('location_id', $location_id)->count();
        Log::channel('callvcal')->info('rates: '.$rates);

        // Check if the $rates collection is empty
        if ($rates == 0) {
            // Fetch default rates where location_id is null
            $defaultRates = RateList::whereNull('location_id')->get();

            Log::channel('callvcal')->info('defaultRates: '.count($defaultRates));

            // Loop through the default rates and create new rates for the user's location
            foreach ($defaultRates as $defaultRate) {
            $res=RateList::create([
                    'rate' => $defaultRate->rate,
                    'snf' => $defaultRate->snf,
                    'fat' => $defaultRate->fat,
                    'location_id'=>$location_id
                ]);
                Log::channel('callvcal')->info('defaultRates:res: '.json_encode($res));
            }
        }
    }
}
