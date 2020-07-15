<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Sessions Details</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mt-2"></h3>
                  <button class="btn btn-success xs" id="add_session" @click="AddSessionModal">
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
                      <th>Section Name</th>
                      <th>Course Name</th>
                      <th>Teaher Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="sessions.data.length > 0" v-for="session in sessions.data">
                      <td>{{session.id}}</td>
                      <td>{{session.section.classroom.title + ' - ' + session.section.title}}</td>
                      <td>{{session.course.classroom.title + ' - ' + session.course.title}}</td>
                      <td>{{session.teacher.name}}</td>
                      <td>
                          <a href="#" @click="DetailSessionModal(session)">
                            <i class="fas fa-eye blue ml-1"></i>
                          </a>
                          <a href="#" @click="EditSessionModal(session)">
                            <i class="fas fa-edit blue ml-1"></i>
                          </a>
                          <a href="#" @click="deleteSession(session.id)"><i class="fas fa-trash red ml-1"></i></a>
                      </td>
                    </tr>
                    <tr v-if="sessions.data.length == 0">
                      <td colspan="7" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="sessions" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- CREATE/EDIT VIEW -->
        <div class="modal fade" id="sessionModal" tabindex="-1" role="dialog" aria-labelledby="sessionModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="sessionModalLabel">Add New Session</h5>
                <h5 v-show="editmode" class="modal-title" id="sessionModalLabel">Update Session</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateSession() : createSession()">
              <div class="modal-body">
                <div class="form-group">
                  <select v-model="form.section_id" id="section_id" name="section_id" class="form-control" :class="{ 'is-invalid': form.errors.has('section_id') }">
                    <option value="">Select Section</option>
                    <option v-for="section in sections" :value="section.id">{{section.title}}</option>
                  </select>
                </div>
                <div class="form-group">
                  <select v-model="form.course_id" id="course_id" name="course_id" class="form-control" :class="{ 'is-invalid': form.errors.has('course_id') }">
                    <option value="">Select Course</option>
                    <option v-for="course in courses" :value="course.id">{{course.title}}</option>
                  </select>
                </div>
                <div class="form-group">
                  <select v-model="form.teacher_id" id="teacher_id" name="teacher_id" class="form-control" :class="{ 'is-invalid': form.errors.has('teacher_id') }">
                    <option value="">Select Teacher</option>
                    <option v-for="teacher in teachers" :value="teacher.id">{{teacher.name}}</option>
                  </select>
                </div>
                <div class="form-group">
                  <input v-model="form.meeting_url" id="meeting_url" type="text" name="meeting_url" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('meeting_url') }">
                  <has-error :form="form" field="meeting_url"></has-error>
                </div>
                <div class="form-group">
                  <select v-model="form.state" id="teacher_id" name="state" class="form-control" :class="{ 'is-invalid': form.errors.has('state') }">
                    <option value="">Set State</option>
                    <option value="enable">enable</option>
                    <option value="disable">disable</option>
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

        <!-- DETAIL VIEW -->
        <div class="modal fade" id="sessionModalDetail" tabindex="-1" role="dialog" aria-labelledby="sessionModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="sessionModalDetailLabel">Session Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

                <!-- //children -->
                <b>Assignments</b>
                <ul id="assignments">
                  <li v-for="assignment in session_assignments">
                    <a href="#" @click="openAssignment(assignment)">{{assignment.title}}</a>
                  </li>
                </ul>
                <assign></assign>

                <b>Quizzes</b>
                <ul id="quizzes">
                   <li v-for="quizes in session_quizes">
                    <a>{{quizes.title}}</a>
                  </li>
                </ul>

                <a href="#" @click="AddAssignmentModal()" class="btn btn-primary btn-block mb-1"><b>Create Assignment</b></a>
                <quiz class="btn btn-primary btn-block"></quiz>
              <!-- /.card-body -->
              </div>
            </div>
          </div>
        </div>

        <!-- CREATE ASSIGNMENT -->
        <div class="modal fade" id="assignmentModal" tabindex="-1" role="dialog" aria-labelledby="assignmentModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="assignmentModalLabel">Add New Assignment</h5>
                <h5 v-show="editmode" class="modal-title" id="assignmentModalLabel">Update Assignment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateAssignment() : createAssignment()" enctype='multipart/form-data'>
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="assignment_form.session_id" id="session_id" type="text" name="session_id" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': assignment_form.errors.has('session_id') }" readonly hidden>
                  <has-error :form="assignment_form" field="session_id"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="assignment_form.title" id="title" type="text" name="title" placeholder="Assignment Name"
                    class="form-control" :class="{ 'is-invalid': assignment_form.errors.has('title') }">
                  <has-error :form="assignment_form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="assignment_form.description" id="description" type="text" name="description" placeholder="Description"
                    class="form-control" :class="{ 'is-invalid': assignment_form.errors.has('description') }">
                  <has-error :form="assignment_form" field="description"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="assignment_form.marks" id="marks" type="text" name="marks" placeholder="Marks"
                    class="form-control" :class="{ 'is-invalid': assignment_form.errors.has('marks') }">
                  <has-error :form="assignment_form" field="marks"></has-error>
                </div>
                <div class="form-group">
                  <input id="file" type="file" name="file" placeholder="Enter Name" @change="fileUpload"
                    class="form-control" :class="{ 'is-invalid': assignment_form.errors.has('file') }">
                  <has-error :form="assignment_form" field="file"></has-error>
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

    </div>
</template>

<script>
    export default {
        data(){
          return{
            search:'',
            editmode: false,
            sections:{},
            courses:{},
            teachers:{},
            sessions:{},
            assign_name: '',
            assign_type: '',
            current_session_id:'',
            session_assignments:{},
            session_quizes:{},
            assignment_form : new Form(
              {
                id: '',
                session_id: '',
                title: '',
                description: '',
                marks: '',
                file: null,
                file_name:'',
              }
            ),
            form: new Form({
              id:          '',
              section_id:  '',
              course_id:   '',
              teacher_id:  '',
              meeting_url: '',
              state:       ''
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findsession?q='+query+'&page=' + page)
              .then(response => {
                this.sessions = response.data;
            });
          },
          AddSessionModal(){
            this.form.reset();
            this.editmode = false;
            $('#sessionModal').modal('show');
          },
          AddAssignmentModal(){
            this.form.reset();
            this.editmode = false;
            this.assignment_form.session_id = this.current_session_id;
            $('#assignmentModal').modal('show');
          },
          fileUpload(event){
            let file = event.target.files[0];
            this.assignment_form.file_name = file.name;
            let reader = new FileReader();
            reader.onloadend = (file)=>{
              this.assignment_form.file = reader.result;
            }
            reader.readAsDataURL(file);
          },
          EditSessionModal(session){
            console.log(session);
            this.form.fill(session);
            this.editmode = true;
            $('#sessionModal').modal('show');
          },
          DetailSessionModal(session)
          {
            $('#sessionModalDetail #title').text(session.section.classroom.title + session.section.title + ' - ' + session.course.title);
            this.session_assignments = session.assignments;
            this.session_quizes = session.quizes;

            //children
            // $('#sessionModalDetail #assignments li').remove();
            
            // for(var i = 0; i < session.assignments.length; i++ ){
            // $('#sessionModalDetail #assignments').append('<li>'+session.assignments[i].title+'</li>');
            // }
            
            // $('#sessionModalDetail #quizzes li').remove();
            // for(var i = 0; i < session.quizzes.length; i++ ){
            // $('#sessionModalDetail #quizzes').append('<li>'+session.quizzes[i].title+'</li>');
            // }

            this.current_session_id = session.id;
            $('#sessionModalDetail').modal('show');
          },
          openAssignment(assignment){
            this.assign_name = assignment.file;
            this.assign_type = assignment.type;
            this.asgn_id = assignment.id;

            $('#assignmentModalDetail #title').text(assignment.title);
            $('#assignmentModalDetail #description').text(assignment.description);
            $('#assignmentModalDetail #marks').text(assignment.marks);

            $('#assignmentModalDetail').modal('show');
          },
          deleteSession(id){
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
                  this.form.delete('api/session/'+id);
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
            axios.get('api/get_session_section').then(({data})=>(this.sections = data))
          },
          loadCourse(){
            axios.get('api/get_session_course').then(({data})=>(this.courses = data))
          },
          loadTeacher(){
            axios.get('api/get_session_teacher').then(({data})=>(this.teachers = data))
          },
          loadSession(){
            axios.get('api/session').then(({data})=>(this.sessions = data))
          },
          createSession(){
            this.$Progress.start();
            this.form.post('api/session')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#sessionModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Session Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          createAssignment(){
            this.$Progress.start();
            this.assignment_form.post('api/assignment')
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#assignmentModal').modal('hide');
              $('#sessionModalDetail').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Assignment Created Successfully'
              });
              this.$Progress.finish();
            })
            .catch(()=>{});
          },
          updateSession(){
            this.$Progress.start();
            this.form.put('api/session/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#sessionModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Session Updated Successfully'
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
            this.loadSession();
            this.loadSection();
            this.loadCourse();
            this.loadTeacher();
            Fire.$on('searching',()=>{
              let query = this.search;
              axios.get('api/findsession?q='+query).then(({data})=>(this.sessions = data))
            });
            Fire.$on('RefreshTable',()=>this.loadSession());
        }
    };
</script>
