<?php

namespace App\Admin\Extensions;

use OpenAdmin\Admin\Grid\Exporters\ExcelExporter;

class PageExporter extends ExcelExporter
{
    protected $fileName = 'Article list.xlsx';

    protected $columns = [
        'order_date_time',
        'bill_no',
        'shift',
        'total',
        'remark',
        'advance',
        'customer_id',
        'user_id',
        'cow_litres',
        'cow_fat',
        'cow_clr',
        'cow_snf',
        'cow_rate',
        'cow_amt',
        'buffalo_litres',
        'buffalo_fat',
        'buffalo_clr',
        'buffalo_snf',
        'buffalo_rate',
        'buffalo_amt',
        'mixed_litres',
        'mixed_fat',
        'mixed_clr',
        'mixed_snf',
        'mixed_rate',
        'mixed_amt',
    ];
}
