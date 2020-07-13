<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>Contacts</h2>
        </div>
        <div class="row">
            <div class="col-md-4" v-for="user in users">
                <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                  <h5>{{user.name}}</h5>
                </div>
                <div class="card-body pt-1">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b></b></h2>
                      <p class="text-muted text-sm"><b>Email: </b><br>{{user.email}}</p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: Demo Street 123, Demo City 04312, NJ</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: + 800 - 12 12 23 52</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="/img/person.png" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
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
            users:{},
          }
        },
        methods:{
            loadUser(){
                axios.get('api/user?a=1').then(({data})=>(this.users = data))
            }
        },
        mounted() {
            this.loadUser();
        }
    };
</script>
