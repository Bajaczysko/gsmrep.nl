$(document).ready(function() { 
  "use strict";
  var options = {};
  options.ui = {
      container: "#pwd-container",
      showVerdictsInsideProgressBar: true,
      viewports: {
          progress: ".pwstrength_viewport_progress"
      }
  };  
  $('#password').pwstrength(options);

  // switch login-registration page
  $('#registration-link').on('click', function(e) {
    e.preventDefault();   
    $('#login-box').hide(); 
    $('#registration-box').show();
  });
    $('#login-link').on('click', function(e) {    
    e.preventDefault();
    $('#registration-box').hide();
    $('#login-box').show();         
  });

  // Media manager settings
  $('#profileLink').click(function() {
    $('#profileModal').modal('show');
  });

  $('#changePwdLink').click(function() {
    $('#changePwdModal').modal('show');
  }); 

  // Off-Canvas code
  $('body').addClass('js');
  var menulink = $('#showTreeView'),
      wrap = $('#wrap');
  menulink.click(function() {
    menulink.toggleClass('active');
    wrap.toggleClass('active');
    return false;
  }); 

  // show tooltip over form labels 
  $('[data-toggle="tooltip"]').tooltip();  

  // Safari Browser check for muliple file upload issue
  var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
  if (isSafari) {
      $('#upload-file').removeAttr('multiple')
  }
});

function recaptchaCallback() {
    $('#submitBtn').removeAttr('disabled');
};