function displayResponse(domElem, values, mDivs,  mValues) {

    for (let index = 0; index < domElem.length; index++) {
        domElem[index].html(values[index] + " Mo")
    }

    for (let index = 0; index < mDivs.length; index++) {
        mDivName = mDivs[index].getAttribute("name");
        mDivs[index].title = mValues[mDivName] + " Mo";
    }

    $(".cache-lastCalculated-value").html(no_time_elapsed);
}

$(document).ready(function () {
    $(".refresh-cache-size").on("click", function test () {
        $(this).children("span").addClass("spin6").removeClass("icon-arrows-cw")

        return new Promise((res, rej) => {
            jQuery.ajax({
                url: "ws.php?format=json&method=pwg.getCacheSize",
                type: "POST",
                data: {
                    param : "test_param",
                    service : "test_service"
                },
                success: function (raw_data) {
                    data = jQuery.parseJSON(raw_data);
                    if (data.stat === "ok") {
                        res();

                        var domElemToRefresh = [$(".cache-size-value"), $(".multiple-pictures-sizes"), $(".multiple-compiledTemplate-sizes")];
                        var domElemValues = [data.result.infos[0].value, data.result.infos[1].value.all, data.result.infos[2].value];
                        for (let i = 0; i < domElemValues.length; i++) {
                          domElemValues[i] = (domElemValues[i]/1024/1024).toFixed(2);
                        }

                        var multipleSizes = $(".delete-check-container").children(".delete-size-check");
                        var multipleSizesValues = data.result.infos[1].value
                        for (const [key, value] of Object.entries(multipleSizesValues)) {
                            multipleSizesValues[key] = (multipleSizesValues[key]/1024/1024).toFixed(2);
                        }

                        displayResponse(domElemToRefresh , domElemValues, multipleSizes,  multipleSizesValues);

                        $(this).children("span").addClass("icon-arrows-cw").removeClass("spin6");

                    } else {
                        rej(data);
                    }
                },
                error: function(message) {
                    rej(message);
                    console.log(message);
                }
            });
        })
    })


    
})