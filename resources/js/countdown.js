document.querySelectorAll(".countdown[data-date]").forEach(function(el) {
  const dateStr = el.dataset.date;
  // Parse YYYY-MM-DD HH:mm format
  const countDownDate = new Date(dateStr.replace(' ', 'T'));

  if(!isNaN(countDownDate)) {
    // Update the count down every 1 second
    const interval = setInterval(function() {
      // Get today's date and time
      const now = new Date().getTime();

      // Find the distance between now and the count down date
      const distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      el.innerHTML = "<div class='digit'>" + days + "<span class='letter'>d</span></div><div class='digit'>" + hours + "<span class='letter'>h</span></div><div class='digit'>"
        + minutes + "<span class='letter'>m</span></div><div class='digit'>" + seconds + "<span class='letter'>s</span></div>";

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(interval);
        el.innerHTML = "<div class='released'>RELEASED</div>";
      }
    }, 1000);
  }
});
