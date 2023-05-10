<?php

namespace App\Exports\User;

use App\Http\Controllers\Globals;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllTransactionsExport implements FromArray, WithHeadings, ShouldAutoSize
{

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return Transaction::orderBy('id', 'desc')->where('user', auth()->user()->email)->get()->map(function($transaction){
            $member = Globals::getUserByEmail($transaction->user);
            $bank = Globals::getBank($transaction->bank);
            $bankDetails = '';
            if($transaction->type == 'payouts'){
                if($bank != null){
                    $bankDetails = $bank->bank_name . '-' . $bank->account_name . '-' . $bank->account_number;
                }
            }

            return [
                'amount' => '₦' . number_format($transaction->amount,2),
                'type' => $transaction->type,
                'bank' => $bankDetails,
                'status' => $transaction->status,
                'date' => date('M d, Y', strtotime($transaction->created_at))
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
            'Bank',
            'Status',
            'Date'
        ];
    }
}

