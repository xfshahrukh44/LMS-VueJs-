<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Courses Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_course" @click="AddCourseModal">
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
                      <th>Course Name</th>
                      <th>Class Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="courses.data.length > 0" v-for="course in filteredList">
                      <td>{{course.id}}</td>
                      <td>{{course.title}}</td>
                      <td>{{course.classroom.title}}</td>
                      <td>
                          <a href="#" @click="DetailCourseModal(course)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditCourseModal(course)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteCourse(course.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="courses.data.length == 0">
                      <td colspan="4" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="courses" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="courseModalLabel">Add New Course</h5>
                <h5 v-show="editmode" class="modal-title" id="courseModalLabel">Update Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateCourse() : createCourse()">
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
        <div class="modal fade" id="courseModalDetail" tabindex="-1" role="dialog" aria-labelledby="courseModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="courseModalDetailLabel">Course Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Class</b> <a class="float-right" id="classroom"></a>
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
            courses:{data: []},
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
            axios.get('api/findcourse?q='+query+'&page=' + page)
              .then(response => {
                this.courses = response.data;
            });
          },
          AddCourseModal(){
            this.form.reset();
            this.editmode = false;
            $('#courseModal').modal('show');
          },
          EditCourseModal(course){
            console.log(course);
            this.form.fill(course);
            this.editmode = true;
            $('#courseModal').modal('show');
          },
          DetailCourseModal(course)
          {
            $('#courseModalDetail #title').text(course.title);
            $('#courseModalDetail #classroom').text(course.classroom.title);
            $('#courseModalDetail').modal('show');
          },
          deleteCourse(id){
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
                  this.form.delete('api/course/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  Fire.$emit('searching');
                }
            });
          },
          loadCourse(){
            axios.get('api/course').then(({data})=>(this.courses = data))
          },
          loadProgram(){
            axios.get('api/get_course_classroom').then(({data})=>(this.classrooms = data))
          },
          createCourse(){
            this.$Progress.start();
            this.form.post('api/course')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#courseModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Course Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateCourse(){
            this.$Progress.start();
            this.form.put('api/course/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#courseModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Course Updated Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
        },
        computed:{
          filteredList:function(){
            return this.courses.data.filter(course =>{
              return course.title.toLowerCase().includes(this.search.toLowerCase()) ||
              course.classroom.title.toLowerCase().includes(this.search.toLowerCase())
            })
          }
        },
        mounted() {
            this.loadCourse();
            this.loadProgram();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findcourse?q='+query).then(({data})=>(this.courses = data))
            });
            Fire.$on('RefreshTable',()=>this.loadCourse());
        }
    };
</script>
