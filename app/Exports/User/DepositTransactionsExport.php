<?php

namespace App\Exports\User;

use App\Http\Controllers\Globals;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepositTransactionsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return Transaction::where(['type'=>'deposits'])->where('user', auth()->user()->email)->get()->map(function($transaction){
            $member = Globals::getUserByEmail($transaction->user);

            return [
                'amount' => '₦' . number_format($transaction->amount,2),
                'type' => ucwords($transaction->type),
                'status' => ucwords($transaction->status),
                'date' => $transaction->created_at->format('M d, Y')
            ];
        })->toArray();
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'Amount',
            'Type',
            'Status',
            'Date'
        ];
    }
}

