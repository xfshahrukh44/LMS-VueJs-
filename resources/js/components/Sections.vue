<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Sections Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_section" @click="AddSectionModal">
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
                      <th>Section Name</th>
                      <th>Class Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="sections.data.length > 0" v-for="section in filteredList">
                      <td>{{section.id}}</td>
                      <td>{{section.title}}</td>
                      <td>{{section.classroom.title}}</td>
                      <td>
                          <a href="#" @click="DetailSectionModal(section)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditSectionModal(section)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteSection(section.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="sections.data.length == 0">
                      <td colspan="4" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="sections" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="sectionModal" tabindex="-1" role="dialog" aria-labelledby="sectionModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="sectionModalLabel">Add New Section</h5>
                <h5 v-show="editmode" class="modal-title" id="sectionModalLabel">Update Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateSection() : createSection()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="form.title" id="title" type="text" name="title" placeholder="Title"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                  <has-error :form="form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <select v-model="form.classroom_id" id="classroom_id" name="classroom_id" class="form-control" :class="{ 'is-invalid': form.errors.has('classroom_id') }">
                    <option value="">Select Class</option>
                    <option v-for="classroom in classrooms" :value="classroom.id">{{classroom.title}}</option>
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
        <div class="modal fade" id="sectionModalDetail" tabindex="-1" role="dialog" aria-labelledby="sectionModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="sectionModalDetailLabel">Section Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Classroom</b> <a class="float-right" id="classroom"></a>
                  </li>
                </ul>

		            <!-- children -->
                <b>Class Sessions</b>
                <ul id="sessions">
                </ul>
                
		            <b>Students</b>
                <ul id="students">
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
            sections:{data: []},
            classrooms:{},
            form: new Form({
              id:           '',
              title:        '',
              classroom_id: ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findsection?q='+query+'&page=' + page)
              .then(response => {
                this.sections = response.data;
            });
          },
          AddSectionModal(){
            this.form.reset();
            this.editmode = false;
            $('#sectionModal').modal('show');
          },
          EditSectionModal(section){
            console.log(section);
            this.form.fill(section);
            this.editmode = true;
            $('#sectionModal').modal('show');
          },
          DetailSectionModal(section)
          {
            $('#sectionModalDetail #sessions #students li').remove();
            $('#sectionModalDetail #title').text(section.title);
            $('#sectionModalDetail #classroom').text(section.classroom.title);

            //children
            for(var i = 0; i < section.sessions.length; i++ ){
            $('#sectionModalDetail #sessions').append('<li>'+section.sessions[i].course.title+'</li>');
            }

            for(var i = 0; i < section.students.length; i++ ){
            $('#sectionModalDetail #students').append('<li>'+section.students[i].name+'</li>');
            }

            $('#sectionModalDetail').modal('show');
          },
          deleteSection(id){
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
                  this.form.delete('api/section/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  Fire.$emit('searching');
                }
            });
          },
          loadSection(){
            axios.get('api/section').then(({data})=>(this.sections = data))
          },
          loadProgram(){
            axios.get('api/get_section_classroom').then(({data})=>(this.classrooms = data))
          },
          createSection(){
            this.$Progress.start();
            this.form.post('api/section')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#sectionModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Section Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateSection(){
            this.$Progress.start();
            this.form.put('api/section/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#sectionModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Section Updated Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
        },
        computed:{
          filteredList:function(){
            return this.sections.data.filter(section =>{
              return section.title.toLowerCase().includes(this.search.toLowerCase()) ||
              section.classroom.title.toLowerCase().includes(this.search.toLowerCase())
            })
          }
        },
        mounted() {
            this.loadSection();
            this.loadProgram();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findsection?q='+query).then(({data})=>(this.sections = data))
            });
            Fire.$on('RefreshTable',()=>this.loadSection());
        }
    };
</script>
