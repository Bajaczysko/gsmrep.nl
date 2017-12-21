/*global $*/
var msnry;

$(document).ready(function() {
  if($('#media-manager').length) {
    setViewHeight();

    $( window ).resize(function(){
      setViewHeight();
    });

    // Get column width for masonry layout
    // Column width changes for mobile layout
    var cw = 200;
    var wd = $(window).width();
    if((wd > 0) && (wd <= 767)) {
        cw = 120;
    }

    // Initiating masonry layout
    msnry = new Masonry('#masonry-container', {
      columnWidth: cw,
      itemSelector: '.item',
      gutter: 10
    });

    // Set default view if cookie exists      
    var view  = ($.cookie('view')) ? '#details' : '#thumbs';      
    $(view).addClass('active');
    $(view + 'View').removeClass('hidden');
    if(view == '#thumbs'){
      $('#toolbar-select').removeClass('hidden');
      msnry.layout();
    }  

    var groups = {};
    $('.galleryItem').each(function() {
      var id = parseInt($(this).attr('data-group'), 10);      
      if(!groups[id]) {
        groups[id] = [];
      }       
      groups[id].push(this);
    });    

    $.each(groups, function() {      
      $(this).magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        gallery: { enabled:true }
      })      
    });

    // Load media of selected folder
    $('a.mediapath').click(function(e) {        
      e.preventDefault();    
      $('#path').val($(this).attr('href'));
      $('#mediaForm').submit();
    });

    // Enable select button, disable media links
    $('#toolbar-select button').click(function(e) {                
      $(this).toggleClass('active');
      $('.cover').toggleClass('media-disabled');
      $('.media-item').removeClass('media-selected');
      $('#thumbsView input').prop('checked',false);
    }); 

    // Select media items
    $('.media-item').click(function() {  
      if($('#toolbar-select button').hasClass('active')){
        $(this).toggleClass('media-selected');            
        $(this).find('input').prop('checked',function(i,val) {
          return !val;
        });
      }    
    });

    // Delete file or folder
    $('a.delete-item').click(function(e) {
      e.preventDefault();    
      var dom = this;
      var media = $(this).data('media');
      var msg;
      if (media == 'folder') {
        msg = 'This action will delete the selected folder and all its contents.'
      } else if (media == 'file') {
        msg = 'This action will delete the selected file.'
      }
      bootbox.confirm(msg, function(r) {
        if (r === true) {
          // ajax request to remove file/folder
          $.post(site_url + '/remove_media', {
            'rm[]': [$(dom).attr('href')]
          }).done(function() {                                                          
            window.location.assign(site_url);
          })
        }
      })
    });

    $('a.rename-item').click(function(e){
      e.preventDefault();
      var media = $(this).data('media');      
      var raw_name = $(this).data('raw-name');
      var path = $(this).attr('href');
      bootbox.prompt({
        title: 'Rename '+media,
        value: raw_name,
        callback: function(name) {
          if ((name !== null) && (name != raw_name)) {
            $.post(site_url + '/rename_media', {
              'path': path,
              'edited_name': name
            }).done(function(response) {                                                   
              if(response == '1'){
                window.location.assign(site_url);
              } else {
                var r = $.parseJSON(response);
                bootbox.alert('<br><div class="alert alert-'+r.type+'" role="alert">'+r.msg+'</div>');
              }
            });
          }
        }
      });
    });

    // Drag and drop support for files upload
    Dropzone.options.uploadForm = {
      paramName: 'filedata', // The name that will be used to transfer the file
      uploadMultiple: true,
      maxFilesize: max_size, //MB 
      maxFiles: max_files,   
      parallelUploads: 1,
      addRemoveLinks: true,
      autoProcessQueue: false,
      init: function() {          
        dz = this;          
        var submitButton = $('.upload-btn');

        // On adding file
        dz.on('addedfile', function(file) {            
          submitButton.css('display','block');            
        });

        // On removing file
        dz.on('removedfile', function(file) {            
          if(!dz.getQueuedFiles().length){
            submitButton.css('display','none');
          }
        });  

        // On clicking submit button start upload process
        submitButton.click(function(){
          dz.processQueue();
        }); 

        // process files queue if left to upload
        dz.on('success', function(file) {              
          if(dz.getQueuedFiles().length) {              
            dz.processQueue();
          }
        });

        // Send file starts
        dz.on('sending', function(file, xhr, formData) {             
          formData.append('count',1); // set to create  
          formData.append('client',JSON.stringify(client));                          
          $('.meter').show();
        });

        // File upload Progress
        dz.on('totaluploadprogress', function(progress) {            
          $('.roller').width(progress + '%');
        });

        dz.on('queuecomplete', function(progress) {
          $('.meter').delay(999).slideUp(999);
          submitButton.css('display','none');
          window.location.assign(site_url);
        });            
      }
    }; 
  }	  
});

// Delete multiple files or folder
function delete_media() {  
  var checked = $('#mediaForm input:checkbox').is(':checked');
  if (checked === true) {
    bootbox.confirm('This action will delete the selected media.', function(r) {
      if (r === true) {
        $.post(site_url + '/remove_media', $('#mediaForm').serialize()).done(function(r) {          
          window.location.assign(site_url);                    
        });
      }
    });
  } else {
    bootbox.alert('Select atleast one media or folder.');
  }
}

// Switch between thumbs and details view
function setViewType(view) {    
  var id = $('.mode a.active').attr('id');            
  if (id != view) {
    // Set cookie for details view
    if (view == 'details') {
      if (!$.cookie('view')) {
        $.cookie('view', 1, {
          expires: 7, // cookie expiration days
          path: '/'
        });
      }            
    } else {
      $.removeCookie('view', { path: '/' });            
    }
    $('.mode a').toggleClass('active');
    $('.mode-view').toggleClass('hidden');
    $('#toolbar-select').toggleClass('hidden');
    if(view == 'thumbs'){
      msnry.layout();
    }       
  }
}

function setViewHeight() {
  var ht = $(window).height() - 177;    
  $('.mode-view').css('height',ht);
}