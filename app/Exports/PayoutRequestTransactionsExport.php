<?php

namespace App\Exports;

use App\Http\Controllers\Globals;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayoutRequestTransactionsExport implements FromArray, WithHeadings, ShouldAutoSize
{

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return Transaction::where(['type'=>'payouts','status'=>'pending'])->get()->map(function($transaction){
            $member = Globals::getUserByEmail($transaction->user);
            $bank = Globals::getBank($transaction->bank);
            $bankDetails = '';
            if($transaction->type == 'payouts'){
                if($bank != null){
                    $bankDetails = $bank->bank_name . '-' . $bank->account_name . '-' . $bank->account_number;
                }
            }
            return [
                'user' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'amount' => '₦' . number_format($transaction->amount,2),
                'bank' => $bankDetails,
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
            'User',
             'Email',
            'Phone',
            'Amount',
            'Bank',
            'Date',
        ];
    }
}

