<?php namespace App\Models;

use App\Admin\Forms\RateChart;

class RateCalculation
{
    public $clr = 0;
    public $fat = 0;
    public $rate = 0;
    public $litres = 0;
    public $amt = 0;
    public $snf = 0;
    public $shift = 0;
    public $type = 0;

    public function __construct($clr = 0, $fat = 0, $rate = 0, $litres = 0, $amt = 0, $snf = 0,$shift=null,$type=null)
    {
        $this->clr = $clr;
        $this->shift = $shift;
        $this->type = $type;
        $this->fat = $fat;
        $this->rate = $rate;
        $this->litres = $litres;
        $this->amt = $amt;
        $this->snf = $snf;

        $this->calRate();
    }

    public function getSnf()
    {
        if ($this->snf > 0) return $this->snf;

        $snf = ($this->clr / 4) + (0.21 * $this->fat) + 0.36;
        return $snf == (int)$snf ? (int)$snf : round($snf, 2);
    }

    public function getFat()
    {
        if ($this->fat > 0) return $this->fat;

        $fat = ($this->snf - ($this->clr / 4) - 0.36) / 0.21;
        return $fat == (int)$fat ? (int)$fat : round($fat, 2);
    }

    public function getClr()
    {
        if ($this->clr > 0) return $this->clr;

        $clr = (4 * $this->snf) - (4 * 0.21 * $this->fat) - (4 * 0.36);
        return $clr == (int)$clr ? (int)$clr : round($clr, 2);
    }

    public function calRate()
    {
        // Get and ensure clr, fat, and snf are positive
        $this->clr = max(0, $this->getClr());
        $this->fat = max(0, $this->getFat());
        $this->snf = max(0, $this->getSnf());
    
        // Get rates from RateChart
        $rates = (new RateChart())->data();
    
        // Check if rates chart is empty
        if ($rates->isEmpty()) {
            echo "Rates chart is empty";
            return 0;
        }
    
        // Find the rate model matching snf and fat values
        $rateModel = $rates->first(function ($rateModel) {
            return round($rateModel->snf, 1) == round($this->snf, 1) &&
                   round($rateModel->fat, 1) == round($this->fat, 1)&&($rateModel);
        });
    
        // Ensure the rate is non-negative, otherwise default to 0
        $this->rate = max(0, $rateModel ? $rateModel->rate : 0);
    
        // Ensure litres is positive before calculating the amount
        $this->litres = max(0, $this->litres);
        $this->amt = $this->litres * $this->rate;
    
        return $this->amt;
    }
    
}
?>