<?php

namespace App\Exports;

use App\Http\Controllers\Globals;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepositTransactionsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return Transaction::where(['type'=>'deposits'])->latest()->get()->map(function($transaction){
            $member = Globals::getUserByEmail($transaction->user);

            return [
            'user' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'amount' => '₦' . $transaction->amount,
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
          'User',
          'Email',
          'Phone',
          'Amount',
          'Type',
          'Status',
          'Date'
        ];
    }
}
