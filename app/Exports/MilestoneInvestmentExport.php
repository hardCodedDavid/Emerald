<?php

namespace App\Exports;

use App\Http\Controllers\Globals;
use App\MilestoneInvestment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MilestoneInvestmentExport implements FromArray, WithHeadings, ShouldAutoSize
{

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'User',
            'Amount',
            'Farm',
            'Maturity Date',
            'Maturity Status',
            'Units',
            'Days Remaining',
            'Milestones',
            'Total ROI',
            'Total Returns',
            'Status'
        ];
    }

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return MilestoneInvestment::latest()->get()->map(function($investment){
            $finalDate = null;
            foreach($investment->milestoneDates() as $key => $date) {
                if (count($investment->milestoneDates()) == ($key + 1))
                    $finalDate = $date;
            }
            return [
                'user' => $investment->user->name,
                'amount' => '₦' . number_format($investment->amount_invested,2),
                'farm' => $investment->farm->title,
                'maturity' =>  $finalDate->format('l jS F, Y - h:i A'),
                'maturity_status' => ucwords($investment->maturity_status),
                'units' => $investment->units,
                'days_remaining' => $finalDate->diffInDays(now()),
                'milestone' => $investment->farm->milestone,
                'total_roi' => '₦' .number_format($investment->getTotalROI(), 2),
                'total_returns' => '₦' .number_format($investment->getTotalROI() + $investment->amount_invested ,2),
                'status' => $investment->status,
            ];
        })->toArray();
    }
}
