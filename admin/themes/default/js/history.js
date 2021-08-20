$(document).ready(() => {
  
  $(".filter").submit(function (e) {
    e.preventDefault();

    var dataArray = $(this).serializeArray()
    console.log(dataArray);
    dataObj = {};

    dataObj["types"] = [];
    $(dataArray).each(function(i, field){
      if (field.name == "types[]") {
        dataObj["types"].push(field.value);
      } else {
        dataObj[field.name] = field.value;
      }
    });

    $.ajax({
      url: API_METHOD,
      method: "POST",
      data: {
        start: dataObj['start'],
        end: dataObj['end'],
        types: dataObj['types'],
        user: dataObj['user'],
        image_id: dataObj['image_id'],
        filename: dataObj['filename'],
        ip: dataObj['ip'],
        display_thumbnail: dataObj['display_thumbnail'],
      },
      success: function (data) {
        console.log(data);
      },
      error: function (e) {
        console.log("Something went wrong: " + e);
      }
    })

    console.log(dataObj);
  })
})