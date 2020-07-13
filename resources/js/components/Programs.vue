<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Programs Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_program" @click="AddProgramModal">
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
                      <th>Title</th>
                      <th>Created At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="programs.data.length > 0" v-for="program in programs.data">
                      <td>{{program.id}}</td>
                      <td>{{program.title}}</td>
                      <td>{{program.created_at | myDate}}</td>
                      <td>
                          <a href="#" @click="DetailProgramModal(program)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditProgramModal(program)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteProgram(program.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="programs.data.length == 0">
                      <td colspan="4" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="programs" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="programModal" tabindex="-1" role="dialog" aria-labelledby="programModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="programModalLabel">Add New Program</h5>
                <h5 v-show="editmode" class="modal-title" id="programModalLabel">Update Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateProgram() : createProgram()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="form.title" id="title" type="text" name="title" placeholder="Title"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                  <has-error :form="form" field="title"></has-error>
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

        <div class="modal fade" id="programModalDetail" tabindex="-1" role="dialog" aria-labelledby="programModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="programModalDetailLabel">Program Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

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
            programs:{},
            form: new Form({
              id:       '',
              title:     ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findprogram?q='+query+'&page=' + page)
              .then(response => {
                this.programs = response.data;
            });
          },
          AddProgramModal(){
            this.form.reset();
            this.editmode = false;
            $('#programModal').modal('show');
          },
          EditProgramModal(program){
            this.form.fill(program);
            this.editmode = true;
            $('#programModal').modal('show');
          },
          DetailProgramModal(program)
          {
            $('#programModalDetail #title').text(program.title);
            $('#programModalDetail').modal('show');
          },
          deleteProgram(id){
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
                  this.form.delete('api/program/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  Fire.$emit('searching');
                }
            });
          },
          loadProgram(){
            axios.get('api/program').then(({data})=>(this.programs = data))
          },
          createProgram(){
            this.$Progress.start();
            this.form.post('api/program')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#programModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Program Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateProgram(){
            this.$Progress.start();
            this.form.put('api/program/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#programModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Program Updated Successfully'
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
            this.loadProgram();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findprogram?q='+query).then(({data})=>(this.programs = data))
            });
            Fire.$on('RefreshTable',()=>this.loadProgram());
        }
    };
</script>
