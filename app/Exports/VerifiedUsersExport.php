<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VerifiedUsersExport implements FromArray, WithHeadings, ShouldAutoSize
{

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return User::where('email_verified_at', '!=', null)->orderBy('id', 'asc')->get()->map(function($user){
            return [
                'Name' => $user->name,
                'Email' => $user->email,
                'Phone' => $user->phone,
                'status' => $user->email_verified_at != null? 'Verified' : 'Unverified' ,
                'date' => $user->created_at->format('d-M-Y')
            ];
        })->toArray();
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Status',
            'Date Joined'
        ];
    }
}
