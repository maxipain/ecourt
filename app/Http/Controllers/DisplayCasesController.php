<?php

namespace App\Http\Controllers;

use App\Models\court;
use App\Models\court_user;
use App\Models\station;
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
            $station = station::find($station_id);
            $court = $station->court_id;

            $cases = misdeed::where('court',$court)->get();
            $type = "Active cases";

            return view('station admin.cases.active',compact('cases','type'));

        }
        else{
            redirect(route('login'));
        }
    }

    public function closed(){
        if(Auth::user()->category == 'station admin'){
            $user_id = Auth::user()->id;

            //fetch police from station
            $station_user = station_user::where('user_id',$user_id)->first();
            $station_id = $station_user->station_id;
            $station = station::find($station_id);
            $court = $station->court_id;

            $cases = misdeed::where('court',$court)->get();

            $type = "Cases you worked on";
            return view('station admin.cases.closed',compact('cases','type'));
        }
        else{
            redirect(route('login'));
        }
    }

    //For fetching states
    public function IndividualCase($id)
    {
        $case = misdeed::where('id',$id)->first();
        $courts = court::all();
        $edit = 1;
        $prosecutor_id = $case->prosecutor;
        $prosecutor = User::find($prosecutor_id);
        $magistrate_id = $case->magistrate;
        $magistrate = User::find($magistrate_id);
        $police_id = $case->agent;
        $police = User::find($police_id);
        $court = court::find($case->court);
        return view('station admin.cases.case',compact('case','courts','edit','prosecutor','magistrate','court','police'));
    }

    //For fetching states
    public function viewCase($id)
    {
        $case = misdeed::where('id',$id)->first();
        //get court id
        $edit = 0;
        $prosecutor_id = $case->prosecutor;
        $prosecutor = User::find($prosecutor_id);
        $magistrate_id = $case->magistrate;
        $magistrate = User::find($magistrate_id);
        $police_id = $case->agent;
        $police = User::find($police_id);
        return view('station admin.cases.case',compact('case','edit','prosecutor','magistrate','police'));
    }
}
