$(document).ready(function () {
    $("#searchInput").on("keyup", function () {
      var searchTerm = $(this).val().toLowerCase().trim();
  
      $("#USER-TABLE tbody tr").each(function () {
        var username = $(this).find('td:eq(0) input[name="username"]').val().toLowerCase();
        var email = $(this).find('td:eq(1) input[name="email"]').val().toLowerCase();
        var clearance = $(this).find('td:eq(2) select[name="clearance"]').val().toLowerCase();
        var status = $(this).find('td:eq(3)').text().toLowerCase().trim();
  
        var rowContent = username + " " + email + " " + clearance + " " + status;
  
        if (rowContent.includes(searchTerm)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    });
  });
  