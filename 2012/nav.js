   $(document).ready(function() {
    $('#homelink').click(function() {
     $('#tardis').hide();
     $('#contact').hide();
     $('#me').hide();
     $('#stuff').hide();
     $('#home').fadeIn(800);
    });
   
   $('#bloglink').click(function() {
     $('#tardis').fadeIn(800);
     $('#contact').hide();
     $('#home').hide();
     $('#me').hide();
     $('#stuff').hide();
    }); 

    $('#contactlink').click(function() {
     $('#tardis').hide();
     $('#contact').fadeIn(800);
     $('#home').hide();
     $('#me').hide();
     $('#stuff').hide();
    }); 

    $('#melink').click(function() {
     $('#tardis').hide();
     $('#me').fadeIn(800);
     $('#home').hide();
     $('#contact').hide();
     $('#stuff').hide();
    }); 

      $('#stufflink').click(function() {
     $('#tardis').hide();
     $('#stuff').fadeIn(800);
     $('#home').hide();
     $('#contact').hide();
     $('#me').hide();
    }); 

});
