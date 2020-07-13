<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Dashboard</h2>
        </div>
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box light-blue">
              <span class="info-box-icon dark-blue"><i class="material-icons white">assignment</i></span>

              <div class="info-box-content">
                <span class="info-box-text white">Assignments</span>
                <span class="info-box-number white">
                  <number
                    ref="number1"
                    :from="0"
                    :to="assignment_limit"
                    :duration="1"
                    :delay="2"
                    easing="Power2.easeOut"/>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box light-blue">
              <span class="info-box-icon dark-blue"><i class="material-icons white">done_all</i></span>

              <div class="info-box-content">
                <span class="info-box-text white">Submissions</span>
                <span class="info-box-number white">
                  <number
                    ref="number1"
                    :from="0"
                    :to="submission_limit"
                    :duration="1"
                    :delay="2"
                    easing="Power2.easeOut"/>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box light-blue">
              <span class="info-box-icon dark-blue"><i class="material-icons white">laptop_chromebook</i></span>

              <div class="info-box-content">
                <span class="info-box-text white">Live Classes</span>
                <span class="info-box-number white">
                  <number
                    ref="number1"
                    :from="0"
                    :to="live_class_limit"
                    :duration="1"
                    :delay="2"
                    easing="Power2.easeOut"/>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box light-blue b1">
              <span class="info-box-icon dark-blue"><i class="material-icons white">record_voice_over</i></span>

              <div class="info-box-content">
                <span class="info-box-text white">Teachers</span>
                <span class="info-box-number white">
                  <number
                    ref="number1"
                    :from="0"
                    :to="active_teacher_limit"
                    :duration="1"
                    :delay="2"
                    easing="Power2.easeOut"/>
                    out of
                    <number
                    ref="number1"
                    :from="0"
                    :to="teacher_limit"
                    :duration="1"
                    :delay="2"
                    easing="Power2.easeOut"/>
                    present today
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box light-blue">
              <span class="info-box-icon dark-blue"><i class="material-icons white">people</i></span>

              <div class="info-box-content">
                <span class="info-box-text white">Students</span>
                <span class="info-box-number white">
                  <number
                    ref="number1"
                    :from="0"
                    :to="student_limit"
                    :duration="1"
                    :delay="2"
                    easing="Power2.easeOut"/>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box light-blue">
              <span class="info-box-icon dark-blue"><i class="material-icons white">done_all</i></span>

              <div class="info-box-content">
                <span class="info-box-text"><router-link to="/attendances" class="white">Time Spent this month</router-link></span>
                <span class="info-box-number white" id="time">
                  
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          
        </div>


        <div class="row mt-2">
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <line-chart :data="{'2017-05-13': 2, '2017-05-14': 5}"></line-chart>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <pie-chart :donut="true" :data="ChartData"></pie-chart>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <column-chart :data="ChartData"></column-chart>
              </div>
            </div>
          </div>
          <div class="col-sm-12 mt-2">
            <div class="card">
              <div class="card-header">
                <h4>Geo Chart</h4>
              </div>
              <div class="card-body">
                <geo-chart :data="[['Pakistan', 44]]"></geo-chart>
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
                LineChartData: {},
                ChartData: {},
                assignment_limit:'',
                submission_limit:'',
                live_class_limit:'',
                teacher_limit:'',
                active_teacher_limit: '',
                student_limit:'',
                work_hours:'',
                check:'',
                days:''
            }
        },
        methods:{
          loadData(){
              axios.get('api/chart_data')
              .then(({data})=>{
                  this.ChartData = {
                      "admin"  : data[0].user_count,
                      "student": data[1].user_count,
                      "teacher": data[2].user_count,
                      "user"   : data[3].user_count,
                  }
                })
          },
          loadNumber(){
              axios.get('api/adminhome')
              .then(({data})=>{
                this.assignment_limit = data.assignment_count;
                this.submission_limit = data.submission_count;
                this.live_class_limit = data.live_class_count;
                this.teacher_limit = data.teacher_count;
                this.active_teacher_limit = data.active_teacher_count;
                this.student_limit = data.student_count;
                this.work_hours = data.wh;
                this.check = data.check;
                this.days = data.total_days;
              }).then(()=>{
                var h = parseInt(this.work_hours.substring(1, 3));
                if(this.days > 0){
                    for(var i = 0; i<this.days; i++){
                        h+=24;
                    }
                }
                if(this.check == 'out'){
                    if(h < 10){
                        document.getElementById("time").innerHTML = "0"+h+this.work_hours.substring(3,9);
                    }
                    else if(h >=10){
                        document.getElementById("time").innerHTML = h+this.work_hours.substring(3,9);
                    }
                    
                }
              }).then(()=>{
                var h = parseInt(this.work_hours.substring(1, 3));
                var m = parseInt(this.work_hours.substring(4, 6));
                var s = parseInt(this.work_hours.substring(7, 9));
                if(this.days > 0){
                    for(var i = 0; i<this.days; i++){
                        h+=24;
                    }
                }
                if(this.check == 'in'){
                  var x = setInterval(function(){
                      if(s < 9){
                          s++;
                          if(m < 10){
                              if(h < 10){
                                  var time = "0"+h+":0"+m+":0"+s;
                              }
                              else if(h >= 10 ){
                                  var time = h+":0"+m+":0"+s;
                              }
                          }
                          else if(m >= 10){
                              if(h < 9){
                                  var time = "0"+h+":"+m+":0"+s;
                              }
                              else if(h >= 9 ){
                                  var time = h+":"+m+":0"+s;
                              }
                          }
                      }
                      else if(s >= 9 && s < 59){
                          s++;
                          if(m < 10){
                              if(h < 10){
                                  var time = "0"+h+":0"+m+":"+s;
                              }
                              else if(h >= 10 ){
                                  var time = h+":0"+m+":"+s;
                              }
                          }
                          else if(m >= 10){
                              if(h < 10){
                                  var time = "0"+h+":"+m+":"+s;
                              }
                              else if(h >= 10 ){
                                  var time = h+":"+m+":"+s;
                              }
                          }
                      }
                      else if(s >= 59){
                          s = 0;
                          if(m < 9){
                              m++;
                              if(h < 10){
                                  var time = "0"+h+":0"+m+":0"+s;
                              }
                              else if(h >= 10 ){
                                  var time = h+":0"+m+":0"+s;
                              }
                          }
                          else if(m >= 9 && m < 59){
                              m++;
                              if(h < 10){
                                  var time = "0"+h+":"+m+":0"+s;
                              }
                              else if(h >= 10 ){
                                  var time = h+":"+m+":0"+s;
                              }
                          }
                          else if(m >= 59){
                              m = 0;
                              if(h < 9){
                                  h++;
                                  var time = "0"+h+":0"+m+":0"+s;
                              }
                              else if(h >= 9 ){
                                  h++;
                                  var time = h+":0"+m+":0"+s;
                              }
                          }
                          
                      }
                      document.getElementById("time").innerHTML = time;

                      },1000);
                  }
                  $(document).on('click','#check-out',function(){
                      clearInterval(x);
                  });
             });
          },
          loadLineChartData()
          {
            axios.get('api/get_line')
          },
        },
        mounted() {
          this.loadNumber();
          this.loadData();
          //height adjustment
          $('.info-box').height($('.b1').height());
        }
    };
</script>