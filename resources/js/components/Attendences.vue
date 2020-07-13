<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Attendance Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-4"></h3>
                <div class="card-tools">
                  <div class="input-group input-group-sm">
                    <input v-model="search" type="text" name="table_search" class="form-control float-right" id="table_search" placeholder="Search" @keyup.enter="searchit">
                    <div class="input-group-append ml-2">
                      <button class="btn btn-primary" id="s_btn" @click="searchit">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>

                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>User Name</th>
                      <th>Check-In Time</th>
                      <th>Check-Out Time</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="attendances.data.length > 0" v-for="attendance in attendances.data">
                      <td>{{attendance.id}}</td>
                      <td>{{attendance.user_name}}</td>
                      <td>{{attendance.check_in}}</td>
                      <td>{{attendance.check_out}}</td>
                      <td>
                          <a href="#" @click="EditAttendanceModal(attendance)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                      </td>
                    </tr>
                    <tr v-if="attendances.data.length == 0">
                      <td colspan="7" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="attendances" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
          return{
            search:'',
            attendances:{},
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findattendance?q='+query+'&page=' + page)
              .then(response => {
                this.attendances = response.data;
            });
          },
          loadAttendance(){
            axios.get('api/attendance').then(({data})=>(this.attendances = data))
          },
          searchit(){
            Fire.$emit('searching');
            }
        },
        mounted() {
            this.loadAttendance();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findattendance?q='+query).then(({data})=>(this.attendances = data))
            });
            Fire.$on('RefreshTable',()=>this.loadAttendance());
        }
    };
</script>
