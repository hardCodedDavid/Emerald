<?php

namespace App\Exports;

use App\Http\Controllers\Globals;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReferralTransactionsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return Transaction::where(['type'=>'referral'])->latest()->get()->map(function($transaction){
            $member = Globals::getUserByEmail($transaction->user);

            return [
                'user' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'amount' => '₦' . number_format($transaction->amount,2),
                'type' => ucwords($transaction->type),
                'status' => ucwords($transaction->status)
            ];
        })->toArray();
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'User',
            'Email',
            'Phone',
            'Amount',
            'Type',
            'Status'
        ];
    }
}
