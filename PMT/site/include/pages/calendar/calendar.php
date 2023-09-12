<?php





?>



<div id="calendar"></div>




<script>
$(document).ready(function(){
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    locale: 'fr',
    editable: true,
    nowIndicator: true,
    businessHours: {
			dow: [ 1, 2, 3, 4, 5 ],

			start: '8:00',
			end: '18:00',
		},
    height: 700,
    selectable: true,
    // selectHelper: true,
    selectMirror: true,
    unselectAuto: true,
    selectOverlap: true,
    selectMinDistance: 30,
    select: function(info) {
      info.allDay = true
    },
		// select: function(start, end, jsEvent, view) {
		// 	// $('#ModalAdd #add-start').val(moment(start).format('DD/MM/YYYY'));
		// 	// $('#ModalAdd #add-end').val(moment(end).format('DD/MM/YYYY'));
		// 	// $('#ModalAdd #add-start-time').val(moment(start).subtract(2, 'hours').format('HH:mm'));
		// 	// $('#ModalAdd #add-end-time').val(moment(end).subtract(2, 'hours').format('HH:mm'));
    //
		// 	var allDay = !start.hasTime() && !end.hasTime();
    //
		// 	if(allDay) {
		// 		$("#add-allday").prop("checked", true);
		// 		$("#add-start-time").parent().hide();
		// 		$("#add-end-time").parent().hide();
		// 	} else {
		// 		$("#add-allday").prop("checked", false);
		// 		$("#add-start-time").parent().show();
		// 		$("#add-end-time").parent().show();
		// 	}
		// 	// $('#ModalAdd').modal('show');
		// },
    // Quand on clique sur un évènement
    eventClick: function(info) {
      // alert('Event: ' + info.event.title);
      // alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
      // alert('View: ' + info.view.type);

      // change the border color just for fun
      info.el.style.borderColor = 'red';
    },
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,list'
    },
    events: {
      url: 'include/ajax/calendar/getEvents.php',
      method: 'POST',
      extraParams: {
      custom_param1: 'something',
      custom_param2: 'somethingelse'
      },
      failure: function() {
        alert('there was an error while fetching events!');
      },
      color: 'yellow',   // a non-ajax option
      textColor: 'black' // a non-ajax option
    },



    eventDrop: function(event, delta, revertFunc) { // si changement de position
			edit(event);
		},
		eventResize: function(event,dayDelta,minuteDelta,revertFunc) { // si changement de longueur
			edit(event);
		}


  });
  calendar.render();
});



function edit(event){
		start = event.start.format('YYYY-MM-DD HH:mm:ss');
		if(event.end){
			end = event.end.format('YYYY-MM-DD HH:mm:ss');
		}else{
			end = start;
		}

		id =  event.id;

		Event = [];
		Event[0] = id;
		Event[1] = start;
		Event[2] = end;
		Event[3] = event.allDay;
alert('coucou')
		// $.ajax({
		// 	url: 'ajax/agenda/editEventDate.php',
		// 	type: "POST",
		// 	data: {Event:Event},
		// 	success: function(rep) {
		// 		if(rep == 'OK'){
		// 			// alert('Saved');
		// 		}else{
		// 			// alert('Could not be saved. try again.');
		// 		}
		// 	}
		// });
	}

</script>


<script type="text/javascript" src="js/fullcalendar/main.min.js"></script>
<script type="text/javascript" src="js/fullcalendar/locales-all.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/fullcalendar/main.min.css" />
