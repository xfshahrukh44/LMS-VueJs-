<template>
    <div class="container">
        <div class="modal fade" id="assignmentModalDetail" tabindex="-1" role="dialog" aria-labelledby="assignmentModalDetailLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalDetailLabel">Assignment Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <h3 class="profile-username text-center" id="title"></h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Description</b> <a class="float-right" id="description"></a>
                  </li>
                  <li class="list-group-item">
                    <b>Marks</b> <a class="float-right" id="marks"></a>
                  </li>
                  <li class="list-group-item">
                    <b>Download File</b> 
                    <span class="material-icons float-right" @click="downloadFile">
                        get_app
                    </span>
                  </li>
                </ul>

              <!-- /.card-body -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        methods:
        {
            downloadFile()
            {
                var id = this.$parent.asgn_id;
                axios.get('api/dloadasgn?asgn_id='+id,
                    {
                        responseType:'arraybuffer',
                    })
                    .then(({data})=>{
                        let blob = new Blob([data], { type: this.$parent.assign_type });
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = this.$parent.assign_name.substr(8);
                        link.click();
                    });
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    };
</script>
