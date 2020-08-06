<template>
    <div class="container">
        <!-- INDEX VIEW -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                  <h3 class="card-title mt-2">Lectures</h3>
                  <div class="card-tools mt-2">
                  <div class="input-group input-group-sm">
                    <input v-model="search" type="text" name="table_search" class="form-control float-right" id="table_search" placeholder="Search">
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Lecture Title</th>
                      <th>Video</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="lecture in filteredList">
                      <td style="vertical-align:middle; text-align:center;">{{lecture.title}}</td>
                      <td style="text-align:center;"><iframe :src="lecture.url" allowfullscreen></iframe></td>
                      <td style="vertical-align:middle; text-align:center;">{{lecture.created_at | myDate}}</td>
                    </tr>
                    <tr v-if="lectures.data.length == 0">
                      <td colspan="7" style="text-align:center;">No Data Available</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="lectures" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                search:'',
                session_id: '',
                lectures: {data:[]},
            }
        },
        methods: {
            loadLectures(){
              if(this.$route.params.session_id){
                this.session_id = this.$route.params.session_id;
                localStorage.setItem('session_id',this.session_id);
                axios.get('api/lecture?session_id=' + this.session_id)
                .then(({data}) => {
                    this.lectures = data;
                });
              }
              // else if(localStorage.getItem('session_id',this.session_id)){
              //   this.session_id = parseInt(localStorage.getItem('session_id',this.session_id));
              //   axios.get('api/lecture?session_id=' + this.session_id)
              //   .then(({data}) => {
              //       this.lectures = data;
              //   });
              // }
              else{
                axios.get('api/lecture')
                .then(({data}) => {
                    this.lectures = data;
                });
              }
            },
            getResults(page = 1) {
                axios.get('api/lecture?page=' + page + '&session_id=' + this.session_id)
                .then(response => {
                    this.lectures = response.data;
                });
            },
        },
        computed:{
          filteredList:function(){
            return this.lectures.data.filter(lecture =>{
              return lecture.title.toLowerCase().includes(this.search.toLowerCase()) ||
              Vue.filter('myDate')(lecture.created_at).toLowerCase().includes(this.search.toLowerCase())
            })
          }
        },
        mounted() {
            this.loadLectures();
            console.log('Lecture index mounted.')
        }
    };
</script>
