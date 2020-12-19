import $ from 'jquery';
import moment from 'moment';

$(".countdown[data-date]").each(function(){
  let $this = $(this);
  var countDownDate = moment($this.data("date"), 'YYYY-MM-DD HH:mm').toDate();

  if(!isNaN(countDownDate)) {
    // Update the count down every 1 second
    var x = setInterval(function() {

      // Get today's date and time
      var now = new Date().getTime();

      // Find the distance between now and the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      $this.html("<div class='digit'>" + days + "<span class='letter'>d</span></div><div class='digit'>" + hours + "<span class='letter'>h</span></div><div class='digit'>"
        + minutes + "<span class='letter'>m</span></div><div class='digit'>" + seconds + "<span class='letter'>s</span></div>");

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        $this.html("<div class='released'>RELEASED</div>");
      }
    }, 1000);
  }
});
