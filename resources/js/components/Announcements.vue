<template>
    <div class="container">
        <div class="row mt-3 ml-1 mb-2">
            <h2 class="pr-2">Announcements</h2>
            <button class="btn btn-success xs" id="add_announcement" @click="AddAnnouncementModal" v-if="$gate.isAdmin() || $gate.isTeacher()">
                <i class="fas fa-plus fa-lg"></i>
            </button>
            
            <!-- <button class="btn btn-info xs" @click="forceRerender">
              refresh
            </button> -->
        </div>

        <!-- INDEX VIEW -->
        <div class="row">
          <div class="card card-primary col-md-12 ml-3" v-for="announcement in announcements">
              <div class="dropdown" style="text-align:right;">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="material-icons">settings</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                  <a href="#" @click="EditAnnouncementModal(announcement)" v-if="$gate.isAdmin() || announcement.id == form.user_id">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons blue mr-1">create</i>
                        Edit
                      </div>
                    </button>
                  </a>
                  <a href="#" @click="deleteAnnouncement(announcement.id)" v-if="$gate.isAdmin()">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons red mr-1">delete</i>
                        Delete
                      </div>
                    </button>
                  </a>
                  <div class="dropdown-divider" v-if="$gate.isAdmin()"></div>

                  <a href="#" @click="DetailAnnouncementModal(announcement)" v-if="$gate.isAdmin() || $gate.isTeacher()">
                    <button class="dropdown-item" type="button">
                      <div class="row">
                        <i class="material-icons mr-1 green">add</i>
                        Create New..
                      </div>
                    </button>
                  </a>
                  <div class="dropdown-divider" v-if="$gate.isAdmin() || $gate.isTeacher()"></div>
                </div>
              </div>
            <!-- <div class="card-header" v-if="$gate.isAdmin() || $gate.isTeacher()">
              
            </div> -->
            <div class="card-body box-profile">
              <h1 class="profile-username text-center">{{announcement.title}}</h1>
              <!-- //children -->   
              <p><h5>{{announcement.content}}</h5></p>
            </div>
            <!-- Buttons -->
            <!-- /.card-body -->
            <div class="card-footer" id="cardFooter">
              <!-- <a href="#" class="btn btn-warning btn-block mb-1">
                Join Meeting
              </a> -->
            </div>
          </div>
        </div>

        <!-- CREATE/EDIT VIEW -->
        <div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="announcementModalLabel">Add New Announcement</h5>
                <h5 v-show="editmode" class="modal-title" id="announcementModalLabel">Update Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editmode ? updateAnnouncement() : createAnnouncement()">
                <div class="modal-body">
                    <div class="form-group">
                    <input v-model="form.title" id="title" type="text" name="title" placeholder="Enter Title"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                    <has-error :form="form" field="title"></has-error>
                    </div>
                    <div class="form-group">
                    <textarea v-model="form.content" id="content" type="text" name="content" placeholder="Enter Content"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('content') }"></textarea>
                    <has-error :form="form" field="content"></has-error>
                    </div>
                    <div style="text-align: left;">              
                    <input v-model="form.for_teachers" id="for_teachers" type="checkbox" name="for_teachers" 
                        :class="{ 'is-invalid': form.errors.has('for_teachers') }">
                    <label class="black">For Teachers</label>
                    <has-error :form="form" field="for_teachers"></has-error>
                    </div>
                    <div style="text-align: left;">              
                    <input v-model="form.for_students" id="for_students" type="checkbox" name="for_students" 
                        :class="{ 'is-invalid': form.errors.has('for_students') }">
                    <label class="black">For Students</label>
                    <has-error :form="form" field="for_students"></has-error>
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
            announcements:{},
            current_announcement_id:'',
            form: new Form({
              id: '',
              user_id: '',
              title: '',
              content: '',
              for_teachers: 0,
              for_students: 0,
            })
          }
        },
        methods:{
          getResults(page = 1) {
            let query = this.search;
            axios.get('api/findannouncement?q='+query+'&page=' + page)
              .then(response => {
                this.announcements = response.data;
            });
          },
          AddAnnouncementModal(){
            this.form.reset();
            this.editmode = false;
            $('#announcementModal').modal('show');
          },
          EditAnnouncementModal(announcement){
            this.form.fill(announcement);
            this.editmode = true;
            $('#announcementModal').modal('show');
          },
          DetailAnnouncementModal(announcement){
            this.current_announcement_id = announcement.id;
            $('#announcementModalDetail').modal('show');
          },
          updateAnnouncementId(id){
            this.current_announcement_id = id;
            // $('#announcementModalDetail').modal('hide');
            this.$router.push({name: 'lectureIndex', params: {announcement_id: this.current_announcement_id}});
          },
          deleteAnnouncement(id){
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
                  this.form.delete('api/announcement/'+id);
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  );
                  this.loadAnnouncement();
                }
            });
          },
          loadAnnouncement(){
            axios.get('api/announcement').then(({data})=>(this.announcements = data))
          },
          createAnnouncement(){
            axios.get('api/get_user_name')
            .then(({data}) => {
                this.form.user_id = data.id;
                if(this.form.for_teachers == true){
                    this.form.for_teachers = 1;
                }
                if(this.form.for_students == true){
                    this.form.for_students = 1;
                }
            })
            .then(() => {
                this.$Progress.start();
                this.form.post('api/announcement')   
            })
            .then(()=>{
              Fire.$emit('RefreshTable');
              $('#announcementModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Announcement Created Successfully'
              });
              this.$Progress.finish();
              this.loadAnnouncement();
            })
            .catch(()=>{});
          },
          updateAnnouncement(){
            this.$Progress.start();
            this.form.put('api/announcement/'+this.form.id)
            .then(()=>{
              Fire.$emit('searching');
              $('#announcementModal').modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Announcement Updated Successfully'
              });
              this.$Progress.finish();
              this.loadAnnouncement();
            })
            .catch(()=>{});
          },
        },
        mounted() {
            this.loadAnnouncement();
            axios.get('api/get_user_name')
            .then(({data}) => {
                this.form.user_id = data.id;
            });
            // Fire.$on('searching',()=>{
            //   let query = this.search;
            //   axios.get('api/findannouncement?q='+query).then(({data})=>(this.announcements = data))
            // });
            Fire.$on('RefreshTable',()=>this.loadAnnouncement());
        }
    };
</script>


<style scoped lang="scss">
.profile-username
{
    font-size: 40px;
    text-align: left !important;
    color: black;
}
.card-header{
  background-color:white !important;
  text-align: right;
  padding: 0;
  width: 101%;
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

