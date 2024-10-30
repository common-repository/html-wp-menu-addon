<div class="formbold-main-wrapper">

  <!-- Author: FormBold Team -->
  <!-- Learn More: https://formbold.com -->
  <div class="formbold-form-wrapper">
    <img src="<?php echo plugins_url('../banner.png', __FILE__ ); ?>" style="width: 100%;">
<div class="pad_all">
    <h2 style="text-align: center;">HTML WP Menu</h2>
    
    <form action="#" method="POST">
      <div class="formbold-mb-5">
        <label for="theme-name" class="formbold-form-label">
          Header Menu ID(ID of header menu block.do not use any space and the div have id should not use any class or any attribute also DO NOT WRITE ANY CSS with this id. example: <?php echo esc_html( htmlspecialchars('<div id="', ENT_QUOTES).'<span class="bold-plug">'.'header-menu'.'</span>'.htmlspecialchars('"><ul>Menu code li</ul></div>', ENT_QUOTES)); ?>):
        </label>
        <input
          type="text"
          name="theme-header-name"
          id="theme-header-name"
          placeholder="Enter Theme Menu ID(Header Menu)"
          class="formbold-form-input" value="<?php if(get_option('theme-header-name')!=''){ echo esc_html(get_option('theme-header-name')); } ?>"
          required
        />
      </div>
      <div class="formbold-mb-5">
        <label for="theme-name" class="formbold-form-label">
           Footer Menu ID(ID of footer menu block.do not use any space and the div have id should not use any class or any attribute also DO NOT WRITE ANY CSS with this id. example: <?php echo esc_html(htmlspecialchars('<div id="', ENT_QUOTES).'<span class="bold-plug">'.'footer-menu'.'</span>'.htmlspecialchars('"><ul>Menu code li</ul></div>', ENT_QUOTES)); ?>):
        </label>
        <input
          type="text"
          name="theme-footer-name"
          id="theme-footer-name"
          placeholder="Enter Theme Menu ID(Footer Menu)" value="<?php if(get_option('theme-footer-name')!=''){ echo esc_html(get_option('theme-footer-name')); } ?>"
          class="formbold-form-input"
          required
        />
      </div>

       <div class="formbold-mb-5">
        <input
          type="checkbox"
          name="menuboots"
          id="menuboots"
          class="formbold-form-input bootmenuchk" <?php if((get_option('theme-menuboots')!='')): ?>checked <?php endif; ?>
        />Do you want to add bootstrap support for menu?
        </div>
      <div>
        <button class="formbold-btn w-full" id="submit">Save</button>
      </div>
    </form>
    <div class="result"></div>
  </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).on('click', '#submit', function(e){
   if (confirm('Are you want to save data')){
    e.preventDefault();
     var fd = new FormData();
    
    var theme_header_name = jQuery('#theme-header-name').val();
    var theme_footer_name = jQuery('#theme-footer-name').val();

   // alert(theme_header_name.indexOf(' '));
if((theme_header_name.indexOf(' ')===-1 && theme_footer_name.indexOf(' ')===-1 )){
  progress();
   
    fd.append("theme-header-name", theme_header_name);
    fd.append("theme-footer-name", theme_footer_name);  
    fd.append('action', 'htmlwpmenu_save');  ;
    fd.append('nonce'  , '<?php echo wp_create_nonce( 'htmlwp_menu_upload_file_action' ); ?>');
     if ( jQuery(document).find("input").hasClass("bootmenuchk") ) {
   // alert('ff'); 
     fd.append('menuboots', '');  
      jQuery(document).find('.bootmenuchk:checked').each(function () {
         //jQuery(this).name;
         fd.append(jQuery(this).attr('name'), 1);  
      });

    }
    
console.log();
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: fd,
        contentType: false,
        processData: false,
        beforeSend: function ( xhr ) {
               //Add your image loader here
               jQuery('.result').html('<div class="d-flex flex-column align-items-center justify-content-center"><div class="row"><div class="spinner-grow" role="status"><span class="visually-hidden">Loading...</span></div></div><div class="row"><strong>Saving Data</strong></div></div>');
            },
        success: function(response){

            //console.log(JSON.parse(response).error);
            if(JSON.parse(response).error)
            {
              jQuery('.result').html('<div class="alert alert-danger d-flex align-items-center" role="alert"><div>'+JSON.parse(response).message+'</div></div>');
            }
            if(JSON.parse(response).success)
            {
              // jQuery('form').trigger("reset");
              jQuery('.result').html('<div class="alert alert-info d-flex align-items-center" role="alert"><div>'+JSON.parse(response).message+'</div></div>');
            }
        }
    });
  }
  else if(theme_header_name.indexOf(' ')!=-1 || theme_footer_name.indexOf(' ')!=-1 ) 
  {
     alert('Do not use space');
  }
  else
  {
    alert('Fill all the data');
  }
}else{
   // alert("Theme Not Created");
} 
   
});

  function progress()
  {
    
  }
</script>