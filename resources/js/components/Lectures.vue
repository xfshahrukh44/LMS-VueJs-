<template>
    <div>
        <a data-toggle = "modal" data-target = "#lectureModal" v-if="$gate.isAdmin() || $gate.isTeacher()">
            <i class="material-icons white">menu_book</i>
            <br>
            <b>Lecture</b>
        </a>
        <button class="btn btn-block" v-if="$gate.isStudent()">
          <div class="row justify-content-center white">
            <i class="material-icons white mr-1">menu_book</i>
            <b>View Lectures</b>
          </div>
        </button>
        <!-- CREATE/EDIT VIEW -->
        <div class="modal fade" id="lectureModal" tabindex="-1" role="dialog" aria-labelledby="lectureModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title black" id="lectureModalLabel">Add New Lecture</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="createLecture()">
              <div class="modal-body">
                <div class="form-group">
                  <input v-model="lecture_form.session_id" id="session_id" type="text" name="session_id" placeholder="Enter Name"
                    class="form-control" :class="{ 'is-invalid': lecture_form.errors.has('session_id') }" readonly hidden>
                  <has-error :form="lecture_form" field="session_id"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="lecture_form.title" id="title" type="text" name="title" placeholder="Lecture Title"
                    class="form-control" :class="{ 'is-invalid': lecture_form.errors.has('title') }">
                  <has-error :form="lecture_form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <input v-model="lecture_form.url" id="url" type="text" name="url" placeholder="URL"
                    class="form-control" :class="{ 'is-invalid': lecture_form.errors.has('url') }">
                  <has-error :form="lecture_form" field="url"></has-error>
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
        data(){
            return{
                current_lecture: {},
                lecture_form: new Form(
                    {
                        id: '',
                        session_id: '',
                        title: '',
                        url: '',
                    }
                )
            }
        },
        methods:
        {
            createLecture()
            {
                this.lecture_form.session_id = this.$parent.current_session_id;
                this.$Progress.start();
                this.lecture_form.post('api/lecture')
                .then(({data})=>{
                // Fire.$emit('RefreshTable');
                $('#lectureModal').modal('hide');
                $('#sessionModalDetail').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Lecture Created Successfully'
                });
                this.$Progress.finish();
                this.$parent.methods.loadSession();
                })
                .catch(()=>{});
            },
        },
        mounted() {
            console.log('lecture mounted')
        }
    };
</script>