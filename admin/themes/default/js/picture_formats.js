function fitExtensions() {
    $(".format-card-ext span").each((i, node) => {
        let size = Math.min(180 * 1/node.innerHTML.length, 45) 
        node.setAttribute('style', `font-size:${size}px`)
    })
}

fitExtensions()

$('.format-card').each((i, node) => {
    let card = $(node)
    let button = card.find(".format-delete")
    button.click(() => {
        console.log(card.data('id'));
        button.find('i').attr("class", "icon-spin6 animate-spin")
        $.ajax({
            url: "ws.php?format=json&method=pwg.images.formats.delete",
            type: "POST",
            data: {
                pwg_token : pwg_token,
                format_id: card.data('id'),
            },
            success: function (raw_data) {
                card.fadeOut("slow", () => {
                    card.remove();
                    if ($('.format-card').length == 0)
                        $('.no-formats').show()
                })
            },
            error: function(message) {
              console.log(message);
            }
        })
    })
})