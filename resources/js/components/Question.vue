<template>
    <div class="container">
        <div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title black" id="questionModalLabel">Question{{" - "+(this.count+1)}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="createQuestion()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="question_form.question_id" id="question_id" type="text" name="question_id" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': question_form.errors.has('question_id') }" readonly hidden>
                  <has-error :form="question_form" field="question_id"></has-error>
                </div>
                <div class="form-group">
                  <textarea v-model="question_form.content" id="content" type="textarea" name="content" placeholder="Question"
                    class="form-control" :class="{ 'is-invalid': question_form.errors.has('content') }"></textarea>
                  <has-error :form="question_form" field="content"></has-error>
                </div>

                <!-- OPTION WORK -->
                <!-- a -->
                <div class="form-group">
                  <input v-model="option_a_form.content" id="content" type="text" name="content"
                    class="form-control" :class="{ 'is-invalid': option_a_form.errors.has('content') }">
                  <has-error :form="option_a_form" field="content"></has-error>
                </div>
                <div style="text-align: left;">              
                  <input v-model="option_a_form.is_correct" id="is_correct" type="checkbox" name="is_correct" 
                    :class="{ 'is-invalid': option_a_form.errors.has('is_correct') }">
                  <label class="black">Is Correct?</label>
                  <has-error :form="option_a_form" field="is_correct"></has-error>

                </div>
                <!-- b -->
                <div class="form-group">
                  <input v-model="option_b_form.content" id="content" type="text" name="content"
                    class="form-control" :class="{ 'is-invalid': option_b_form.errors.has('content') }">
                  <has-error :form="option_b_form" field="content"></has-error>
                </div>
                <div style="text-align: left;">              
                  <input v-model="option_b_form.is_correct" id="is_correct" type="checkbox" name="is_correct" 
                    :class="{ 'is-invalid': option_b_form.errors.has('is_correct') }">
                  <label class="black">Is Correct?</label>
                  <has-error :form="option_b_form" field="is_correct"></has-error>

                </div>
                <!-- c -->
                <div class="form-group">
                  <input v-model="option_c_form.content" id="content" type="text" name="content"
                    class="form-control" :class="{ 'is-invalid': option_c_form.errors.has('content') }">
                  <has-error :form="option_c_form" field="content"></has-error>
                </div>
                <div style="text-align: left;">              
                  <input v-model="option_c_form.is_correct" id="is_correct" type="checkbox" name="is_correct" 
                    :class="{ 'is-invalid': option_c_form.errors.has('is_correct') }">
                  <label class="black">Is Correct?</label>
                  <has-error :form="option_c_form" field="is_correct"></has-error>
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
    </div>
</template>

<script>
    export default {
        data()
        {
            return{
                current_question_id: '',
                count:0,
                question_form: new Form(
                    {
                        id: '',
                        quiz_id: '',
                        content: '',
                    }
                ),
                option_a_form: new Form(
                  {
                    question_id: '',
                    content: '',
                    is_correct: '',
                    is_selected: '',
                  }
                ),
                option_b_form: new Form(
                  {
                    question_id: '',
                    content: '',
                    is_correct: '',
                    is_selected: '',
                  }
                ),
                option_c_form: new Form(
                  {
                    question_id: '',
                    content: '',
                    is_correct: '',
                    is_selected: '',
                  }
                ),
            }
        },
        methods:
        {
            createQuestion()
            {
                $('#questionModal').modal('hide');
                this.question_form.quiz_id = this.$parent.current_quiz_id;
                // this.$children.option_form.post('api/option');
                this.question_form.post('api/question')
                .then(({data})=>{
                  this.current_question_id = data;
                  this.createOption(this.option_a_form);
                  this.createOption(this.option_b_form);
                  this.createOption(this.option_c_form);
                  
                  if(this.count < (this.$parent.quiz_form.number_of_questions)-1){
                      this.count++;
                      $('#questionModal').modal('show');
                      this.question_form.reset();
                  }
                  else{
                    Toast.fire({
                        icon: 'success',
                        title: 'Quiz Created Successfully'
                    });
                    this.$Progress.start();
                    $('#questionModal').modal('hide');
                    $('#sessionModalDetail').modal('hide');
                    this.$parent.$parent.loadSession();
                    this.$Progress.finish();
                  }              
                })
                .catch(()=>{});
            },
            createOption(form)
            {
              console.log('creating option');
              form.question_id = this.current_question_id;
              form.post('api/option');
              form.reset();
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    };
</script>
