$(document).ready(function (e) {
      
    $('#image').change(function() {
    let reader = new FileReader();
          reader.onload = (e) => { 
            $('#image_preview_container').attr('src', e.target.result); 
          }
          reader.readAsDataURL(this.files[0]); 

      });
    
  });