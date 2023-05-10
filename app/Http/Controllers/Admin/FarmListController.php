<?php

namespace App\Http\Controllers\Admin;

use App\FarmList;
use App\Http\Controllers\Controller;
use App\MilestoneFarm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FarmListController extends Controller
{
    public function edit($slug)
    {
        return $slug;

        return view('admin.addFarmlist', ['farmlist' => FarmList::where('slug', $slug)->first(), 'edit'=>true]);
    }

    public function longIndex()
    {
        return view('admin.farm.farmlist-long');
    }

    public function loadLongFarms(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2=> 'cover',
            3=> 'start_date',
            4=> 'close_date',
            5=> 'price',
            6=> 'status',
            7=> 'interest',
            8=> 'milestone',
            9=> 'duration',
            10=> 'units',
        );

        $totalData = MilestoneFarm::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $posts = MilestoneFarm::latest()->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else {
            $search = $request->input('search.value');

            $posts = MilestoneFarm::latest()->where('title','LIKE',"%{$search}%")
                ->orWhere('price','LIKE',"%{$search}%")
                ->orWhere('interest','LIKE',"%{$search}%")
                ->orWhere('status','LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = count($posts);
        }


        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $status = '';

                if($post->isOpen()){
                    $status .= '<div class="badge badge-success">Open</div>';
                }elseif($post->isClosed()){
                    $status .= '<div class="badge badge-danger">Closed</div>';
                }elseif($post->available_units < 1){
                    $status .= '<div class="badge badge-danger">Sold Out</div>';
                }else{
                    $status .= '<div class="badge badge-warning">Coming Soon</div>';
                }

                $nestedData['sn'] = $i++;
                $nestedData['title'] = ucwords($post->title);
                $nestedData['cover'] = '<a href="' . asset($post->cover). '" data-featherlight="image" data-title="' .$post->title. '">
                                            <img src="' . asset('assets/uploads/courses/farmlist.png') . '" alt="' . $post->title .'" width="70">
                                        </a>';
                $nestedData['price'] = '₦' . number_format($post->price, 2);
                $nestedData['start_date'] = date('M d, Y', strtotime($post->start_date));
                $nestedData['close_date'] = date('M d, Y', strtotime($post->close_date));
                $nestedData['interest'] = $post->interest . '%';
                $nestedData['milestone'] = $post->milestone;
                $nestedData['duration'] = $post->duration;
                $nestedData['units'] = $post->available_units . ' Units';
                $nestedData['status'] = $status;
                $nestedData['actions'] =  '<div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="/admin/farmlist/long/'. $post->slug .'/delete" class="dropdown-item" onclick="return confirm("Are you sure you want to delete this farmlist?");">Delete</a>
                                                <a href="/admin/farmlist/long/'. $post->slug .'/edit" class="dropdown-item">Edit</a>
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

    public function addLong()
    {
        return view('admin.farm.long');
    }

    public function addLongPost(Request $req)
    {
        if(!$req->category_id || $req->category_id == 'null'){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You need to select a category.</div></div>');
        }

        if (count($req->milestones) == 0) {
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You must add at least one milestone</div></div>');
        }

        if(MilestoneFarm::whereTitle($req->title)->exists()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm name already exist. Choose a new name.</div></div>');
        }

        if ($files = $req->file('cover')) {

            $destinationPath = 'assets/uploads/courses'; // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);

            $farmlist = MilestoneFarm::create([
                'title'=>$req->title,
                'start_date'=> Carbon::parse($req->start_date)->format('Y-m-d H:i:s'),
                'close_date'=> Carbon::parse($req->close_date)->format('Y-m-d H:i:s'),
                'cover'=>$destinationPath."/".$profileImage,
                'price'=>$req->price,
                'description'=>$req->description,
                'interest' => json_encode($req->milestones),
                'milestone' => count($req->milestones),
                'duration' => $req->duration,
                'available_units' => $req->available_units,
                'category_id' => $req->category_id
            ]);

            return redirect("/admin/farmlist/long")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farm list has been created successfully.</div></div>');
        }

        return redirect("/admin/farmlist/long")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You need to provide a cover image.</div></div>');
    }

    public function editLongFarmlist($slug){
        return view('admin.farm.long', ['farmlist' => MilestoneFarm::where('slug', $slug)->first(), 'edit'=>true]);
    }

    public function editLongFarmlistPost(Request $req){
        if (count($req->milestones) == 0) {
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>You must add at least one milestone</div></div>');
        }

        $editing = MilestoneFarm::where('id', $req->id)->first();

        if ($files = $req->file('cover')) {
            if($files != ''){
                Storage::delete($editing->cover);
                $destinationPath = 'assets/uploads/courses'; // upload path
                $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $profileImage);
                $editing->update([
                    'cover'=>$destinationPath."/".$profileImage,
                ]);
            }
        }

        $editing->update([
            'title'=>$req->title,
            'start_date' => Carbon::parse($req->start_date)->format('Y-m-d H:i:s'),
            'close_date' => Carbon::parse($req->close_date)->format('Y-m-d H:i:s'),
            'price'=>$req->price,
            'description'=>$req->description,
            'interest' => json_encode($req->milestones),
            'milestone' => count($req->milestones),
            'duration' => $req->duration,
            'available_units' => $req->available_units,
            'category_id' => $req->category_id
        ]);

        return redirect("/admin/farmlist/long")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farm list has been edited successfully.</div></div>');
    }

    public function deleteLongFarmlist($slug)
    {

        $farm = MilestoneFarm::where('slug', $slug)->first();

        if(! $farm){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Farm does not exist.</div></div>');
        }

        if($farm->investments()->exists()){
            return redirect()->back()->with('message', '<div class="alert alert-danger alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farmlist cannot be delete because it is currently tied to an investment.</div></div>');
        }

        $farm->delete();
        return redirect("/admin/farmlist/long")->with('message', '<div class="alert alert-success alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Your farmlist has been deleted successfully.</div></div>');
    }

    public function shortIndex()
    {
        return view('admin.farm.farmlist-short');
    }

    public function addShort()
    {
        return view('admin.farm.short');
    }

    public function loadShortFarms(Request $request, $type)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            2=> 'cover',
            3=> 'price',
            4=> 'status',
            5=> 'interest',
            6=> 'maturity_date',
            7=> 'available_units',
        );

        switch ($type){
            case 'pending':
                $totalData = MilestoneFarm::where(function($query){
                    $query->whereDate('start_date', '>', now());
                })->count();

                break;
            case 'closed':
                $totalData = MilestoneFarm::where(function($query){
                    $query->whereDate('close_date', '<=', now());
                })->count();

                break;
            case 'opened':
                $totalData = MilestoneFarm::where(function($query){
                    $query->whereDate('start_date','<=', now());
                    $query->whereDate('close_date', '>', now());
                })->count();

                break;
            default:
                $totalData = MilestoneFarm::count();
                break;
        }

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            switch ($type){
                case 'pending':
                    $posts = MilestoneFarm::where(function($query){
                        $query->whereDate('start_date', '>', now());
                    })->latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
                case 'closed':
                    $posts = MilestoneFarm::where(function($query){
                        $query->whereDate('close_date', '<=', now());
                    })->latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
                case 'opened':
                    $posts = MilestoneFarm::where(function($query){
                        $query->whereDate('start_date','<=', now());
                        $query->whereDate('close_date', '>', now());
                    })->latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
                default:
                    $posts = MilestoneFarm::latest()->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                    break;
            }

        }
        else {
            $search = $request->input('search.value');

            switch ($type){
                case 'pending':

                    $posts =  MilestoneFarm::latest()->where('title','LIKE',"%{$search}%")
                        ->where(function($query){
                            $query->whereDate('start_date', '>', now());
                        })->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

                    $totalFiltered = MilestoneFarm::where('title','LIKE',"%{$search}%")
                        ->where(function($query){
                            $query->whereDate('start_date', '>', now());
                        })->count();
                    break;
                case 'closed':

                    $posts =  MilestoneFarm::latest()->where('title','LIKE',"%{$search}%")
                        ->where(function($query){
                            $query->whereDate('close_date', '<=', now());
                        })->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

                    $totalFiltered = MilestoneFarm::where('title','LIKE',"%{$search}%")
                        ->where(function($query){
                            $query->whereDate('close_date', '<=', now());
                        })->count();
                    break;
                case 'opened':

                    $posts =  MilestoneFarm::latest()->where('title','LIKE',"%{$search}%")
                        ->where(function($query){
                            $query->whereDate('start_date','<=', now());
                            $query->whereDate('close_date', '>', now());
                        })
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

                    $totalFiltered = MilestoneFarm::where('title','LIKE',"%{$search}%")
                        ->where(function($query){
                            $query->whereDate('start_date','<=', now());
                            $query->whereDate('close_date', '>', now());
                        })->count();
                    break;
                default:

                    $posts =  MilestoneFarm::latest()->where('title','LIKE',"%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();

                    $totalFiltered = MilestoneFarm::where('title','LIKE',"%{$search}%")
                        ->count();
                    break;
            }

        }


        $data = array();
        if(!empty($posts)) {
            $i = 1;
            foreach ($posts as $post) {
                $status = '';
                $statusLinks = '<a href="/farmlist/'. $post->slug .'" class="dropdown-item">View Farm</a>';

                if($post->isOpen()){
                    $status .= '<div class="badge badge-success">Open</div>';
                    $statusLinks .= '<a href="/farmlist/invest/'. $post->slug .'" class="dropdown-item">Invest</a>';
                }elseif($post->isClosed()){
                    $status .= '<div class="badge badge-danger">Closed</div>';
                }elseif($post->available_units < 1){
                    $status .= '<div class="badge badge-danger">Sold Out</div>';
                }else{
                    $status .= '<div class="badge badge-warning">Coming Soon</div>';
                }

                $nestedData['sn'] = $i++;
                $nestedData['title'] = ucwords($post->title);
                $nestedData['cover'] = '<a href="' . asset($post->cover). '" data-featherlight="image" data-title="' .$post->title. '">
                                            <img src="' . asset('assets/uploads/courses/farmlist.png') . '" alt="' . $post->title .'" width="70">
                                        </a>';
                $nestedData['price'] = '₦' . number_format($post->price, 2);
                $nestedData['interest'] = $post->interest . '%';
                $nestedData['maturity_date'] = $post->milestone;
                $nestedData['units'] = $post->available_units . ' Units';
                $nestedData['status'] = $status;
                $nestedData['actions'] =  '<div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                '.$statusLinks.'
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
