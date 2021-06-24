function displayResponse(domElem, values, mDivs,  mValues, lastTimeCalc) {

    for (let index = 0; index < domElem.length; index++) {
        domElem[index].html(values[index])
    }

    for (let index = 0; index < mDivs.length; index++) {
        mDivs[index].title = mValues[index] + "Mo";
    }

    $(".cache-lastCalculated-value").html(lastTimeCalc)
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

                        console.log(data);

                        console.log(data.result.infos[1].value);

                        var domElemToRefresh = [$(".cache-size-value"), $(".multiple-pictures-sizes"), $(".multiple-compiledTemplate-sizes")];
                        var domElemValues = [data.result.infos[0].value, 69, 42];

                        var multipleSizes = $(".delete-check-container").children(".delete-size-check");
                        var multipleSizesValues = data.result.infos[1].value;

                        displayResponse(domElemToRefresh , domElemValues, multipleSizes,  multipleSizesValues, data.result.infos[2].value);

                        $(".refresh-icon").addClass("icon-arrows-cw").removeClass("spin6");

                    } else {
                        rej(raw_data);
                    }
                },
                error: function(message) {
                    rej(message);
                }
            });
        })
    })


    
})