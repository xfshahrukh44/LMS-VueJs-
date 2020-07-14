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
                  <opt id="option1" class="black"></opt>
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
                )
            }
        },
        methods:
        {
            createQuestion()
            {
                $('#questionModal').modal('hide');
                this.question_form.quiz_id = this.$parent.current_quiz_id;
                this.$Progress.start();
                // this.$children.option_form.post('api/option');
                this.question_form.post('api/question')
                .then(({data})=>{
                  Toast.fire({
                      icon: 'success',
                      title: 'Question Created Successfully'
                  });
                  this.current_question_id = data;
                  
                  if(this.count < (this.$parent.quiz_form.number_of_questions)-1){
                      this.count++;
                      $('#questionModal').modal('show');
                      this.question_form.reset();
                  }
                  else{
                    $('#questionModal').modal('hide');
                    $('#sessionModalDetail').modal('hide');
                  }              
                })
                .catch(()=>{});
            },
        },
        mounted() {
            console.log('Component mounted.')
        }
    };
</script>
