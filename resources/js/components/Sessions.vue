<template>
    <div class="container" v-if="renderComponent">
        <div class="row mt-3 ml-1 mb-2">
            <h2 class="pr-2">Your Classes</h2>
            <button class="btn btn-success xs" id="add_session" @click="AddSessionModal" v-if="$gate.isAdmin()">
                <i class="fas fa-plus fa-lg"></i>
            </button>
            
            <!-- <button class="btn btn-info xs" @click="forceRerender">
              refresh
            </button> -->
        </div>

        <!-- INDEX VIEW -->
        <div class="row">
          <div class="card card-primary col-md-3 ml-3" v-if="sessions.data.length > 0" v-for="session in filteredList">
            <div class="card-header" v-if="$gate.isAdmin() || $gate.isTeacher()">
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="material-icons">settings</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                  <a href="#" @click="EditSessionModal(session)" v-if="$gate.isAdmin()">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons blue mr-1">create</i>
                        Edit
                      </div>
                    </button>
                  </a>
                  <a href="#" @click="deleteSession(session.id)" v-if="$gate.isAdmin()">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons red mr-1">delete</i>
                        Delete
                      </div>
                    </button>
                  </a>
                  <div class="dropdown-divider" v-if="$gate.isAdmin()"></div>

                  <a href="#" @click="DetailSessionModal(session)" v-if="$gate.isAdmin() || $gate.isTeacher()">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons mr-1 green">add</i>
                        Create New..
                      </div>
                    </button>
                  </a>
                  <div class="dropdown-divider" v-if="$gate.isAdmin() || $gate.isTeacher()"></div>

                  <a href="#" @click="changeMeetingState(session)" v-if="$gate.isAdmin() || $gate.isTeacher()">
                    <button class="dropdown-item" type="button" v-if="session.state == 'disable'">
                      <div class="row">
                        <i class="material-icons mr-1 green">toggle_on</i>
                        Enable Meeting
                      </div>
                    </button>
                    <button class="dropdown-item" type="button" v-if="session.state == 'enable'">
                      <div class="row">
                        <i class="material-icons mr-1 red">toggle_off</i>
                        Disable Meeting
                      </div>
                    </button>
                  </a>
                  <a href="#" @click="EditSessionModal(session)" v-if="$gate.isAdmin() || $gate.isTeacher()">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons mr-1 blue">link</i>
                        Change Meeting URL
                      </div>
                    </button>
                  </a>
                </div>
              </div>
            </div>
            <div class="card-body box-profile">

              <h1 class="profile-username text-center">{{session.course.title}}</h1>
              
              <div id="actions" style="text-align: right;">
                
              </div>

              <p class="text-muted text-center">{{session.teacher.name}}</p>

              <!-- //children -->   
              <h5 class="row"><i class="material-icons mr-1">assignment</i>Assignments</h5>
              <ul id="assignments">
                <li v-for="assignment in session.assignments">
                  <a href="#" @click="openAssignment(assignment)">{{assignment.title}}</a>
                </li>
              </ul>
              <assign></assign>

              <h5 class="row"><i class="material-icons mr-1">spellcheck</i>Quizzes</h5>
              <ul id="quizzes">
                <li v-for="quiz in session.quizzes">
                  <a href="#" @click="openQuiz(quiz)">{{quiz.title}}</a>
                  <a class="float-right red" href="#" @click="submitQuiz(quiz)" :id="'quiz' + quiz.id" v-if="$gate.isStudent()">Start Quiz</a>
                </li>     
              </ul>

              <h5 class="row">
                <i class="material-icons mr-1">menu_book</i>
                Lectures ({{session.lectures.length}})
                <a href="#" @click="updateSessionId(session.id)">
                  <span class="material-icons text-align-right">
                    call_made
                  </span>
                </a>
              </h5>
              
            </div>
            <!-- Buttons -->
            <!-- /.card-body -->
            <div class="card-footer" id="cardFooter">
              <!-- <a id="createButton" class="btn btn-primary btn-block mb-1" href="#" @click="DetailSessionModal(session)" v-if="$gate.isAdmin() || $gate.isTeacher()">
                Create New...
              </a> -->
              <!-- <a class="btn btn-primary btn-block mb-1" @click="updateSessionId(session.id)">
                <div class="row justify-content-center white">
                  <i class="material-icons white mr-1">menu_book</i>
                  <b>View Lectures</b>
                </div>
              </a> -->
              <a :href="session.meeting_url" v-if="session.state == 'enable'" class="btn btn-warning btn-block mb-1">
                Join Meeting
              </a>
            </div>
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
                <div class="form-group" v-if="$gate.isAdmin()">
                  <select v-model="form.section_id" id="section_id" name="section_id" class="form-control" :class="{ 'is-invalid': form.errors.has('section_id') }">
                    <option value="">Select Section</option>
                    <option v-for="section in sections" :value="section.id">{{section.classroom.title + ' - ' + section.title}}</option>
                  </select>
                </div>
                <div class="form-group" v-if="$gate.isAdmin()">
                  <select v-model="form.course_id" id="course_id" name="course_id" class="form-control" :class="{ 'is-invalid': form.errors.has('course_id') }">
                    <option value="">Select Course</option>
                    <option v-for="course in courses" :value="course.id">{{course.title}}</option>
                  </select>
                </div>
                <div class="form-group" v-if="$gate.isAdmin()">
                  <select v-model="form.teacher_id" id="teacher_id" name="teacher_id" class="form-control" :class="{ 'is-invalid': form.errors.has('teacher_id') }">
                    <option value="">Select Teacher</option>
                    <option v-for="teacher in teachers" :value="teacher.id">{{teacher.name}}</option>
                  </select>
                </div>
                <div class="form-group" v-if="$gate.isAdmin() || $gate.isTeacher()">
                  <input v-model="form.meeting_url" id="meeting_url" type="text" name="meeting_url" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('meeting_url') }">
                  <has-error :form="form" field="meeting_url"></has-error>
                </div>
                <div class="form-group" v-if="$gate.isAdmin()">
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

        <!-- FOUR BUTTONS -->
        <div class="modal fade" id="sessionModalDetail" tabindex="-1" role="dialog" aria-labelledby="sessionModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-body row mt-2">

                <assign></assign>   
                <!-- Buttons -->
                <a href="#" @click="AddAssignmentModal()" class="btn btn-primary btn-app col-sm-4" v-if="$gate.isAdmin() || $gate.isTeacher()">
                  <!-- <div class="row justify-content-center"> -->
                    <i class="material-icons white">assignment</i>
                    <br>
                    <b>Assignment</b>
                  <!-- </div> -->
                </a>
                <quiz class="btn btn-primary btn-app col-sm-3" v-if="$gate.isAdmin() || $gate.isTeacher()"></quiz>
                <lecture class="btn btn-primary btn-app col-sm-4"></lecture>
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

        <!-- QUIZ DETAIL MODAL -->
        <div class="modal fade" id="quizModalDetail" tabindex="-1" role="dialog" aria-labelledby="quizModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title black" id="quizModalDetailLabel">Quiz</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center black">{{current_quiz_detail + current_quiz.title}}</h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li v-for="question in current_quiz.questions">
                    {{question.content}}
                    <ul>
                      <li v-for="option in question.options">
                        {{option.content}}
                        <!-- CHECKBOX HERE -->
                      </li>
                    </ul>
                  </li>
                </ul>

              <!-- /.card-body -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- QUIZ SUBMIT MODAL -->
        <div class="modal fade" id="submitQuizModalDetail" tabindex="-1" role="dialog" aria-labelledby="submitQuizModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h2 class="modal-title black" id="submitQuizModalDetailLabel">{{current_quiz_detail + current_quiz.title}}</h2>
              </div>
              <div class="modal-header justify-content-center">
                

              <div class="base-timer">
                <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                  <g class="base-timer__circle">
                    <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                    <path
                      :stroke-dasharray="circleDasharray"
                      class="base-timer__path-remaining"
                      :class="remainingPathColor"
                      d="
                        M 50, 50
                        m -45, 0
                        a 45,45 0 1,0 90,0
                        a 45,45 0 1,0 -90,0
                      "
                    ></path>
                  </g>
                </svg>
                <span class="base-timer__label">{{ formattedTimeLeft }}</span>
              </div>         


              </div>
              <div class="modal-body">

                <ol class="list-group list-group-unbordered mb-3">
                  <li v-for="question in current_quiz.questions">
                    <h4><b>{{question.content}}</b></h4>
                    <ol type="A">
                      <li v-for="option in question.options" action="">
                        <input type="radio" :id="option.id" :name="question.id">
                        {{option.content}}
                        <!-- CHECKBOX HERE -->
                      </li>
                    </ol>
                  </li>
                </ol>
                <a class="btn btn-primary float-right col-md-3" @click="markQuiz(current_quiz)">Submit</a>

              <!-- /.card-body -->
              </div>
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>

    </div>
</template>

<script>

    // Timer Code Variables

    const FULL_DASH_ARRAY = 283;
    var WARNING_THRESHOLD = 0;
    var ALERT_THRESHOLD = 0;

    var COLOR_CODES = {
      info: {
        color: "green"
      },
      warning: {
        color: "orange",
        threshold: WARNING_THRESHOLD
      },
      alert: {
        color: "red",
        threshold: ALERT_THRESHOLD
      }
    };

    var TIME_LIMIT = 0;

    // Timer Code Variables End

    export default {
        data(){
          return{
            renderComponent: true,
            timeLimit: 0,
            timePassed: 0,
            timePassed: 0,
            timerInterval: null,
            trigger:0,
            search:'',
            editmode: false,
            sections:{},
            courses:{},
            teachers:{},
            sessions:{data: []},
            assign_name: '',
            assign_type: '',
            current_session_id:'',
            session_assignments:{},
            session_quizzes:{},
            current_quiz: {},
            current_quiz_detail: '',
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
          forceRerender(){
            // this.renderComponent = false;
            // this.$nextTick().then(() => {
            //   this.renderComponent = true;
            // });
            this.$forceUpdate();
            // this.loadSession();
          },  
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
            this.current_session_id = session.id;
            $('#sessionModalDetail').modal('show');
          },
          updateSessionId(id){
            this.current_session_id = id;
            // $('#sessionModalDetail').modal('hide');
            this.$router.push({name: 'lectureIndex', params: {session_id: this.current_session_id}});
          },
          changeMeetingState(session){
            axios.get('api/change_meeting_state?session_id=' + session.id)
            // axios.put('api/change_meeting_state'+session.id)
            .then(() => {
              this.loadSession();
            });
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
          openQuiz(quiz)
          {
            this.current_quiz = quiz;
            this.current_quiz_detail = quiz.session.section.classroom.title + quiz.session.section.title + ' - ' + quiz.session.course.title + ' | ' + quiz.session.teacher.name + " | ";
            $('#quizModalDetail').modal('show');
          },
          submitQuiz(quiz)
          {
            $(window).bind("beforeunload",function(event) {
              return "You have some unsaved changes";
            });

            this.current_quiz = quiz;

            //TIMER WORK
            TIME_LIMIT = this.current_quiz.minutes * 60;
            COLOR_CODES.warning.threshold = TIME_LIMIT * .3;
            COLOR_CODES.alert.threshold = TIME_LIMIT * .1;
            console.log(WARNING_THRESHOLD, ALERT_THRESHOLD);

            this.current_quiz_detail = quiz.session.section.classroom.title + quiz.session.section.title + ' - ' + quiz.session.course.title + ' | ' + quiz.session.teacher.name + " | ";
            console.log(quiz.session.course)
            axios.get('api/checkquizsubmission?quiz_id=' + quiz.id)
            .then(({data}) => {
              if(data == 0)
              {
                $('#submitQuizModalDetail').modal('hide');
                Toast.fire({
                  icon: 'warning',
                  title: 'Quiz already submitted.'
                });        
              }
              else
              {
                $('#submitQuizModalDetail').modal({backdrop: 'static', keyboard: false});
                $('#submitQuizModalDetail').modal('show');
                this.trigger++;
                if(this.trigger == 1){
                  this.startTimer();
                }
              }
            })
          },
          markQuiz(current_quiz)
          {
            var total_marks = current_quiz.marks;
            var question_marks = current_quiz.marks / current_quiz.number_of_questions;
            var marks_obtained = 0;

            for(var i = 0; i < current_quiz.questions.length; i++)
            {
              for(var j = 0; j < current_quiz.questions[i].options.length; j++)
              {
                var radio_id = '#' + current_quiz.questions[i].options[j].id;
                var option = current_quiz.questions[i].options[j];
                // console.log(radio_id);
                if($(radio_id).is(':checked') && option.is_correct == 1)
                {
                  marks_obtained += question_marks;
                }
              }
            }
            axios.post('api/quizsubmission?marks_obtained=' + marks_obtained + '&quiz_id=' + current_quiz.id)
            .then(({data}) => 
            {
              $('#submitQuizModalDetail').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Submitted successfully.'
              });
            })
            $('#sessionModalDetail').modal('hide');
            console.log(marks_obtained);
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
              this.loadSession();
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
              this.loadSession();
            })
            .catch(()=>{});
          },
          onTimesUp() {
            clearInterval(this.timerInterval);
            this.markQuiz(this.current_quiz);
          },
          startTimer() {
            this.timerInterval = setInterval(() => (this.timePassed += 1), 1000);
          },
        },
        computed: {
          filteredList:function(){
            return this.sessions.data.filter(session =>{
              return (session.section.classroom.title + session.section.title).toLowerCase().includes(this.search.toLowerCase()) ||
              (session.section.classroom.title + '-' + session.course.title).toLowerCase().includes(this.search.toLowerCase()) ||
              session.teacher.name.toLowerCase().includes(this.search.toLowerCase())
            })
          },

          circleDasharray() {
            return `${(this.timeFraction * FULL_DASH_ARRAY).toFixed(0)} 283`;
          },

          formattedTimeLeft() {
            const timeLeft = this.timeLeft;
            const minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;

            if (seconds < 10) {
                seconds = `0${seconds}`;
            }

            return `${minutes}:${seconds}`;
          },

          timeLeft() {
            return TIME_LIMIT - this.timePassed;
          },

          timeFraction() {
            const rawTimeFraction = this.timeLeft / TIME_LIMIT;
            return rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
          },

          remainingPathColor() {
            const { alert, warning, info } = COLOR_CODES;

            if (this.timeLeft <= alert.threshold) {
                return alert.color;
            } else if (this.timeLeft <= warning.threshold) {
                return warning.color;
            } else {
                return info.color;
            }
          }
        },
        watch: {
            timeLeft(newValue) {
                if (newValue === 0) {
                    this.onTimesUp();
                }
            },
            testProp:function(newVal,oldVal){
                this.startTimer();
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


<style scoped lang="scss">
.card-header{
  background-color:white !important;
  text-align: right;
  padding: 0;
  width: 106%;
}
.card-body{
  padding-top:0;
}
.base-timer {
  position: relative;
  width: 200px;
  height: 200px;

  &__svg {
    transform: scaleX(-1);
  }

  &__circle {
    fill: none;
    stroke: none;
  }

  &__path-elapsed {
    stroke-width: 5px;
    stroke: white;
  }

  &__path-remaining {
    stroke-width: 5px;
    stroke-linecap: round;
    transform: rotate(90deg);
    transform-origin: center;
    transition: 1s linear all;
    fill-rule: nonzero;
    stroke: currentColor;

    &.green {
      color: rgb(65, 184, 131);
    }

    &.orange {
      color: orange;
    }

    &.red {
      color: red;
    }
  }

  &__label {
    position: absolute;
    width: 200px;
    height: 200px;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
  }
}
</style>

