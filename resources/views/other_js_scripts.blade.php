

<script>
  $('#b1').click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-sm btn-success',
        cancelButton: 'btn btn-sm btn-danger'
      },
      buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'success',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, cancel!',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        swalWithBootstrapButtons.fire(
          'Deleted!',
          'Your file has been deleted.',
          'success'
        )
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          'Cancelled',
          'Your imaginary file is safe :)',
          'error'
        )
      }
    })
  });
</script>


    <script>
        $(document).ready(function (){
            var table = $('#dataTabless').DataTable({
                'responsive': true
            });
    
            // Handle click on "Expand All" button
            $('#btn-show-all-children').on('click', function(){
                // Expand row details
                table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
            });
    
            // Handle click on "Collapse All" button
            $('#btn-hide-all-children').on('click', function(){
                // Collapse row details
                table.rows('.parent').nodes().to$().find('td:first-child').trigger('click');
            });
        });
    </script>


<script>
  
  $('#special').on('click', function () {
    mySelect.find('option:selected').prop('disabled', true);
    mySelect.selectpicker('refresh');
  });
  
  $('#special2').on('click', function () {
    mySelect.find('option:disabled').prop('disabled', false);
    mySelect.selectpicker('refresh');
  });
  
  $('#basic2').selectpicker({
    liveSearch: true,
    maxOptions: 1
  });
</script>



<script>
$(document).ready(function(){
    
  if ($('#accordionSidenavPages a').hasClass('active')){
    $('#accordionSidenavPages a.active').parent().parent().prev('a').removeClass('collapsed');
    $('#accordionSidenavPages a.active').parent().parent().addClass('show');
    // console.log(id);
  } else {
    $('#takeonebar').addClass('slamdown');
  }
});

</script>
<script>
	$( "#sortable, #sortable2" ).sortable();
</script>
    <script>
    $(".alert").delay(3000).fadeOut();
    </script>


    <script type="text/javascript">
    $.datetimepicker.setLocale('en');
    $('.datetimepickerget').datetimepicker({
    lang:'en',
    formatDate:'d.m.Y',
    step:5,
    startDate:  new Date()
     });
    </script>
    <script type="text/javascript">
    $('.datepicker').datepicker({
    lang:'en',
    formatDate:'d.m.Y',
    step:5,
    });
    </script>

    <script type="text/javascript">
     $(document).ready(function(){
 $('#select_all').on('click',function(){
     if(this.checked){
         $('.checkbox').each(function(){
             this.checked = true;
         });
     }else{
          $('.checkbox').each(function(){
             this.checked = false;
         });
     }
 });

 $('.checkbox').on('click',function(){
     if($('.checkbox:checked').length == $('.checkbox').length){
         $('#select_all').prop('checked',true);
     }else{
         $('#select_all').prop('checked',false);
     }
 });
});
</script>
<script>
function isNumberKey(evt){
   <!--Function to accept only numeric values-->
    //var e = evt || window.event;

   var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode != 46 && charCode > 31
   && (charCode < 48 || charCode > 57))

    return false;
  else {
       var itemdecimal = evt.srcElement.value.split('.');
       if (itemdecimal.length > 1 && charCode == 46)
           return false;

       return true;
  }
}

</script>
<script>
    var usersarry=[];
    $("#selectusers").keyup(function(){
      var gettypevalue = $("#selectusers").val();
      if(gettypevalue!=""){
          $.ajax({
             type:'POST',
             url:'<?php echo asset('my-admin/getusers');?>',
             data:'_token=<?php echo csrf_token();?>&gettypevalue='+gettypevalue+'&userspresent='+usersarry,
             success:function(data){
               $('#item_list').removeClass('d-none');
               $('#boxx').removeClass('d-none');
               $("#item_list").html(data);
            }
          });
      }
    });
    function set_item(item){
      usersarry.push(item);
      $("#uservalues").val(usersarry);
      $("#selectusers").val('');
          $('#item_list').addClass('d-none');
      var gettext = $('#userid'+item).html();
      $("#showusers").append('<div class="col-md-6" id="showuserseses">'+gettext+'</div>');
          $('#boxx').addClass('d-none');
        }
        function deletediv(e,item){
            usersarry.splice( usersarry.indexOf(item), 1 );
            $("#uservalues").val(usersarry);
            // e.parentNode.parentNode.remove(e.parentNode);
            $('#showuserseses').remove();
        }
        $(function(){
          $(document).click(function(){
              $('#box').addClass('d-none'); //hide the button
              //$('#boxx').hide(); //hide the button
            });
        });
  </script>
    <script>
    function divshowhide(value){
      if(value=='specific'){
        $("#specificdiv").removeClass('d-none');
        $("#specificdiv1").show();
        $("#specificdiv2").show();
      }else{
        $("#specificdiv").addClass('d-none');
        $("#specificdiv1").hide();
        $("#specificdiv2").hide();
      }
    }
     function deletediv1(e,item){
        $('#dsafd'+item).remove();
    }

  </script>


    <script>
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + input.id + '-imagePreview').css('background-image', 'url('+e.target.result +')');
                $('#' + input.id + '-imagePreview').hide();
                $('#' + input.id + '-imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".imageUpload").change(function() {
        readURL(this);
    });
    </script>






<script>
    // This is only for the Inputs to work dynamically

function setTheme(H, inputType) {
  // Convert hex to RGB first
  let r = 0, g = 0, b = 0;
  if (H.length == 4) {
    r = "0x" + H[1] + H[1];
    g = "0x" + H[2] + H[2];
    b = "0x" + H[3] + H[3];
  } else if (H.length == 7) {
    r = "0x" + H[1] + H[2];
    g = "0x" + H[3] + H[4];
    b = "0x" + H[5] + H[6];
  }
  // Then to HSL
  r /= 255;
  g /= 255;
  b /= 255;
  let cmin = Math.min(r,g,b),
      cmax = Math.max(r,g,b),
      delta = cmax - cmin,
      h = 0,
      s = 0,
      l = 0;

  if (delta == 0)
    h = 0;
  else if (cmax == r)
    h = ((g - b) / delta) % 6;
  else if (cmax == g)
    h = (b - r) / delta + 2;
  else
    h = (r - g) / delta + 4;

  h = Math.round(h * 60);

  if (h < 0)
    h += 360;

  l = (cmax + cmin) / 2;
  s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
  s = +(s * 100).toFixed(1);
  l = +(l * 100).toFixed(1);

  document.documentElement.style.setProperty(`--${inputType}-color-h`, h);
  document.documentElement.style.setProperty(`--${inputType}-color-s`, s + '%');
  document.documentElement.style.setProperty(`--${inputType}-color-l`, l + '%');

  hsl = h + ' ' + s + ' ' + l + '';

//   $('#'+inputType+'-color-input').val( hsl );

  $('input[name="input[' + inputType + '_hsl]"').val( hsl );
}

const inputs = ['primary', 'secondary', 'info', 'success', 'danger', 'warning', 'muted', 'dark', 'light', 'black', 'white', 'blue','indigo', 'purple', 'pink', 'red', 'orange','yellow', 'green', 'teal', 'cyan', 'gray', 'graydark'];

inputs.forEach((inputType) => {
  document.querySelector(`#${inputType}-color-input`)
    .addEventListener('change', (e) => {
    setTheme(e.target.value, inputType);

  });

});

</script>


<script>
  /*jslint browser:true*/
  /*global jQuery, document*/

  jQuery(document).ready(function () {
      'use strict';

      jQuery('#example-datetime-local-input, #example-datetime-local-input2, #start_date, #end_date, #dob-date, #expire_date').datetimepicker();
  });
</script>

<script>
  function copyToClipboard(element,element1) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val($(element).text()).select();
      document.execCommand("copy");
      $temp.remove();

      $(element1).addClass("copied_success animated bounceIn");
      setTimeout(RemoveClass, 1000);
  function RemoveClass() {
      $(element1).removeClass("copied_success animated bounceIn");
  }
  }
</script>

<script>
function delete_sweet_alert(url, msg) {
  // sweet alert
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-sm btn-success ml-2',
        cancelButton: 'btn btn-sm btn-danger'
    },
    buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
    title: msg,
    text: "You won't be able to revert this!",
    icon: 'success',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    reverseButtons: true
    }).then((result) => {
    if (result.isConfirmed) {

      swalWithBootstrapButtons.fire(
                '',
                'Successfully Done',
                'success'
                );

      window.location.href = url;

        
    } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
    ) {
        swalWithBootstrapButtons.fire(
        'Cancelled',
        'Cancelled successfully :)',
        'error'
        );
        return false;
    }
  })
}
</script>