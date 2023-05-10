<?php

namespace App\Imports;

use App\BatchPayout;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class BatchPayoutImport implements ToModel, WithCalculatedFormulas
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $allNull = true;
        for ($i = 0; $i < count($row); $i++) if ($row[$i]) $allNull = false;
        if (!$allNull) {
            return new BatchPayout([
                'batch' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'email' => $row[2] ?? null,
                'phone' => $row[3] ?? null,
                'units' => $row[4] ?? null,
                'amount_invested' => $row[5] ?? null,
                'expected_returns' => $row[6] ?? null,
                'farm_cycle' => $row[7] ?? null,
                'payment_date' => $row[8] ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$row[8]) : null,
                'queue' => $row[9] ?? null,
            ]);
        }
    }
}
