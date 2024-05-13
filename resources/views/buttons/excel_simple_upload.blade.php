@if(isset($simple_route))
    <a href="{{ $simple_route }}" class="btn btn-primary ml-2">
        <i data-feather='download'></i>
        {{ __('XLSX Sample') }}</a>
    <button type="button" class="btn btn-outline-primary ml-2" data-toggle="modal"
        data-target="#inlineForm">
        <i data-feather='upload'></i>
        {{ __('Upload File') }}
    </button>
@else
    pass route
@endif

 <!-- Modal -->
 <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
 aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
         <div class="modal-header">
             <h4 class="modal-title" id="myModalLabel33">Upload Excel File</h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
         </div>
         <form action="{{ isset($upload_route) ? $upload_route : '' }}" method="POST" enctype="multipart/form-data">
             @csrf
             <div class="modal-body">
                 <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                     name="file-input">
             </div>
             <div class="modal-footer">
                 <button type="submit" class="btn btn-primary">UPLOAD</button>
             </div>
         </form>
     </div>
 </div>
</div>