<?php

namespace App\Models;



class RateCalculation
{
    public $clr = 0.0;
    public $fat = 0.0;
    public $rate = 0.0;
    public $litres = 0;
    public $amt = 0.0;
    public $snf = 0.0;

    public function __construct($clr = 0.0, $fat = 0.0, $rate = 0.0, $litres = 0, $amt = 0.0, $snf = 0.0)
    {
        $this->clr = $clr;
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

        return ($this->clr / 4) + (0.21 * $this->fat) + 0.36;
    }

    public function getFat()
    {
        if ($this->fat > 0) return $this->fat;

        return ($this->snf - ($this->clr / 4) - 0.36) / 0.21;
    }

    public function getClr()
    {
        if ($this->clr > 0) return $this->clr;

        return (4 * $this->snf) - (4 * 0.21 * $this->fat) - (4 * 0.36);
    }

    public function calRate()
    {
        $this->clr = round($this->getClr(), 1);
        $this->fat = round($this->getFat(), 1);
        $this->snf = round($this->getSnf(), 1);

        $rates = RateList::all();

        if ($rates->isEmpty()) {
            echo "Rates chart is empty";
            return 0.0;
        }

        $rateModel = $rates->first(function ($rateModel) {
            return round($rateModel->snf, 1) == round($this->snf, 1) &&
                   round($rateModel->fat, 1) == round($this->fat, 1);
        });

        $this->rate = $rateModel ? $rateModel->rate : 0.0;
        $this->amt = $this->litres * $this->rate;

        return $this->amt;
    }
}
