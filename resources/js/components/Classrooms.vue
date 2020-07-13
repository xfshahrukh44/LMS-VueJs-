<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Classrooms Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_classroom" @click="AddClassroomModal">
                      <i class="fas fa-user-plus fa-lg"></i>
                  </button>
                <div class="card-tools mt-2">
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
                      <th>Classroom Name</th>
                      <th>School</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="classrooms.data.length > 0" v-for="classroom in classrooms.data">
                      <td>{{classroom.id}}</td>
                      <td>{{classroom.title}}</td>
                      <td>{{classroom.school.title}}</td>
                      <td>
                          <a href="#" @click="DetailClassroomModal(classroom)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditClassroomModal(classroom)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteClassroom(classroom.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="classrooms.data.length == 0">
                      <td colspan="4" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="classrooms" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- index view -->
        <div class="modal fade" id="classroomModal" tabindex="-1" role="dialog" aria-labelledby="classroomModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="classroomModalLabel">Add New Classroom</h5>
                <h5 v-show="editmode" class="modal-title" id="classroomModalLabel">Update Classroom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateClassroom() : createClassroom()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="form.title" id="title" type="text" name="title" placeholder="Title"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                  <has-error :form="form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <select v-model="form.school_id" id="school_id" name="school_id" class="form-control" :class="{ 'is-invalid': form.errors.has('school_id') }">
                    <option value="">Select School</option>
                    <option v-for="school in schools" :value="school.id">{{school.title}}</option>
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

        <!-- detail view -->
        <div class="modal fade" id="classroomModalDetail" tabindex="-1" role="dialog" aria-labelledby="classroomModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="classroomModalDetailLabel">Classroom Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>School</b> <a class="float-right" id="school"></a>
                  </li>
                </ul>
                
                <b>Sections</b>
                <ul id="children">
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
            classrooms:{},
            schools:{},
            form: new Form({
              id:        '',
              title:     '',
              school_id: ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findclassroom?q='+query+'&page=' + page)
              .then(response => {
                this.classrooms = response.data;
            });
          },
          AddClassroomModal(){
            this.form.reset();
            this.editmode = false;
            $('#classroomModal').modal('show');
          },
          EditClassroomModal(classroom){
            console.log(classroom);
            this.form.fill(classroom);
            this.editmode = true;
            $('#classroomModal').modal('show');
          },
          DetailClassroomModal(classroom)
          {
            $('#classroomModalDetail #children li').remove();
            $('#classroomModalDetail #title').text(classroom.title);
            $('#classroomModalDetail #school').text(classroom.school.title);
            for(var i = 0; i < classroom.sections.length; i++ ){
              $('#classroomModalDetail #children').append('<li>'+classroom.sections[i].title+'</li>');
            }
            $('#classroomModalDetail').modal('show');
          },
          deleteClassroom(id){
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
                  this.form.delete('api/classroom/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  Fire.$emit('searching');
                }
            });
          },
          loadClassroom(){
            axios.get('api/classroom').then(({data})=>(this.classrooms = data))
          },
          loadProgram(){
            axios.get('api/get_classroom_school').then(({data})=>(this.schools = data))
          },
          createClassroom(){
            this.$Progress.start();
            this.form.post('api/classroom')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#classroomModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Classroom Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateClassroom(){
            this.$Progress.start();
            this.form.put('api/classroom/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#classroomModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Classroom Updated Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          searchit(){
            Fire.$emit('searching');
      }
        },
        mounted() {
            this.loadClassroom();
            this.loadProgram();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findclassroom?q='+query).then(({data})=>(this.classrooms = data))
            });
            Fire.$on('RefreshTable',()=>this.loadClassroom());
        }
    };
</script>
