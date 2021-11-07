function setDisplayCompact() {

    removeIconDesc();

    $(".albumActions").css("display", "flex");
    removeHoverEffect($(".categoryBox"));
    removeHoverEffect($(".categoryBox").children(".albumActions").children("a"));

    $(".categoryBox").children(".albumActions").children("a").hover(function () {
        $(this).css({
            color : "#000000",
        });
    }, function () {
        $(this).css({
            color : "#848484",
        });
    });
    $(".categoryBox").removeClass("line_cat").removeClass("tile_cat");
    $(".addAlbum").removeClass("tile_add");
    $(".categoryBox").css({
        minWidth: "250px",
        maxWidth: "350px",
        flexDirection: "column",
        maxHeight: "180px",
        alignItems: "unset",
        margin: "15px"
    });

    $(".albumInfos").css({
        marginLeft: "0",
        flexDirection: "column"
    });

    // $(".albumIcon").css({
    //     height: "80px"
    // });

    // $(".albumIcon span").css({
    //     fontSize: "19px",
    //     width: "27px",
    //     padding: "10px"
    // });

    $(".albumIcon").css({
        height: "60px"
    });

    $(".albumIcon span").css({
        fontSize: "14px",
        width: "20px",
        padding: "8px"
    });

    $(".albumInfos p").css({
        margin: "0",
        textAlign: "center",
        whiteSpace: "normal"
    });
    $(".albumInfos p:last-child").css({
        width: "auto"
    });

    $(".albumTop").css({
        width: "auto",
        justifyContent: "center",
        flexDirection: "row",
        alignItems: "baseline",
        height: "65px"
    });

    $(".albumTitle").css("padding", "0 15px");

    $(".addAlbum").css({
        minWidth : "250px",
        maxWidth: "350px",
        flexDirection: "column",
        maxHeight: "180px",
        margin: "15px"
    });

    $(".addAlbum form label").css({
        display: "none"
    });

     $(".addAlbumHead").css({
         flexDirection: "column",
         transform: "translateY(55px)",
         alignItems: "center",
         marginTop: "-10px",
         transition: "0.4s ease",
         marginBottom: "0px"
    });

    $(".addAlbum form").css("flex-direction", "column");

    $(".addAlbum form").css({
        flexDirection: "column",
        marginTop: "0",
        marginBottom: "0",
        transitionDelay: "0s"
    });

    $(".addAlbum.input-mode form").css({
        transitionDelay: "0.4s",
    });

    $(".addAlbum form input").css("margin", "0px 10px 0px 10px");
    $(".addAlbum form button").css("margin", "10px auto 0 auto");
    $(".addAlbum p").css("margin-bottom", "0px");

    $(".addAlbumHead p").css("margin-left", "0");

    $(".addAlbumHead span").css({
        fontSize:"14px",
        width: "20px",
        height: "20px",
        padding: "8px"
    });

    $(".albumActions").css({
        flexDirection : "row",
        marginTop: "auto",
        width: "100%"
    });

    $(".albumActions a").css({
      minWidth : "0px"
    });

    $(".albumActions a:first-child").css("margin-left", "35px");
    $(".albumActions a:last-child").css("margin-right", "35px");
}

function setDisplayLine() {

    /*********** Hover stuff ***********/

    removeIconDesc();
    $(".albumActions").css("display", "flex");
    removeHoverEffect($(".categoryBox"));

    $(".categoryBox").hover(function () {
        $(this).css("background", "#ffd7ad");
        $(this).children(".albumInfos").css({
            color: "#515151"
        });
        $(this).children(".albumActions").children("a").css({
            color: "#515151",
        });

        $(this).children(".albumTop").children(".albumIcon").children("span").addClass("albumIconLineHover");

    }, function () {
        $(this).css("background", "#fafafa");
        $(this).children(".albumInfos").css({
            color: "#a9a9a9"
        });
        $(this).children(".albumActions").children("a").css({
            color: "#848484"
        });        
        
        $(this).children(".albumTop").children(".albumIcon").children("span").removeClass("albumIconLineHover");
    })

    $(".categoryBox").children(".albumActions").children("a").hover(function () {
        $(this).css({
            color : "#000000",
        });
    }, function () {
        $(this).css({
            color : "#515151",
        });
    });

    /************************************/
    $(".categoryBox").addClass("line_cat").removeClass("tile_cat");
    $(".addAlbum").removeClass("tile_add");
    $(".categoryBox").css({
        minWidth: "90%",
        maxWidth: "100%",
        flexDirection: "row",
        maxHeight: "60px",
        alignItems: "unset",
        margin: "5px 15px"
    });

    $(".albumIcon").css({
        height: "60px"
    });

    $(".albumIcon span").css({
        fontSize: "14px",
        width: "20px",
        padding: "8px"
    });
    
    $(".addAlbumHead span").css({
        fontSize:"14px",
        width: "20px",
        height: "20px",
        padding: "8px"
    });

    $(".albumInfos").css({
        marginLeft: "auto",
        flexDirection: "row",
        justifyContent: "space-around",
        width: "auto"
    });

    $(".albumInfos p").css({
        textAlign: "right",
        margin: "0",
        whiteSpace: "nowrap"
    });

    $(".albumInfos p:last-child").css({
        width: "270px"
    });

    $(".albumTop").css({
        width: "35%",
        justifyContent: "flex-start",
        flexDirection: "row",
        alignItems : "baseline",
        height: "75px"
    });

    $(".albumTitle").css("padding", "0 15px");

    $(".addAlbum").css({
        minWidth: "90%",
        maxWidth: "100%",
        flexDirection: "row",
        maxHeight: "60px",
        margin: "15px 15px 5px 15px"
    });

    $(".addAlbum form label").css({
        display: "none"
    });

    $(".addAlbumHead").css({
        flexDirection: "row",
        transform: "translateY(0)",
        alignItems: "center",
        marginTop: "0",
        transform: "translateX(200px)",
        marginBottom: "0"
    });

    $(".addAlbum form").css({
        flexDirection: "row",
        marginTop: "0",
        marginBottom: "0",
        transitionDelay: "0s"
    });

    $(".addAlbum.input-mode form").css({
        transitionDelay: "0s",
    });

    $(".addAlbum form").css("align-items", "center");
    $(".addAlbum form input").css("margin", "0px 10px 0px 10px");
    $(".addAlbum form button").css("margin", "0px 20px");
    $(".addAlbum p").css("margin-bottom", "0px");

    $(".addAlbumHead p").css("margin-left", "15px");

    $(".albumActions").css({
        flexDirection : "row",
        margin: "auto 0px",
        width: "300px",
    });

    $(".albumActions a").css({
      minWidth : "30px"
    });

    $(".albumActions a:first-child").css("margin-left", "35px");
    $(".albumActions a:last-child").css("margin-right", "35px");

}

function setDisplayTile() {

    ShowIconDesc();

    $(".albumActions").css("display", "flex");
    removeHoverEffect($(".categoryBox"));
    removeHoverEffect($(".categoryBox").children(".albumActions").children("a"));
    $(".categoryBox").children(".albumActions").children("a").hover(function () {
        $(this).css({
            color : "#FFA646"
        })
    }, function () {
        $(this).css({
            color : "#848484"
        })
    });

    AddHoverOnAlbumActions();

    $(".addAlbum.input-mode form").css({
        transitionDelay: "0s",
    });
    $(".categoryBox").removeClass("line_cat").addClass("tile_cat");
    $(".addAlbum").addClass("tile_add");
    $(".categoryBox").css({
        minWidth: "220px",
        maxWidth: "280px",
        flexDirection: "column",
        maxHeight: "320px",
        alignItems: "center",
        margin: "15px"
    });

    $(".albumActions").css({
        flexDirection : "column",
        margin:"auto",
        alignItems: "flex-start",
        width: "75%",
    });

    $(".albumInfos").css({
        marginLeft: "0",
        flexDirection: "column",
    });

    $(".albumInfos p:last-child").css({
        width: "auto"
    });
    $(".albumInfos p").css({
        margin: "0",
        textAlign: "center",
        whiteSpace: "normal"
    });

    $(".albumIcon").css({
        height: "80px"
    });

    $(".albumIcon span").css({
        fontSize: "19px",
        width: "27px",
        padding: "10px"
    });

    $(".albumTop").css({
        width: "85%",
        flexDirection: "column",
        alignItems: "unset",
        height: "110px",
    });

    $(".albumTitle").css("padding", "0");

    $(".addAlbum").css({
        minWidth: "220px",
        maxWidth: "280px",
        flexDirection: "column",
        maxHeight: "320px",
        margin: "15px"
    });

     $(".addAlbumHead").css({
        flexDirection: "column",
        transform: "translateY(75px)",
        alignItems: "center",
        marginTop: "10px",
        transition: "0.4s ease",
        marginBottom: "0"
    });

    $(".addAlbum form").css({
        flexDirection: "column",
        marginTop: "auto",
        marginBottom: "20px",
        transitionDelay: "0s"
    });

    $(".addAlbum form input").css("margin", "0px 10px 10px 10px");
    $(".addAlbum form button").css("margin", "10px auto 0 auto");
    $(".addAlbum p").css("margin-bottom", "20px");

    $(".addAlbum form label").css({
        display: "flex",
        margin: "-25px 0 0 15px"
    });

    $(".addAlbumHead p").css("margin-left", "0");

    $(".addAlbumHead span").css({
        fontSize:"19px",
        width: "27px",
        height: "27px",
        padding: "10px"
    });

    $(".albumInfos p").css("margin", "0");

    $(".albumActions a").css({
      minWidth : "0px"
    });

    $(".albumActions a:first-child").css("margin-left", "5px");
    $(".albumActions a:last-child").css("margin-left", "5px");
}

function ShowIconDesc() {
    $(".albumActions span.iconLegend").show();
}

function removeIconDesc() {
    $(".albumActions span.iconLegend").hide();
}

function removeHoverEffect(e) {
    e.unbind('mouseenter').unbind('mouseleave');
}

function AddHoverOnAlbumActions() {
    $(".albumActions").css("display", "none");
    $(".categoryBox").hover(function () {
        $(this).children(".albumActions").css("display", "flex");
    }, function () {
        $(this).children(".albumActions").css("display", "none");
    });
}


$(document).ready(function () {

    if (!$.cookie("pwg_album_manager_view")) {
        $.cookie("pwg_album_manager_view", "tile");
    }

    $(".addAlbum").on("click", function (e) {
        if (e.target.className !== "cancelAddAlbum") {
            $(".addAlbum").addClass('input-mode');

            if ($.cookie("pwg_album_manager_view") !== "tile") {
                $(".addAlbum p").hide(300);
            }
        };
    });

    $(".cancelAddAlbum").on("click", function () {
        $('.addAlbum').removeClass('input-mode');
        $(".addAlbum p").show(800);
    });

    if ($("#displayCompact").is(":checked")) {
        setDisplayCompact();
    };

    if ($("#displayLine").is(":checked")) {
        setDisplayLine();
    };

    if ($("#displayTile").is(":checked")) {
        setDisplayTile();
    };

    $("#displayCompact").change(function () {
        setDisplayCompact();

        if ($(".addAlbum").hasClass("input-mode")) {
            $(".addAlbum p").hide();
        }
        
        $.cookie("pwg_album_manager_view", "compact");
    });

    $("#displayLine").change(function () {
        setDisplayLine();

        if ($(".addAlbum").hasClass("input-mode")) {
            $(".addAlbum p").hide();
        }

        $.cookie("pwg_album_manager_view", "line");
    });

    $("#displayTile").change(function () {
        setDisplayTile();

        if ($(".addAlbum").hasClass("input-mode")) {
            $(".addAlbum p").show();
        }
        
        $.cookie("pwg_album_manager_view", "tile");
    });
});