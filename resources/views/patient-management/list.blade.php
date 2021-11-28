@extends('layouts.dashboard.main')

@section('content')
<main>
    <div class="container-fluid" id="app">
        <h1 class="mt-4"><img class="card-img-top img-thumbnail" style="height: 60px; width : 60px" src="{{ asset('assets/quick_links/patient.png') }}" alt="Patient Management"> Patient Management</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Patients List</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                Patients List
            </div>
            <div class="card-body">
                <sweet-modal ref="editPatient">Edit patient</sweet-modal>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Fullname</th>
                                <th>Gender</th>
                                <th>Civil Status</th>
                                <th>Age</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Attending Doctor</th>
                            </tr>
                        </thead>

                        @if($data['patients'])
                            <tbody>
                                @foreach($data['patients'] as $patient)
                                
                                <tr>
                                    <td>
                                        <a href="{{ route('view-patient', $patient['id'] ) }}"><button class="btn btn-info" title="View Details"><i class="fa fa-file"></i> Medical History</button></a>
                                        <a href="{{ route('edit-patient', $patient['id'] ) }}"><button class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></button></a>
                                        @if($patient['user']->id != Auth::user()->id)
                                            @if(Auth::user()->type != 2 && Auth::user()->type != 8)
                                                <!-- <a href="{{ route('delete-patient', $patient['id']) }}"><button class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button></a> -->
                                                <a href="#"><button data-toggle="modal" data-target="#exampleModal" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></button></a>
                                            @endif
                                        @endif

                                    </td>
                                    <td>{{ $patient['user']['name'] }}</td>
                                    <td>{{ $patient['gender'] }}</td>
                                    <td>{{ $patient['civil_status'] }}</td>
                                    <td>{{ $patient['age'] }}</td>
                                    <td>{{ $patient['mobile_number'] }}</td>
                                    <td>{{ $patient['address'] }}</td>
                                    <td>{{ ($patient['doctor']) ? $patient['doctor']['doctorDetails']['fullname'] : '- N/A -' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tbody>
                                <tr>
                                    <td colspan="6"><center>No records to show.</center></td>
                                </tr>
                            </tbody>
                        @endif

                        
                    </table>
                </div>
            </div>
        </div>

        {{-- <patients-list :user_data="user_data"></patients-list> --}}
    </div>
    <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Remove User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete {{ $patient['user']['name'] }}?              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <a href="{{ route('delete-patient', $patient['id']) }}" type="button" class="btn btn-primary">Delete user</a>
              </div>
            </div>
          </div>
        </div>
</main>
@include('layouts.dashboard.footer')
@endsection
