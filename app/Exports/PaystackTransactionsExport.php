<?php

namespace App\Exports;

use App\Verified;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaystackTransactionsExport implements FromArray, WithHeadings, ShouldAutoSize
{

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return Verified::whereNotNull('user_id')->latest()->get()->map(function($paystack){

            return [
                'user' => $paystack->user->name,
                'email' => $paystack->user->email,
                'number' => $paystack->user->phone,
                'amount' => '₦' . number_format($paystack->amount,2),
                'type' => ucwords($paystack->type),
                'reference' =>  $paystack->reference,
                'date' => $paystack->created_at->format('M d, Y'),
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
            'Phone Number',
            'Amount',
            'Type',
            'Reference',
            'Date'
        ];
    }
}

