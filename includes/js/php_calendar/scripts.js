function viewcalendar() {
  calendario = window.open("/SIEAD/includes/js/php_calendar/calendar.php", "calendario" , "location=0, menubar=0, scrollbars=0, status=0, titlebar=0, toolbar=0, directories=0, resizable=0, width=200, height=240, top=50, left=250");
  calendario.resizeTo(200, 240);
  calendario.moveTo(250, 50);
}
function insertdate(d) {
  window.close();
  window.opener.document.getElementById('date').value = d;
}
