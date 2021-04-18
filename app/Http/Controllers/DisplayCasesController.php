<?php

namespace App\Http\Controllers;

use App\Models\court;
use App\Models\court_user;
use App\Models\station_user;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\misdeed;
use Illuminate\Support\Facades\Auth;

class DisplayCasesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function active(){
        if(Auth::user()->category == 'station admin'){
            $user_id = Auth::user()->id;

            //fetch police from station
            $station_user = station_user::where('user_id',$user_id)->first();
            $station_id = $station_user->station_id;
            $agent_ids = station_user::where('station_id',$station_id)->get();
            foreach($agent_ids as $agent_id){
                $agents = User::where('id',$agent_id->user_id)->get();
            }
            //end
            foreach($agents as $agent){
                if($agent->category == 'agent'){
                    $cases = misdeed::where('agent',$agent->id)->get();
                }
                return view('station admin.cases.active',compact('cases','agents'));
            }


        }
        else{
            redirect(route('login'));
        }


    }

    public function closed(){

    }

    //For fetching states
    public function IndividualCase($id)
    {
        $case = misdeed::where('id',$id)->first();
        $courts = court::all();
        return view('station admin.cases.case',compact('case','courts'));
    }
}
