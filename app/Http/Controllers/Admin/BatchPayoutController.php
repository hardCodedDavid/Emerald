<?php

namespace App\Http\Controllers\Admin;

use App\BatchPayout;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Globals as Util;
use App\Imports\BatchPayoutImport;
use App\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BatchPayoutController extends Controller
{
    public function index()
    {
        return view('admin.batch-payouts.index');
    }

    public function update(BatchPayout $batchPayout): \Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make(\request()->all(), [
            'batch' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'units' => 'required',
            'amount_invested' => 'required',
            'expected_returns' => 'required',
            'farm_cycle' => 'required',
            'payment_date' => 'required',
            'queue' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>
                Invalid data entry.
            </div></div>');
        }

        $batchPayout->update(\request()->all());
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Entry updated successfully.</div></div>');
    }

    public function destroy(BatchPayout $batchPayout)
    {
        $batchPayout->delete();
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Item deleted successfully.</div></div>');
    }

    public function upload(): \Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make(\request()->all(), [
            'file' => 'required|mimes:csv,xls,xlsx',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>
                Unsupported file uploaded, only csv, xls and xlsx are supported.
            </div></div>');
        }
        Excel::import(new BatchPayoutImport, \request()->file('file'));
        return redirect()->back()->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Batch payout uploaded successfully.</div></div>');
    }

    public function loadBatchPayouts(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'batch',
            2=> 'name',
            3=> 'email',
            4=> 'phone',
            5=> 'units',
            6=> 'amount_invested',
            7=> 'expected_returns',
            8=> 'farm_cycle',
            9=> 'payment_date',
            10=> 'queue',
            11=> 'id',
        );

        $totalData = BatchPayout::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $payouts = BatchPayout::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $payouts =  BatchPayout::latest()->where('batch','LIKE',"%{$search}%")
                ->orWhere('name','LIKE',"%{$search}%")
                ->orWhere('email','LIKE',"%{$search}%")
                ->orWhere('phone','LIKE',"%{$search}%")
                ->orWhere('amount_invested','LIKE',"%{$search}%")
                ->orWhere('expected_returns','LIKE',"%{$search}%")
                ->orWhere('units','LIKE',"%{$search}%")
                ->orWhere('farm_cycle','LIKE',"%{$search}%")
                ->orWhere('payment_date','LIKE',"%{$search}%")
                ->orWhere('queue','LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = count($payouts);
        }


        $data = array();

        if(!empty($payouts)) {
            $i = 1;
            foreach ($payouts as $payout) {
                $nestedData['sn'] = $i++;
                $nestedData['batch'] = $payout['batch'];
                $nestedData['name'] = $payout['name'];
                $nestedData['email'] = $payout['email'];
                $nestedData['phone'] = $payout['phone'];
                $nestedData['units'] = $payout['units'];
                $nestedData['amount_invested'] = '₦ '.number_format((float)$payout['amount_invested']);
                $nestedData['expected_returns'] = '₦ '.number_format((float)$payout['expected_returns']);
                $nestedData['farm_cycle'] = $payout['farm_cycle'];
                $nestedData['payment_date'] = date('M-Y', strtotime($payout['payment_date']));
                $nestedData['queue'] = $payout['queue'];
                $nestedData['action'] = '<div class="dropdown show">
                                          <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </a>
                                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a
                                            onclick="prepareEditBatchPayout('.$payout['id'].',\''.$payout['batch'].'\',\''.$payout['name'].'\',\''.$payout['email'].'\',\''.$payout['phone'].'\',\''.$payout['units'].'\',\''.$payout['amount_invested'].'\',\''.$payout['expected_returns'].'\',\''.$payout['farm_cycle'].'\',\''.$payout['payment_date'].'\',\''.$payout['queue'].'\');"
                                             data-toggle="modal" data-target="#editBasicPayoutModal" class="dropdown-item">Edit</a>
                                            <a onclick="if (confirm(\'Are you sure you want to delete?\') === true) { return document.getElementById(\'deletePayout'.$payout->id.'\').submit(); }" class="dropdown-item">Delete</a>
                                            <form method="POST" id="deletePayout'.$payout->id.'" action="/admin/batch-payouts/'.$payout->id.'/delete">
                                                <input name="_token" value="'.csrf_token().'" type="hidden">
                                                <input name="_method" value="DELETE" type="hidden">
                                            </form>
                                          </div>
                                        </div>';
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
    }
}
