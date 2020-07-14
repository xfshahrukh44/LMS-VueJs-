<template>
    <div class="">
      <form>
        <div class="form-group">
          <input v-model="option_form.question_id" id="question_id" type="text" name="question_id" placeholder="Enter Name"
            class="form-control" :class="{ 'is-invalid': option_form.errors.has('question_id') }" readonly hidden>
          <has-error :form="option_form" field="question_id"></has-error>
        </div>
        <div class="form-group">
          <input v-model="option_form.content" id="content" type="text" name="content"
            class="form-control" :class="{ 'is-invalid': option_form.errors.has('content') }">
          <has-error :form="option_form" field="content"></has-error>
        </div>
        <div style="text-align: left;">
          
          <input v-model="option_form.is_correct" id="is_correct" type="checkbox" name="is_correct" 
            :class="{ 'is-invalid': option_form.errors.has('is_correct') }">
          <label>Is Correct?</label>
          <has-error :form="option_form" field="is_correct"></has-error>

          <input v-model="option_form.is_selected" id="is_selected" type="checkbox" name="is_selected" 
            :class="{ 'is-invalid': option_form.errors.has('is_selected') }">
          <label>Is Selected</label>
          <has-error :form="option_form" field="is_selected"></has-error>

        </div>
      </form>
    </div>
</template>

<script>
    export default {
        data()
        {
            return{
                current_option_id: '',
                option_form: new Form({
                    id: '',
                    question_id: '',
                    content: '',
                    is_correct: '',
                    is_selected: '',
                })
            }
        },
        methods:
        {

        },
        mounted() {
            console.log('Option Mounted.')
        }
    };
</script>
