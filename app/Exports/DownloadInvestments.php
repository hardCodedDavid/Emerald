<?php

namespace App\Exports;

use App\Http\Controllers\Globals;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadInvestments implements FromArray, WithHeadings, ShouldAutoSize
{
    public $investments;

    /**
     * DownloadInvestments constructor.
     */
    public function __construct($investments)
    {
        $this->investments = $investments;
    }


    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'User',
            'Amount',
            'Farmlist',
            'Maturity Date',
            'Maturity Status',
            'Units',
            'Days Remaining',
            'Expected Returns',
            'Status'
        ];
    }

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return $this->investments->map(function($investment){
                $cur = strtotime(date('Y-m-d H:i:s'));
                $mat = strtotime($investment->maturity_date);
                $diff = $mat - $cur;
                $farm = Globals::getFarmList($investment->farmlist);
                $interest = $investment->amount_invested*($farm->interest/100);
                $add = $investment->amount_invested+$interest;
                $member = Globals::getUserByEmail($investment->user);

                return [
                    'user' => $member->name,
                    'amount' => '₦' . number_format($investment->amount_invested,2),
                    'farmlist' => ucwords(Globals::getFarmlist($investment->farmlist)->title),
                    'maturity' =>  $investment->maturity_date != null ? date('M d, Y h:i A', strtotime($investment->maturity_date)) : 'Pending',
                    'maturity_status' => ucwords($investment->maturity_status),
                    'units' => $investment->units,
                    'days' => ($investment->maturity_date == null) ? '0' : ( $investment->maturity_status == 'pending' ? round((($diff/24)/60)/60) : '0'),
                    'returns' => '₦' . number_format($add,2),
                    'status' => $investment->status
                ];
            })->toArray();
    }
}
