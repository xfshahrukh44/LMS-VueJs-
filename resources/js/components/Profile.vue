<template>
    <div class="container">
        <div class="row mt-3 ml-1">
            <h2>User Profile</h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header light-blue">
                <h3 class="widget-user-username text-right white" id="userName"></h3>
                <h5 class="widget-user-desc text-right white">Web Designer</h5>
              </div>
              <div class="widget-user-image">
                <img class="img-circle" src="/img/person.png" alt="User Avatar">
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">3,200</h5>
                      <span class="description-text">SALES</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">13,000</h5>
                      <span class="description-text">FOLLOWERS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header">35</h5>
                      <span class="description-text">PRODUCTS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        methods:{
          loadUserName(){
            axios.get('api/get_user_name')
              .then(response => {
                $('#userName').text(response.data.name)
            });
          }
        },
        mounted() {
            this.loadUserName();
        }
    };
</script>
