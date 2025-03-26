$(document).ready(function(){
    $("#filterButton").click(function(e){
      var searchTerm = $("#searchInput").val().toLowerCase().trim();
      
      $("#TASK-TABLE tbody tr").each(function(){
        var rowText = $(this).text().toLowerCase();
        
        if (rowText.indexOf(searchTerm) > -1) {
           $(this).show();
        } else {
           $(this).hide();
        }
      });
    });
  });