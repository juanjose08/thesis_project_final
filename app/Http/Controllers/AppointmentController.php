<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\PatientDetail;
use App\Appointment;
use App\Setting;
use App\Transaction;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use App\ActivityLog;

class AppointmentController extends Controller
{
    public function index(){
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }

        $data = array(
            'permissions' => $permissions,
        );

        return view('appointment.scheduler')->with('data',$data);
    }

    public function getList(){
        if(Auth::user()->type == 2){
            // if doctor, show all schedules for the doctor
            return Appointment::where('doctor_id',Auth::user()->id)->with('doctor.doctorDetails','patient.patientDetails')->get();
        }else if(Auth::user()->type == 3)
        {
            // if patient, show all schedules for the patient
            return Appointment::where('user_id',Auth::user()->id)->with('doctor.doctorDetails','patient.patientDetails')->get();
        }
        else{
            // if others = get all appointments
            return Appointment::with('doctor.doctorDetails','patient.patientDetails')->get();
        }
    }

    public function saveAppointment(Request $request){
       // check appointment avaialability
       $schedule = Appointment::where('date','=',$request->date)
       ->where('time','=',$request->time)
       ->where('status','=',1) // approved appointment
       ->count();

        $sameTimeDoctor = Appointment::where('date','=',$request->date)
            ->where('time','=',$request->time)
            ->orwhere('real_time','=',$request->real_time)
            ->count();

        $settings = Setting::find(1);
        $limit = ($request->time == 'AM') ? $settings->am_limit : $settings->pm_limit;


        // validate date
        $today = Carbon::today();
        $date  = Carbon::parse($request->date);
        $time_open  = Carbon::createFromTimeString('09:00');
        $time_close = Carbon::createFromTimeString('17:00');

        if($date->lessThan($today)){
            Alert::error('', 'Please select date ahead from today');
            return redirect()->back()->withInput(); 
        }
        else{
            if(!Carbon::parse($request->real_time)->between($time_open, $time_close)){
                Alert::error('', 'Please select time between 9:00AM to 5:00PM');
                return redirect()->back()->withInput(); 
            }else{
                if($schedule >= $limit){
                    // no slots left for your selected schedule
                    Alert::error('', 'No slots left for your selected schedule');
                    return redirect()->back()->withInput(); 
                }
                else if($sameTimeDoctor > 0){
                    Alert::error('', 'You already have an appointment with the same doctor and same schedule!');
                    return redirect()->back()->withInput(); 
                }
                else{
                    $appointment = Appointment::create([
                        'doctor_id' => $request->doctor_id,
                        'user_id'   => $request->patient_id,
                        'date'      => $request->date,
                        'real_time' => $request->real_time,
                        'time'      => $request->time,
                        'status'    => 0, // not yet approved
                    ]);
                    
                    ActivityLog::create([
                        'user_id' => Auth::user()->id,
                        'activity' => 'Created an Appointment'
                    ]);

                    Alert::success('', 'Appointment saved');
                    return redirect()->route('appointment');
                }
            }
        }

        
    }

    public function checkAvailability(Request $request){
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }

        $doctors = User::where('type','=',2)->with('doctorDetails')->get();

        $data = array(
            'permissions' => $permissions,
            'doctors'     => $doctors
        );

        return view('appointment.check_availability')->with('data',$data);
    }

    public function checkSchedule(Request $request){
        $am_limit = Setting::all()[0]->am_limit;
        $pm_limit = Setting::all()[0]->pm_limit;
        
        $schedule = Appointment::where('doctor_id','=',$request->doctor_id)
                                ->where('date','=',$request->date)
                                ->where('time','=',$request->period)
                                ->where('status', '=', 1) // approved appointment
                                ->get();

        if($request->period == 'AM'){
            if($schedule->count() >= $am_limit){
                Alert::error('', 'Schedule not available');
            }
            else{
                Alert::success('', 'Schedule available');
            }
        }else if($request->period == 'PM'){
            if($schedule->count() >= $pm_limit){
                Alert::error('', 'Schedule not available');
            }
            else{
                Alert::success('', 'Schedule available');
            }
        }

        return redirect()->back();   

    }

    public function create(){
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }

        $doctors = User::where('type','=',2)->with('doctorDetails')->get();
        $patients = User::where('type', '=' , 3)->with('patientDetails')->get();

        $data = array(
            'permissions' => $permissions,
            'doctors'     => $doctors,
            'patients'    => $patients
        );

        return view('appointment.create')->with('data',$data);
    }

    public function view(Request $request){
        $appointment = Appointment::where('id','=', $request->id)->with('doctor','patient')->get()->toArray();
        // $transaction = Transaction::where('patient_id', '=', $appointment[0]['user_id'])
        //                             ->where('doctor_id','=', $appointment[0]['doctor_id'])
        //                             ->where('schedule_id','=',$appointment[0]['id'])
        //                             ->get()
        //                             ->toArray();
        
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }


        $data = array(
            'permissions' => $permissions,
            'appointments'     => $appointment[0]
        );             
        return view('appointment.view')->with('data',$data);
    }

    public function approve(Request $request){
        $appointment = Appointment::where('id',$request->id)->get()[0];

        // dd($appointment->status);
        $appointment->status = 1;
        $appointment->save();

        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'Approved an appointment'
        ]);

        Alert::success('', 'Appointment approved');
        return redirect()->back();
        
    }

    public function cancel(Request $request){
        $appointment = Appointment::where('id',$request->id)->get()[0];

        // dd($appointment->status);
        $appointment->status = 2;
        $appointment->save();

        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'Canceled an Appointment'
        ]);

        Alert::success('', 'Appointment canceled');
        return redirect()->back();
    }

}
