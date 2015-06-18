//Global Vars
var timelines = [];
var timeline_success = false;

 $(document).ready(function(){

  $('#userGrid').disableSelection();

  $('body').append('<div id="debug-window" class="alert alert-warning" role="alert"><b>[ DEBUGINFO ]</b><br><span></span></div>');
  $('body').append('<div id="error-window" class="alert alert-danger" role="alert"><b>[ ERRORINFO ]</b><br><span></span></div>');
  $('#debug-window, #error-window').hide();


  //
  // SORTABLE GRID
  //

    $("#userGrid").sortable({
      tolerance: 'pointer',
      handle: '.userMove',
      forceHelperSize: true,
        start: function( event, ui ) {
          ui.placeholder.children('.placeholder').width(ui.item.width());
        }
      });

  $('.hx_sidebar li').each(function(index, el) {

    $(this).css("top", (index * 35) + 50);
    $(this).attr('top',$(this).css("top"));
    $(this).css("right", -$(this).children('div').width() - 25);
    $(this).children('.sidebar_label').hide();
  });


  //$('.hx_sidebar ul').append('<li class="sidebar_container"><div class="sidebar_label">das</div></li>');
  $('.hx_sidebar .sidebar_container').css("top", 35).hide();


  $('.hx_sidebar .sidebar_item').click(function(event) {

    scroll_speed = 400;
    elem = $(this);

    if ($(this).css('right') != '0px') {

      $('.hx_sidebar .sidebar_item').not($(this)).fadeOut(250);
      $(this).animate({top: 0}, scroll_speed);
      $('.hx_sidebar .sidebar_container').fadeIn();
      $(this).animate({right: 0}, scroll_speed);
      $('.hx_sidebar .sidebar_container').animate({right: 0}, scroll_speed);

    } else {

        $(this).animate({right: -$(this).children('div').width() - 25}, scroll_speed);
        $('.hx_sidebar .sidebar_container').animate({right: -$(this).children('div').width() - 25}, scroll_speed);
        $('.hx_sidebar .sidebar_container').fadeOut(200);
        $(this).animate({top: $(this).attr('top')}, scroll_speed);
        $(this).children('.sidebar_label').hide();
        $('.hx_sidebar .sidebar_item').not($(this)).not('.sidebar_container').fadeIn(250);


    }


  });



  // $('h1, h2, h3, h4').each(function(){
  //   $(this).html('<span class="hx_corners">'+$(this).html()+'</span>');
  // });

  //This fill the slide1 html

  $.ajax({
    url: "ajax/getUserList.php",
    type: "GET",
    success: function(data) {
      try {

        var myUserData = jQuery.parseJSON(data); //contains all userdata

        jQuery.each(myUserData, function(index,user){

          console.log('ID: ' + user.uid);
          console.log('Name: ' + "masked user");
          console.log('====================================================');

          var loadingtext = "Click to Load Data";

          // var html = "";
          // html += '<div class="col-md-12 userChart" id="userChart_'+user.uid+'">';
          // html += ' <div class="userName">'+user.name+'<span class="pull-right userMove"><i class="fa fa-bars"></i></span></div>';
          // html += ' <div class="VISContainer" id="VISContainer'+user.uid+'" uid="'+user.uid+'">';
          // html += '   <div class="chartPlaceholder">';
          // html += '    <button type="button"  id="loaddata_btn_'+user.uid+'" uid="'+user.uid+'" class="btn btn-block btn-primary center-block loaddata">' + loadingtext + '</button>';
          // html += '   </div>';
          // html += ' </div>';
          // html += '</div>';

          var html = "";
          html += '<div class="col-md-12 userChart" id="userChart_'+user.uid+'">';
          html += ' <div class="userName">'+user.name+'<span class="pull-right userMove"><i class="fa fa-bars"></i></span></div>';
          html += ' <div class="VISContainer" id="VISContainer'+user.uid+'" uid="'+user.uid+'">';
          html += '   <div class="chartPlaceholder">';
          html += '    <button type="button"  id="loaddata_btn_'+user.uid+'" uid="'+user.uid+'" class="btn btn-block btn-primary center-block loaddata">' + loadingtext + '</button>';
          html += '   </div>';
          html += ' </div>';
          html += '</div>';

          $('#userGrid').append(html);

          $('#loaddata_btn_' + $(this).attr('uid')).click(function(event) {
            //$(this).fadeOut();

            var myID = $(this).attr('uid');

            $.ajax({
              url: "ajax/getUserData.php",
              type: "GET",
              data: {
                uid: myID
              },
              success: function(data) {

                var items = new vis.DataSet();
                var myFriendData = $.parseJSON(data);

                var groups = new vis.DataSet([
                  {id: 1, content: '<i class="fa fa-globe center-block"></i>'}, // web
                  {id: 2, content: '<i class="fa fa-mobile center-block" ></i>'}, //app
                  {id: 3, content: '<i class="fa fa-comments-o center-block"></i>'}, //msg
                  {id: 4, content: '<i class="fa fa-cubes center-block"></i>'} // other
                ]);

                var myParsedData = [];
                var boolFirstRun = true;
                var start = null;
                var ende = null;

                $.each(myFriendData, function(index, value) {

                  var tempDateArr = value.start.split('-');
                  // console.log('tempDateArr: ' + tempDateArr );
                  var tempStartTimeArr = value.start_time.split(':');
                  // console.log('tempStartTimeArr: ' + tempStartTimeArr );
                  var tempDateEndArr = value.end.split('-');
                  // console.log('tempDateEndArr: ' + tempDateEndArr );
                  var tempEndTimeArr = value.end_time.split(':');
                  // console.log('tempEndTimeArr: ' + tempEndTimeArr );

                  var myStartDate = new Date(tempDateArr[0], tempDateArr[1]-1, tempDateArr[2],tempStartTimeArr[0], tempStartTimeArr[1], tempStartTimeArr[2]);
                  var myEndDate = new Date(tempDateEndArr[0],tempDateEndArr[1]-1,tempDateEndArr[2], tempEndTimeArr[0],tempEndTimeArr[1],tempEndTimeArr[2]);
                  // console.log('myStartDate: ' + myStartDate );
                  // console.log('myEndDate: ' + myEndDate );


                  if(boolFirstRun){
                    boolFirstRun = !boolFirstRun;
                    start = myStartDate;
                  }

                  console.log('------------------------------------------------------');
                  console.log('id: ' + index );
                  console.log('group: ' + (value.sid - 1) );
                  console.log('start: ' + myStartDate );
                  console.log('end: ' + myEndDate );

                  items.add({
                    id: index,
                    group: (value.sid - 1),
                    start: myStartDate,
                    end: myEndDate
                  });

                  ende = myEndDate;

                });

                timelines.push( new vis.Timeline(document.getElementById('VISContainer'+myID),null, {

                  editable: false,
                  start: start,
                  end: ende,
                  // minHeight: 208,
                  // maxHeight: 300,
                  // width: "100%",
                  stack: false,
                  orientation: 'bottom'

                }));

                // $('#userChart_'+myID).removeClass('col-md-6');
                // $('#userChart_'+myID).addClass('col-md-12');

                  timelines[timelines.length-1].setGroups(groups);
                  timelines[timelines.length-1].setItems(items);
                $('#VISContainer'+myID).animate({height: 'auto',}, 500, function() {
                });

                $('#VISContainer'+myID+' .chartPlaceholder').fadeOut();

                timeline_success = !timeline_success;

              },
              error: function(data) {
                alert("ERROR! "+data);
              }

            });

          });

        });

      }
      catch (e) {

        alert(e);

      }

    }

  });

  $(".item").on('dblclick', function (e) {

    // DEBUG("OK");

    //var sel = timeline.getSelection();

    $('#dialog').dialog({
      title: 'some title',
      autoOpen: true,
      modal: true,
      resizable: true,
      closeOnEscapeType: true,
      width: 500,
      minHeight: 300,
      position: ['center', 0]
    });

  });

}); // jQuery END

function searchFunc(val) {

  $('#userGrid > .userChart').each(function() {

    var username = $($(this).children('.userName')).html();

    if( username.toLowerCase().indexOf(val.toLowerCase()) >= 0) {
      $(this).show();
    }
    else{
      $(this).hide();
    }

  });

}

function DEBUG(string){
  debug_case = $('#debug-window');
  debug_case.children('span').html('<pre>'+string+'</pre>');
  debug_case.css('top',-(debug_case.height() + 60)).show();
  debug_case.animate({top: 60});
  debug_case.click(function(event) {
    $(this).animate({
      top: -($(this).height() + 60),
    });
  });
}

function ERROR(string){
  error_case = $('#error-window');
  error_case.children('span').html('<pre>'+string+'</pre>');
  error_case.css('top',-(error_case.height() + 60)).show();
  error_case.animate({top: 60});
  error_case.click(function(event) {
    $(this).animate({
      top: -($(this).height() + 60),
    });
  });
}
