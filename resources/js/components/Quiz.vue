<template>
    <div>
        <a data-toggle = "modal" data-target = "#quizModal">
            <i class="material-icons white">spellcheck</i>
            <br>
            <b>Quiz</b>
        </a>
        <!-- CREATE/EDIT VIEW -->
        <div class="modal fade" id="quizModal" tabindex="-1" role="dialog" aria-labelledby="quizModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title black" id="quizModalLabel">Add New Quiz</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="createQuiz()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="quiz_form.session_id" id="session_id" type="text" name="session_id" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': quiz_form.errors.has('session_id') }" readonly hidden>
                  <has-error :form="quiz_form" field="session_id"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="quiz_form.title" id="title" type="text" name="title" placeholder="Quiz Title"
                    class="form-control" :class="{ 'is-invalid': quiz_form.errors.has('title') }">
                  <has-error :form="quiz_form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="quiz_form.marks" id="marks" type="number" name="marks" placeholder="Max Marks"
                    class="form-control" :class="{ 'is-invalid': quiz_form.errors.has('marks') }">
                  <has-error :form="quiz_form" field="marks"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="quiz_form.number_of_questions" id="number_of_questions" type="number" name="number_of_questions" placeholder="# of Questions"
                    class="form-control" :class="{ 'is-invalid': quiz_form.errors.has('number_of_questions') }">
                  <has-error :form="quiz_form" field="number_of_questions"></has-error>
                </div> 
                <div class="form-group">
                  <input v-model="quiz_form.minutes" id="minutes" type="number" name="minutes" placeholder="Minutes"
                    class="form-control" :class="{ 'is-invalid': quiz_form.errors.has('minutes') }">
                  <has-error :form="quiz_form" field="minutes"></has-error>
                </div> 
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Create</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        <!-- QUESTION -->
        <question></question>
    </div>
</template>

<script>
    export default {
        data(){
            return{
              current_quiz: {},
              quiz_form: new Form(
                    {
                        id: '',
                        session_id: '',
                        title: '',
                        marks: '',
                        number_of_questions: '',
                        minutes: '',
                    }
                )
            }
        },
        methods:
        {
            createQuiz()
            {
                this.quiz_form.session_id = this.$parent.current_session_id;
                // this.$Progress.start();
                this.quiz_form.post('api/quiz')
                .then(({data})=>{
                // Fire.$emit('RefreshTable');
                $('#quizModal').modal('hide');
                // Toast.fire({
                //     icon: 'success',
                //     title: 'Quiz Created Successfully'
                // });
                this.$Progress.finish();
                this.current_quiz_id =  data;
                $('#questionModal').modal('show');

                })
                .catch(()=>{});
            },
        },
        mounted() {
            console.log('quiz mounted')
        }
    };
</script>
