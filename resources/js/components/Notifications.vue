<template>
    <div class="container" id="box">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
          <span class="material-icons mr-1">notifications_active</span>
          <span class="badge badge-danger navbar-badge mt-3" id="main_count"></span>
        </a>
        <!-- NO NOTIFICATIONS FOUND -->
        <div v-if="main_count == 0" class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px; width:290px;">
          <a href="#" class="dropdown-item dropdown-footer">
            <div class="row">
                <i class="material-icons ml-1">sentiment_dissatisfied</i>
                <span class="ml-1 mt-1">No new notifications</span>
            </div>
          </a>
        </div>
        <!-- NOTIFICATIONS FOUND -->
        <div v-else class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px; width:290px;">
          <!-- General Announcement -->
          <router-link to="/announcement" class="dropdown-item" v-if="announcement_count > 0">
            <div class="row">
                <i class="material-icons ml-2">campaign</i>
                <span class="ml-1 mb-1" id="announcement_count"></span>
            </div>
          </router-link>
          <!-- Assignments -->
          <router-link to="/sessions" class="dropdown-item" v-if="assignment_count > 0">
            <div class="row">
                <i class="material-icons ml-2">assignment</i>
                <span class="ml-1 mb-1" id="assignment_count"></span>
            </div>
          </router-link>
          <!-- Quizzes -->
          <router-link to="/sessions" class="dropdown-item" v-if="quiz_count > 0">
            <div class="row">
                <i class="material-icons ml-2">spellcheck</i>
                <span class="ml-1 mb-1" id="quiz_count"></span>
            </div>
          </router-link>
          <!-- Lectures -->
          <router-link to="/lectures" class="dropdown-item" v-if="lecture_count > 0">
            <div class="row">
                <i class="material-icons ml-2">menu_book</i>
                <span class="ml-1 mb-1" id="lecture_count"></span>
            </div>
          </router-link>
          <!-- Mark all as read -->
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer" @click="mark_all_read">
            <div class="row">
                <i class="material-icons ml-2 mt-2 green">done_all</i>
                <span class="ml-1 mb-1 mt-2">Mark all as read</span>
            </div>
          </a>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                main_count: 0,
                announcement_count: 0,
                assignment_count: 0,
                quiz_count: 0,
                lecture_count: 0,
            }
        },
        methods:{
            loadNotifications(){
                axios.get('api/notifications')
                .then(({data}) => {
                    this.main_count = data.main_count;
                    this.announcement_count = data.announcement_count;
                    this.assignment_count = data.assignment_count;
                    this.quiz_count = data.quiz_count;
                    this.lecture_count = data.lecture_count;
                    if(data.main_count == 0)
                    {
                        $('#main_count').hide();
                    }
                    else
                    {
                        $('#main_count').show();
                    }
                })
                .then(() => {
                    $('#main_count').text(this.main_count);
                    $('#announcement_count').text(this.announcement_count + ' new announcement(s)');
                    $('#assignment_count').text(this.assignment_count + ' new assignment(s)');
                    $('#quiz_count').text(this.quiz_count + ' new quiz(zes)');
                    $('#lecture_count').text(this.lecture_count + ' new lecture(s)');
                });
            },
            mark_all_read(){
                axios.get('api/mark_all_read')
                .then(() => {
                    this.loadNotifications();
                });
            },
        },
        mounted() {
            this.loadNotifications();
            setInterval(() => {
                this.loadNotifications();   
            }, 10000)
            
            console.log('Component mounted.')
        }
    };
</script>
