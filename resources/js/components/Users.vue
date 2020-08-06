<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>User Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_user" @click="AddUserModal">
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
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Created At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="users.data.length > 0" v-for="user in filteredList">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.type | upText}}</td>
                      <td>{{user.created_at | myDate}}</td>
                      <td>
                          <a href="#" @click="DetailUserModal(user)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditUserModal(user)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteUser(user.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="users.data.length == 0">
                      <td colspan="6" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="userModalLabel">Add New User</h5>
                <h5 v-show="editmode" class="modal-title" id="userModalLabel">Update User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateUser() : createUser()" id="userForm">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="form.name" id="name" type="text" name="name" placeholder="Name"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" required>
                  <has-error :form="form" field="name"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="form.email" id="email" type="email" name="email" placeholder="Email" 
                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" required>
                  <has-error :form="form" field="email"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="form.password" type="password" name="password" placeholder="Password" 
                    class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                  <has-error :form="form" field="password"></has-error>
                </div>
                <div class="form-group">
                  <select v-model="form.type" id="type" name="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                    <option value="">Select Type</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="user">Standard User</option>
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

        <div class="modal fade" id="userModalDetail" tabindex="-1" role="dialog" aria-labelledby="userModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="userModalDetailLabel">User Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="name"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Email</b> <a class="float-right" id="email"></a>
                  </li>
                  <li class="list-group-item">
                    <b>Type</b> <a class="float-right" id="type"></a>
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
            users:{data:[]},
            form: new Form({
              id:       '',
              name:     '',
              email:    '',
              password: '',
              type:     ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/finduser?q='+query+'&page=' + page)
              .then(response => {
                this.users = response.data;
            });
          },
          AddUserModal(){

            this.form.reset();
            this.editmode = false;
            $('#userModal').modal('show');
          },
          EditUserModal(user){
            this.form.fill(user);
            this.editmode = true;
            $('#userModal').modal('show');
          },
          DetailUserModal(user)
          {
            $('#userModalDetail #name').text(user.name);
            $('#userModalDetail #email').text(user.email);
            $('#userModalDetail #type').text(user.type);
            $('#userModalDetail').modal('show');
          },
          deleteUser(id){
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
                  this.form.delete('api/user/'+id);
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
            axios.get('api/user').then(({data})=>(this.users = data))
          },
          createUser(){
            this.$Progress.start();
            this.form.post('api/user')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#userModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'User Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateUser(){
            this.$Progress.start();
            this.form.put('api/user/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#userModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'User Updated Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
        },
        computed:{
          filteredList:function(){
            return this.users.data.filter(user =>{
              return user.name.toLowerCase().includes(this.search.toLowerCase()) ||
              user.email.toLowerCase().includes(this.search.toLowerCase()) ||
              user.type.toLowerCase().includes(this.search.toLowerCase()) ||
              Vue.filter('myDate')(user.created_at).toLowerCase().includes(this.search.toLowerCase())
            })
          }
        },
        mounted() {
            this.loadUser();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/finduser?q='+query).then(({data})=>(this.users = data))
            });
            Fire.$on('RefreshTable',()=>this.loadUser());
        }
    };
</script>
