<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\PatientDetail;
use App\MedicalHistory;
use App\LabResult;
use RealRashid\SweetAlert\Facades\Alert;
use App\ActivityLog;

class PatientController extends Controller
{
    public function index(){
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }

        $patients = $this->getPatients();

        $data = array(
            'permissions' => $permissions,
            'patients' => $patients
        );

        return view('patient-management.list')->with('data',$data);

    }

    public function getPatients(){
        if(Auth::user()->type == 2){
            return PatientDetail::where('doctor_id', Auth::user()->id)->with('doctor.doctorDetails','user')->get();
        }else if(Auth::user()->type == 3){
            return PatientDetail::where('user_id', Auth::user()->id)->with('doctor.doctorDetails','user')->get();
        }
        else{
            return PatientDetail::with('doctor.doctorDetails','user')->get();
        }
    }

    public function profile(){
        return view('patient-management.profile');
    }

    public function edit(Request $request){
        $patientDetail = PatientDetail::find($request->id);
        $patientAccount = User::find($patientDetail->user_id);

        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }
        $data = array(
            'permissions' => $permissions,
            'patientDetail' => $patientDetail,
            'patientAccount' => $patientAccount,
        );

        return view('patient-management.edit')->with('data',$data);
        
    }

    public function update(Request $request){
        $patientDetail = PatientDetail::find($request->id);
        $patientAccount = User::find($request->user_id);

        $patientAccount->name = $request->fullname;
        $patientAccount->save();

        $patientDetail->mobile_number = $request->mobile;
        $patientDetail->gender        = $request->gender;
        $patientDetail->civil_status  = $request->civil_status;
        $patientDetail->age           = $request->age;
        $patientDetail->address       = $request->address;
        $patientDetail->date_of_birth = $request->dob;
        $patientDetail->emergency_name= $request->emergency_name;
        $patientDetail->emergency_address = $request->emergency_address;
        $patientDetail->weight = $request->weight;
        $patientDetail->height = $request->height;
        $patientDetail->save();

        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'Updated patient details'
        ]);

        Alert::success('', 'Patient Details Updated!');

        return back();
    }

    public function view(Request $request){
        $patientDetail = PatientDetail::find($request->id);
        $patientAccount = User::find($patientDetail->user_id);
        $medicalHistory = MedicalHistory::where('patient_id','=',$patientDetail->user_id)->with('doctor')->get();
        $labResults = LabResult::where('patient_id','=', $patientDetail->user_id)->with('patient','patient.patientDetails','procedure')->get();
        $resultLab = [];
        foreach($labResults as $lab){
            array_push($resultLab, $lab);
        }
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }
        $data = array(
            'permissions' => $permissions,
            'patientDetail' => $patientDetail,
            'patientAccount' => $patientAccount,
            'medicalHistory' => $medicalHistory,
            'labResults' => $resultLab,
        );

        return view('patient-management.view')->with('data',$data);
    }

    public function delete(Request $request){
        $patientDetail = PatientDetail::find($request->id);
        $patientAccount = User::find($patientDetail->user_id)->delete();
        PatientDetail::find($request->id)->delete();

        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'Delete patient details'
        ]);

        Alert::success('', 'Patient Details Deleted!');

        return back();

    }

    public function createMedicalHistory(Request $request){
        $patientDetail = PatientDetail::find($request->id);
        $patientAccount = User::find($patientDetail->user_id);
        $doctors = User::where('type','=',2)->get();
        
        $user = User::where('id', Auth::user()->id)->with('usertype','usertype.permissions')->get();
        $permissions = [];
        foreach($user[0]->usertype->permissions as $permission)
        {
            array_push($permissions, $permission->name);
        }
        $data = array(
            'permissions' => $permissions,
            'patientDetail' => $patientDetail,
            'doctors'       => $doctors,
            'patientAccount' => $patientAccount,
        );

        return view('patient-management.create_medical_history')->with('data',$data);
    }

    public function saveMedicalHistory(Request $request){
        $patientAccount = User::where('id','=',$request->patient_id)->with('patientDetails')->get();
        
        $newRecord = MedicalHistory::create([
            'patient_id' => $request->patient_id,
            'complains'  => $request->complains,
            'diagnosis'  => $request->diagnosis,
            'treatment'  => $request->treatment,
            'last_visit' => $request->last_visit,
            'next_visit' => $request->next_visit,
            'attending_doctor' => $request->attending_doctor
        ]);

        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'Created a patient medical history'
        ]);

        Alert::success('', 'Successfuly saved medical history');
        return redirect()->route('view-patient', $patientAccount[0]['patientDetails'][0]['id']);
        
    }
}
