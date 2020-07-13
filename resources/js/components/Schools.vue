<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Schools Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_school" @click="AddSchoolModal">
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
                      <th>Program</th>
                      <th>Title</th>
                      <th>Location</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="schools.data.length > 0" v-for="school in schools.data">
                      <td>{{school.school_id}}</td>
                      <td>{{school.program_title}}</td>
                      <td>{{school.school_title}}</td>
                      <td>{{school.location}}</td>
                      <td>
                          <a href="#" @click="DetailSchoolModal(school)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditSchoolModal(school)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteSchool(school.school_id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="schools.data.length == 0">
                      <td colspan="5" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="schools" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="schoolModal" tabindex="-1" role="dialog" aria-labelledby="schoolModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="schoolModalLabel">Add New School</h5>
                <h5 v-show="editmode" class="modal-title" id="schoolModalLabel">Update School</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateSchool() : createSchool()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="form.school_title" id="school_title" type="text" name="school_title" placeholder="Title"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('school_title') }">
                  <has-error :form="form" field="school_title"></has-error>
                </div>
                <div class="form-group">
                  <select v-model="form.program_id" id="program_id" name="program_id" class="form-control" :class="{ 'is-invalid': form.errors.has('program_id') }">
                    <option value="">Select Program</option>
                    <option v-for="program in programs" :value="program.id">{{program.title}}</option>
                  </select>
                </div>
                <div class="form-group">
                  <input v-model="form.location" id="location" type="text" name="location" placeholder="Location"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('location') }">
                  <has-error :form="form" field="location"></has-error>
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

        <div class="modal fade" id="schoolModalDetail" tabindex="-1" role="dialog" aria-labelledby="schoolModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="schoolModalDetailLabel">School Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Program</b> <a class="float-right" id="program"></a>
                  </li>
                  <li class="list-group-item">
                    <b>Location</b> <a class="float-right" id="location"></a>
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
            schools:{},
            programs:{},
            form: new Form({
              school_id:    '',
              school_title:  '',
              program_id: '',
              location:      ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findschool?q='+query+'&page=' + page)
              .then(response => {
                this.schools = response.data;
            });
          },
          AddSchoolModal(){
            this.form.reset();
            this.editmode = false;
            $('#schoolModal').modal('show');
          },
          EditSchoolModal(school){
            console.log(school);
            this.form.fill(school);
            this.editmode = true;
            $('#schoolModal').modal('show');
          },
          DetailSchoolModal(school)
          {
            $('#schoolModalDetail #title').text(school.school_title);
            $('#schoolModalDetail #program').text(school.program_title);
            $('#schoolModalDetail #location').text(school.location);
            $('#schoolModalDetail').modal('show');
          },
          deleteSchool(id){
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
                  this.form.delete('api/school/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  Fire.$emit('searching');
                }
            });
          },
          loadSchool(){
            axios.get('api/school').then(({data})=>(this.schools = data))
          },
          loadProgram(){
            axios.get('api/get_school_program').then(({data})=>(this.programs = data))
          },
          createSchool(){
            this.$Progress.start();
            this.form.post('api/school')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#schoolModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'School Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateSchool(){
            this.$Progress.start();
            this.form.put('api/school/'+this.form.school_id)
            .then(()=>{
              Fire.$emit('searching');
              $('#schoolModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'School Updated Successfully'
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
            this.loadSchool();
            this.loadProgram();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findschool?q='+query).then(({data})=>(this.schools = data))
            });
            Fire.$on('RefreshTable',()=>this.loadSchool());
        }
    };
</script>
