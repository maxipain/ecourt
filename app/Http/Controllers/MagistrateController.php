<?php

namespace App\Http\Controllers;

use App\Models\court;
use App\Models\court_user;
use App\Models\misdeed;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\SendNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Nexmo\Laravel\Facade\Nexmo;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class MagistrateController extends Controller
{



    public function index()
    {
        if (Auth::user()->category == 'court admin') {
            //get court id
            $user_id = Auth::user()->id;
            $court_id = court_user::where('user_id',$user_id)->first();

            $court = court::where('id',$court_id->court_id)->first();

            $magistrates = $court->users()->get();

            return view('court admin.magistrates.show',compact('magistrates'));
        }
        else{
            redirect(route('login'));
        }

    }

    public function create()
    {
        //
        if (Auth::user()->category == 'court admin') {
            return view('court admin.magistrates.create');
        }
        else{
            redirect(route('login'));
        }

    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'staff_id' => ['required', 'string', 'max:12','unique:users']
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->avatar = "public/files/profile/avatar.jpg";
        $user->staff_id = $request->staff_id;
        $user->category = $request->category;
        $user->password = Hash::make($request->staff_id);
        $user->save();

        $user_id = Auth::user()->id;
        $court_user = court_user::where('user_id',$user_id)->first();


        $court_id = $court_user->court_id;


        $user->courts()->sync($court_id);
        return redirect()->back()->with('success',"$request->name's account created successfully'");

    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        if (Auth::user()->category == 'court admin') {
            $magistrate = User::where('id', $id)->first();
            return view('court admin.magistrates.edit', compact('magistrate'));
        }
        else{
            redirect(route('login'));
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'staff_id' => ['required', 'string', 'max:12']
        ]);


    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->back()->with('success', 'magistrate deleted successfully');
    }


    //For fetching states
    public function IndividualCase($id)
    {
        $case = misdeed::where('id',$id)->first();
        //get court id
        $user_id = Auth::user()->id;
        $get_court = court_user::where('user_id',$user_id)->first();
        $court_id = $get_court->court_id;
        $court = court::find($court_id);
        $edit = 1;
        $prosecutor_id = $case->prosecutor;
        $prosecutor = User::find($prosecutor_id);
        $magistrate_id = $case->magistrate;
        $magistrate = User::find($magistrate_id);
        $police_id = $case->agent;
        $police = User::find($police_id);
        return view('magistrate.cases.case',compact('case','court','edit','prosecutor','magistrate','police'));
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
        return view('magistrate.cases.case',compact('case','edit','prosecutor','magistrate','police'));
    }

    public function workedon(){
        $user_id = Auth::user()->id;
        $cases = misdeed::where('magistrate',$user_id)->get();

        return view('magistrate.cases.worked_on',compact('cases'));
    }

    public function decideCase(Request $request, $id)
    {
        $this->validate($request, [
            'reason' => 'required'
        ]);

        $misdeed = misdeed::find($id);

        if($request->outcome == 1){
            $misdeed->magistrate = Auth::user()->id;
            $misdeed->fine = $request->fine;
            $misdeed->magistrate_decision = $request->outcome;
            $message = "Hello ".$misdeed->offender_name.", your case with case number ".$misdeed->id." has been decided as valid, you have been fined ".$request->fine." ksh";
        }else if($request->outcome == 0){
            $misdeed->magistrate = Auth::user()->id;
            $misdeed->magistrate_decision = $request->outcome;
            $misdeed->status = 0;
            $message = "Hello ".$misdeed->offender_name.", your case with case number ".$misdeed->id." has been decided as invalid and has been dropped";
        }

        $misdeed->magistrate_decision_reason = $request->reason;
        $misdeed->save();
        Nexmo::message()->send([
            'to'   => $misdeed->offender_mobile,
            'from' => '254707338839',
            'text' => $message
        ]);
        return redirect()->back()->with('success', 'Case outcome successfully saved');
    }


}
