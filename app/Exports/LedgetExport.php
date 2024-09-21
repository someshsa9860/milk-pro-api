<?php

namespace App\Exports;

use App\Models\Order;
Use OpenAdmin\Admin\Grid\Exporters\ExcelExporter;

class LedgetExport implements ExcelExporter
{
    protected $columns = [
        'bill_no','total','cow_litres','buffalo_litres','mixed_litres'

    ];
}
