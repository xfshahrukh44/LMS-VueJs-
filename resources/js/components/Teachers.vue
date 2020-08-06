<template>
    <div class="container" v-if="$gate.isTeacher() || $gate.isAdmin()">
        <div class="row mt-3 ml-1">
            <h2>Teachers Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_teacher" @click="AddTeacherModal">
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
                      <th>Teacher Name</th>
                      <th>Contact</th>
                      <th>Address</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="teachers.data.length > 0" v-for="teacher in filteredList">
                      <td>{{teacher.id}}</td>
                      <td>{{teacher.user.name}}</td>
                      <td>{{teacher.name}}</td>
                      <td>{{teacher.contact}}</td>
                      <td>{{teacher.address}}</td>
                      <td>
                          <a href="#" @click="DetailTeacherModal(teacher)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditTeacherModal(teacher)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteTeacher(teacher.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="teachers.data.length == 0">
                      <td colspan="7" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="teachers" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="teacherModal" tabindex="-1" role="dialog" aria-labelledby="teacherModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="teacherModalLabel">Add New Teacher</h5>
                <h5 v-show="editmode" class="modal-title" id="teacherModalLabel">Update Teacher</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateTeacher() : createTeacher()">
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

        <div class="modal fade" id="teacherModalDetail" tabindex="-1" role="dialog" aria-labelledby="teacherModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="teacherModalDetailLabel">Teacher Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="name"></h3>

                <ul class="list-group list-group-unbordered mb-3">
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
            teachers:{data: []},
            sections:{},
            form: new Form({
              id:         '',
              user_id:    '',
              name:       '',
              contact:    '',
              address:    ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findteacher?q='+query+'&page=' + page)
              .then(response => {
                this.teachers = response.data;
            });
          },
          AddTeacherModal(){
            this.form.reset();
            this.editmode = false;
            $('#teacherModal').modal('show');
          },
          EditTeacherModal(teacher){
            console.log(teacher);
            this.form.fill(teacher);
            this.editmode = true;
            $('#teacherModal').modal('show');
          },
          DetailTeacherModal(teacher)
          {
            $('#teacherModalDetail #name').text(teacher.name);
            $('#teacherModalDetail #contact').text(teacher.contact);
            $('#teacherModalDetail #address').text(teacher.address);
            $('#teacherModalDetail').modal('show');
          },
          deleteTeacher(id){
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
                  this.form.delete('api/teacher/'+id);
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
            axios.get('api/get_user_teacher').then(({data})=>(this.users = data))
          },
          loadTeacher(){
            axios.get('api/teacher').then(({data})=>(this.teachers = data))
          },
          createTeacher(){
            this.$Progress.start();
            this.form.post('api/teacher')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#teacherModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Teacher Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateTeacher(){
            this.$Progress.start();
            this.form.put('api/teacher/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#teacherModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Teacher Updated Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
        },
        computed:{
          filteredList:function(){
            return this.teachers.data.filter(teacher =>{
              return teacher.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
              teacher.name.toLowerCase().includes(this.search.toLowerCase()) ||
              teacher.contact.toLowerCase().includes(this.search.toLowerCase()) ||
              teacher.address.toLowerCase().includes(this.search.toLowerCase())
            })
          }
        },
        mounted() {
            this.loadUser();
            this.loadTeacher();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findteacher?q='+query).then(({data})=>(this.teachers = data))
            });
            Fire.$on('RefreshTable',()=>this.loadTeacher());
        }
    };
</script>
