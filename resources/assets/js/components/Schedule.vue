<template>
  <div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i>
        Appointment List
    </div>
    <div class="card-body">
        <div class="form-group">
          <button class="btn btn-info" @click="toggleView" v-if="!calendar_view"><i class="fa fa-calendar"></i> Calendar View</button>
          <button class="btn btn-info" @click="toggleView" v-else><i class="fa fa-list" ></i> List View</button>
          <a href="/appointments/create" class="btn btn-info"><i class="fa fa-plus"></i> Create Appointment</a>
        </div>
        
        <div class="table-responsive" v-if="!calendar_view">
            <table class="table table-bordered" id="" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Patient Name</th>
                        <th>Attending Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Period</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody v-if="appointments.length">
                    <tr v-for="(appointment, index) in appointments" :key="index">
                      <td v-if="appointment.status == 0">
                        <span v-if="user_data.type == 3">
                          <a :href="'/appointments/cancel/' + appointment.id "><button class="btn btn-danger" title="Canceled"><i class="fa fa-times"></i> Cancel Appointment</button></a>
                        </span>
                        <span v-else-if="user_data.type == 4">
                          <a :href="'/appointments/approve/' + appointment.id"><button class="btn btn-info" title="Approve"><i class="fa fa-check"></i> Approve</button></a>
                          <a :href="'/appointments/cancel/' + appointment.id "><button class="btn btn-danger" title="Cancel"><i class="fa fa-times"></i> Cancel Appointment</button></a>
                        </span>
                        <span v-else-if="user_data.type == 1">
                          <a :href="'/appointments/approve/' + appointment.id"><button class="btn btn-info" title="Approve"><i class="fa fa-check"></i> Approve</button></a>
                          <a :href="'/appointments/cancel/' + appointment.id "><button class="btn btn-danger" title="Cancel"><i class="fa fa-times"></i> Cancel Appointment</button></a>
                        </span>
                      </td>
                      <td v-else>
                        <span v-if="appointment.status == 1">
                          <a><button class="btn btn-info" title="Approve" disabled><i class="fa fa-check"></i> Approved</button></a>
                        </span>
                        <span v-else-if="appointment.status == 2">
                          <a><button class="btn btn-danger" title="Canceled" disabled><i class="fa fa-times"></i> Canceled</button></a>
                        </span>
                      </td>
                      <td>{{ appointment.patient.name }}</td>
                      <td>{{ appointment.doctor.name }}</td>
                      <td>{{ appointment.date }}</td>
                      <td>{{ appointment.real_time }}</td>
                      <td>{{ appointment.time }}</td>
                      <td :class="(appointment.status == 0) ? 'pending' : (appointment.status == 1) ? 'approved' : 'canceled' ">{{ (appointment.status == 0) ? 'Pending' : (appointment.status == 1) ? 'Approved' : 'Canceled' }}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                  
                </tbody>
            </table>
        </div>
        <vue-scheduler 
          :events="appointments_calendar" 
          :available-views="['month']"
          @event-created="validateAppointment"
          event-display="comments"
          :showTimeMarker="false"
          :disableDialog="true"
          v-else
        />
    </div>
  </div>
  
</template>
<script>
// ES6 Modules or TypeScript
import Swal from 'sweetalert2'
import moment from 'moment';

  export default {
    props : ['user_data'],
    data() {
      return {
        appointments : [],
        calendar_view  : false,
        patients_list : {},
        doctors_list  : [],
        newScheduleParams : {},
      }
    },
    mounted(){
      this.getAppointments();
      this.getPatientsList();
      this.getDoctorsList();
    },
    methods: {
      eventDisplay(event) {
        return event.name + ' - ' + event.comments;
      },
      getAppointments : function(){
        var self = this;
        this.$http.get('/get-appointments')
        .then(function (response) {
            // handle success
            self.appointments = response.data;
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });
      },

      toggleView : function(){
        this.calendar_view = !this.calendar_view;
      },

      validateAppointment : function(event){
        // console.log('Event created');
        console.log(event);
        // console.log(this.newScheduleParams);
        var self = this;

        if(moment().isBefore(event.date)){
          self.newScheduleParams.date = moment(event.date).format('YYYY-MM-DD');
          if(moment(event.startTime,'kk:mm').format('a') == 'am'){
            self.newScheduleParams.time = 'AM'
          }
          else{
            self.newScheduleParams.time = 'PM'
          }

          self.$http.post('/saveAppointment', self.newScheduleParams)
          .then(function (response) {
              // handle success
              Swal.fire({
                icon: 'success',
                title: '',
                text: 'Appointment has been scheduled successfully!',
              });

              self.getAppointments();
          })
          .catch(function (error) {
              // handle error
              console.log(error);
          });


        }else{
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please select a date ahead from today',
          })
        }

        // console.log(moment(event.date).format('YYYY-MM-DD'));
        // console.log(moment(event.startTime,'kk:mm').format('a'));
        // console.log(moment(event.endTime,'kk:mm').format('a'));


      },

      getPatientsList : function(){
          var self = this;
          this.$http.get('/getPatientList')
          .then(function (response) {
              // handle success
              var patients = response.data;
              var tempdata = {};
              patients.forEach(function(item,index){
                tempdata[item.user_id] = item.user.name;
              })
              self.patients_list = tempdata;
          })
          .catch(function (error) {
              // handle error
              console.log(error);
          });
      },

      getDoctorsList : function(){
          var self = this;
          this.$http.get('/getDoctorsList')
          .then(function (response) {
              // handle success
              var doctors = response.data;
              var tempdata = {};
              doctors.forEach(function(item,index){
                tempdata[item.user_id] = item.fullname;
              })
              self.doctors_list = tempdata;
              // $('#dataTable').DataTable();
          })
          .catch(function (error) {
              // handle error
              console.log(error);
          });
      },

      dayClicked(date) {
        var self = this;

        if(self.user_data.type == 1 || self.user_data.type == 4){
          Swal.fire({
            title: 'Select Patient',
            input: 'select',
            inputOptions: self.patients_list,
            inputPlaceholder: '-- Select Patient --',
            showCancelButton: false,
            inputValidator: function (value) {
              return new Promise(function (resolve, reject) {
                if (value !== '') {
                  resolve();
                } else {
                  resolve('You need to select a Patient');
                }
              });
            }
          }).then(function (result) {
            if (result.isConfirmed) {
              self.newScheduleParams.patient_id = result.value;
                Swal.fire({
                  title: 'Select Doctor',
                  input: 'select',
                  inputOptions: self.doctors_list,
                  inputPlaceholder: 'Required',
                  showCancelButton: false,
                  inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                      if (value !== '') {
                        resolve();
                      } else {
                        resolve('You need to select a Doctor');
                      }
                    });
                  }
                }).then(function (result) {
                  if (result.isConfirmed) {
                    //result.value (doctor id)
                    self.newScheduleParams.doctor_id = result.value;
                  }
                });
            }
          });
        }
      },
    },

    computed : {
      appointments_calendar : function(){
        var schedule = [];
        
        this.appointments.forEach(function(item,index){
          schedule.push({
            date : moment(item.date),
            // startTime: (item.time == 'AM') ? "08:00" : '13:00',
            // endTime: (item.time == 'AM') ? "12:00" : '17:00',
            name: item.patient.name,
            comments : item.doctor.doctor_details.fullname + ' ' + '(' + (item.time == 'AM') ? "Morning" : 'Afternoon'  + ')',
            customAttribute: "I'm a custom attribute",
          })
        });

        return schedule;
      }

    },
  }  
</script>