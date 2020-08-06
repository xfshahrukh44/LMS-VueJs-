<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Students Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_student" @click="AddStudentModal">
                      <i class="fas fa-user-plus fa-lg"></i>
                  </button>
                <div class="card-tools mt-2">
                  <div class="input-group input-group-sm">
                    <input v-model="search" type="text" name="table_search" class="form-control float-right" id="table_search" placeholder="Search">
                    <div class="input-group-append ml-2">
                      <button class="btn btn-primary" id="s_btn">
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
                      <th>User ID</th>
                      <th>Student Name</th>
                      <th>Contact</th>
                      <th>Address</th>
                      <th>Section</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="students.data.length > 0" v-for="student in filteredList">
                      <td>{{student.id}}</td>
                      <td>{{student.user.name}}</td>
                      <td>{{student.name}}</td>
                      <td>{{student.contact}}</td>
                      <td>{{student.address}}</td>
                      <td>{{student.section.classroom.title + student.section.title}}</td>
                      <td>
                          <a href="#" @click="DetailStudentModal(student)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditStudentModal(student)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteStudent(student.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="students.data.length == 0">
                      <td colspan="7" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="students" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="studentModalLabel">Add New Student</h5>
                <h5 v-show="editmode" class="modal-title" id="studentModalLabel">Update Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateStudent() : createStudent()">
              <div class="modal-body">
                <div class="form-group">
                  <select v-model="form.user_id" id="user_id" name="user_id" class="form-control" :class="{ 'is-invalid': form.errors.has('user_id') }">
                    <option value="">Select User</option>
                    <option v-for="user in users" :value="user.id">{{user.name}}</option>
                  </select>
                </div>
                <div class="form-group">
                  <input v-model="form.name" id="name" type="text" name="name" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                  <has-error :form="form" field="name"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="form.contact" id="contact" type="text" name="contact" placeholder="Enter Contact"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('contact') }">
                  <has-error :form="form" field="contact"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="form.address" id="address" type="text" name="address" placeholder="Enter Address"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('address') }">
                  <has-error :form="form" field="address"></has-error>
                </div>
                <div class="form-group">
                  <select v-model="form.section_id" id="section_id" name="section_id" class="form-control" :class="{ 'is-invalid': form.errors.has('section_id') }">
                    <option value="">Select Section</option>
                    <option v-for="section in sections" :value="section.id">{{section.title}}</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                <button v-show="editmode" type="submit" class="btn btn-primary">Update</button>
              </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="studentModalDetail" tabindex="-1" role="dialog" aria-labelledby="studentModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="studentModalDetailLabel">School Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="name"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Section</b> <a class="float-right" id="section"></a>
                  </li>
                  <li class="list-group-item">
                    <b>Contact</b> <a class="float-right" id="contact"></a>
                  </li>
                  <li class="list-group-item">
                    <b>Address</b> <a class="float-right" id="address"></a>
                  </li>
                </ul>

              <!-- /.card-body -->
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
          return{
            search:'',
            editmode: false,
            users:{},
            students:{data: []},
            sections:{},
            form: new Form({
              id:         '',
              user_id:    '',
              name:       '',
              contact:    '',
              address:    '',
              section_id: ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findstudent?q='+query+'&page=' + page)
              .then(response => {
                this.students = response.data;
            });
          },
          AddStudentModal(){
            this.form.reset();
            this.editmode = false;
            $('#studentModal').modal('show');
          },
          EditStudentModal(student){
            console.log(student);
            this.form.fill(student);
            this.editmode = true;
            $('#studentModal').modal('show');
          },
          DetailStudentModal(student)
          {
            $('#studentModalDetail #name').text(student.name);
            $('#studentModalDetail #contact').text(student.contact);
            $('#studentModalDetail #address').text(student.address);
            $('#studentModalDetail #section').text(student.section.classroom.title + ' - ' + student.section.title);
            $('#studentModalDetail').modal('show');
          },
          deleteStudent(id){
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                  this.form.delete('api/student/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  Fire.$emit('searching');
                }
            });
          },
          loadUser(){
            axios.get('api/get_user_student').then(({data})=>(this.users = data))
          },
          loadStudent(){
            axios.get('api/student').then(({data})=>(this.students = data))
          },
          loadProgram(){
            axios.get('api/get_student_section').then(({data})=>(this.sections = data))
          },
          createStudent(){
            this.$Progress.start();
            this.form.post('api/student')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#studentModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Student Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateStudent(){
            this.$Progress.start();
            this.form.put('api/student/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#studentModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Student Updated Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
        },
        computed:{
          filteredList:function(){
            return this.students.data.filter(student =>{
              return student.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
              student.name.toLowerCase().includes(this.search.toLowerCase()) ||
              student.contact.toLowerCase().includes(this.search.toLowerCase()) ||
              student.address.toLowerCase().includes(this.search.toLowerCase()) ||
              (student.section.classroom.title + student.section.title).toLowerCase().includes(this.search.toLowerCase())
            })
          }
        },
        mounted() {
            this.loadUser();
            this.loadStudent();
            this.loadProgram();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findstudent?q='+query).then(({data})=>(this.students = data))
            });
            Fire.$on('RefreshTable',()=>this.loadStudent());
        }
    };
</script>
