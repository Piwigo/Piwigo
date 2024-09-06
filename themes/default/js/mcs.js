$(document).ready(function () {
  related_categories_ids = [];

  $(".linkedAlbumPopInContainer .ClosePopIn").addClass(prefix_icon + "cancel");
  $(".linkedAlbumPopInContainer .searching").addClass(prefix_icon + "spin6").hide();
  $(".AddIconContainer").css('display', 'none');
  $(".filter-validate").on("click", function () {
    $(this).find(".loading").css("display", "block");
    $(this).find(".validate-text").hide();
  });

  // If we open another filter, hide all other dropdowns expect the one just opened
  $("div.filter").on("click", function () {
    $(this).siblings().removeClass("show-filter-dropdown");
    $(this).siblings().children("div.filter-form").css('display','none');
  });

  // If we open the choose filters modal hide all filter forms if any open
  $("div.filter-manager").on("click", function () {
    $('div.filter').children("div.filter-form").css('display','none');
  });

  global_params.search_id = search_id;

  if (!global_params.fields) {
    global_params.fields = {};
  }

  // Declare params sent to pwg.images.filteredSearch.update
  // PS for performSearch()
  PS_params = {};
  PS_params.search_id = search_id;
  empty_filters_list = [];

  // Setup word filter
  if (global_params.fields.allwords) {
    $(".filter-word").css("display", "flex");
    $(".filter-manager-controller.word").prop("checked", true);

    word_search_str = "";
    word_search_words = global_params.fields.allwords.words != null ? global_params.fields.allwords.words : [];
    word_search_words.forEach(word => {
      word_search_str += word + " ";
    });
    $("#word-search").val(word_search_str.slice(0, -1));

    if (global_params.fields.allwords.words && global_params.fields.allwords.words.length > 0) {
      $(".filter-word").addClass("filter-filled");
      $(".filter-word .search-words").html(word_search_str.slice(0, -1));
    } else {
      $(".filter-word .search-words").html(str_word_widget_label);
    }

    word_search_fields = global_params.fields.allwords.fields;
    Object.keys(word_search_fields).forEach(field_key => {
      $("#"+word_search_fields[field_key]).prop("checked", true);
    });

    word_search_mode = global_params.fields.allwords.mode;
    $(".word-search-options input[value=" + word_search_mode + "]").prop("checked", true);

    if (global_params.fields.search_in_tags) {
      $("#tags").prop("checked", true);
    }

    $(".filter-word .filter-actions .clear").on('click', function () {
      $(".filter-word #word-search").val('');
      $(".filter-word .search-params input").prop('checked', true);
      $(".filter-word .word-search-options input[value='AND']").prop('checked', true);
    });

    PS_params.allwords = word_search_str.slice(0, -1);
    PS_params.allwords_fields = word_search_fields;
    PS_params.allwords_mode = word_search_mode;

    empty_filters_list.push(PS_params.allwords);
  }
  //Hide filter spinner
  $(".filter-spinner").hide();

  // Setup tag filter
  $("#tag-search").each(function() {
    $(this).selectize({
      plugins: ['remove_button'],
      maxOptions:$(this).find("option").length,
      items: global_params.fields.tags ? global_params.fields.tags.words : null,
    });
  });

  if (global_params.fields.tags) {
    $(".filter-tag").css("display", "flex");
    $(".filter-manager-controller.tags").prop("checked", true);
    $(".filter-tag-form .search-params input[value=" + global_params.fields.tags.mode + "]").prop("checked", true);

    tag_search_str = "";
    $("#tag-search")[0].selectize.getValue().forEach(id => {
      tag_search_str += $("#tag-search")[0].selectize.getItem(id).text().replace(/\(\d+ \w+\)×/, '').trim() + ", ";
    });
    if (global_params.fields.tags.words && global_params.fields.tags.words.length > 0) {
      $(".filter-tag").addClass("filter-filled");
      $(".filter.filter-tag .search-words").text(tag_search_str.slice(0, -2));
    } else {
      $(".filter.filter-tag .search-words").text(str_tags_widget_label);
    }

    $(".filter-tag .filter-actions .clear").on('click', function () {
      $("#tag-search")[0].selectize.clear();
      $(".filter-tag .search-params input[value='AND']").prop('checked', true);
    });

    PS_params.tags = global_params.fields.tags.words.length > 0 ? global_params.fields.tags.words : '';
    PS_params.tags_mode = global_params.fields.tags.mode;

    empty_filters_list.push(PS_params.tags);
  }

  // Setup Date post filter
  if (global_params.fields.date_posted) {

    $(".filter-date_posted").css("display", "flex");
    $(".filter-manager-controller.date_posted").prop("checked", true);

    if (global_params.fields.date_posted.preset != null && global_params.fields.date_posted.preset != "") {
      // If filter is used and not empty check preset date option
      $("#date_posted-" + global_params.fields.date_posted.preset).prop("checked", true);
      date_posted_str = $('.date_posted-option label#'+ global_params.fields.date_posted.preset +' .date-period').text();

      // if option is custom check custom dates
      if ('custom' == global_params.fields.date_posted.preset && global_params.fields.date_posted.custom != null)
      {
        date_posted_str = '';
        var customArray = global_params.fields.date_posted.custom

        $(customArray).each(function( index ) {
          var customValue = this.substring(1, $(this).length);

          $("#date_posted_"+customValue).prop("checked", true).addClass('selected');
          $("#date_posted_"+customValue).siblings('label').find('.checked-icon').show();

          date_posted_str += $('.date_posted-option label#'+ customValue +' .date-period').text()

          if($(global_params.fields.date_posted.custom).length > 1 && index != $(customArray).length-1)
          {
            date_posted_str += ', ';
          }
        });
      }
      
      // change badge label if filter not empty
      $(".filter-date_posted").addClass("filter-filled");
      $(".filter.filter-date_posted .search-words").text(date_posted_str);
    }

    $(".filter-date_posted .filter-actions .clear").on('click', function () {
      updateFilters('date_posted', 'add');
      $(".date_posted-option input").prop('checked', false);
      $(".date_posted-option input").trigger('change');

      // $('.date_posted-option input').removeAttr('disabled');
      // $('.date_posted-option input').removeClass('grey-icon'); 
    });

    // Disable possiblity for user to select custom option, its gets selected programtically later on
    $("#date_posted_custom").attr('disabled', 'disabled');

    // Handle toggle between preset and custom options
    $(".custom_posted_date_toggle").on("click", function (e) {
      $('.custom_posted_date').toggle()
      $('.preset_posted_date').toggle()
    });

    // Handle accoridan features in custom options
    $(".custom_posted_date .accordion-toggle").on("click", function (e) {
      var clickedOption = $(this).parent();
      $(clickedOption).toggleClass('show-child');
      if('year' == $(this).data('type'))
      {
        $(clickedOption).parent().find('.date_posted-option.month').toggle();
      }
      else if('month' == $(this).data('type'))
      {
        $(clickedOption).parent().find('.date_posted-option.day').toggle();
      }
    });

    // On custom date input select
    $(".custom_posted_date .date_posted-option input").change(function() {
      var parentOption = $(this).parent()

      if(true == $(this).prop("checked")){
        // Toggle tick icon on selected date in custom list
        $(this).siblings('label').find('.checked-icon').show();

        // Add class selected to selected option,
        // We want to find which are selected to handle the others
        $(this).addClass('selected')
        $(parentOption).addClass('selected')
        $(parentOption).find('.mcs-icon').addClass('selected')
      }
      else
      {
        // Toggle tick icon on selected date in custom list
        $(this).siblings('label').find('.checked-icon').hide();

        // Add class selected to selected option,
        // We want to find which are selected to handle the others
        $(this).removeClass('selected')
        $(parentOption).removeClass('selected')
        $(parentOption).find('.mcs-icon').removeClass('selected')
      }

      // TODO finish handling grey tick on child options, and having specifi icon to show children are selected 

      // if this is selected then disable selecting children, and display grey tick 
      // var selectedContainerID = $(this).parent().parent().attr('id');
      // if ($(this).is(":checked"))
      // {
      //   $('#'+selectedContainerID+' .date_posted-option input').not('.selected').attr('disabled', 'disabled');
      //   $('#'+selectedContainerID+' .date_posted-option .mcs-icon').not('.selected').addClass('grey-icon');

      //   if($(parentOption).hasClass('year'))
      //   {
      //     if($(parentOption).find('label .mcs-icon').hasClass('gallery-icon-menu'))
      //     {
      //       $(parentOption).find('label .mcs-icon').toggleClass('gallery-icon-menu gallery-icon-checkmark').show();
      //     } 
      //   }
      //   else if($(parentOption).hasClass('month'))
      //   {
      //     $(this).parents('.year').find('label .mcs-icon').first().toggleClass('gallery-icon-menu gallery-icon-checkmark grey-icon');
      //   }
      //   else if ($(parentOption).hasClass('day'))
      //   {
      //     $(this).parents('.year').find('label .mcs-icon').first().toggleClass('gallery-icon-menu gallery-icon-checkmark').show();
      //     $(this).parents('.month').find('label .mcs-icon').first().toggleClass('gallery-icon-menu gallery-icon-checkmark').show();
      //   }
      // }
      // else
      // {
      //   $('#'+selectedContainerID+' .date_posted-option input').not('.selected').removeAttr('disabled');
      //   $('#'+selectedContainerID+' .date_posted-option .mcs-icon').not('.selected').removeClass('grey-icon');

      //   $(this).parents('.year').find('label .mcs-icon').first().toggleClass('gallery-icon-menu gallery-icon-checkmark grey-icon');
      //   $(this).parents('.month').find('label .mcs-icon').first().toggleClass('gallery-icon-menu gallery-icon-checkmark grey-icon');
          
      // }

      // Used to select custom in preset list if dates are selected
      if($('.custom_posted_date input:checked').length > 0)
      {
        $("#date_posted-custom").prop('checked', true);
        $('.preset_posted_date input').attr('disabled', 'disabled');
      }
      else{
        $("#date_posted-custom").prop('checked', false);
        $('.preset_posted_date input').removeAttr('disabled');
      }

    });

    // Used to select custom in preset list if dates are selected
    if($('.custom_posted_date input:checked').length > 0)
    {
      $("#date_posted-custom").prop('checked', true);
      $('.preset_posted_date input').attr('disabled', 'disabled');
    }
    else{
      $("#date_posted-custom").prop('checked', false);
      $('.preset_posted_date input').removeAttr('disabled');
    }

    PS_params.date_posted_preset = global_params.fields.date_posted.preset != '' ? global_params.fields.date_posted.preset : '';
    PS_params.date_posted_custom = global_params.fields.date_posted.custom != '' ? global_params.fields.date_posted.custom : '';

    empty_filters_list.push(PS_params.date_posted_preset);
    empty_filters_list.push(PS_params.date_posted_custom);
  }

  // Setup album filter
  if (global_params.fields.cat) {
    $(".filter-album").css("display", "flex");
    $(".filter-manager-controller.album").prop("checked", true);
  
    album_widget_value = "";
    global_params.fields.cat.words.forEach(cat_id => {
      add_related_category(cat_id, fullname_of_cat[cat_id]);
      album_widget_value += fullname_of_cat[cat_id] + ", ";
    });
    if (global_params.fields.cat.words && global_params.fields.cat.words.length > 0) {
      $(".filter-album").addClass("filter-filled");
      $(".filter-album .search-words").html(album_widget_value.slice(0, -2));
    } else {
      $(".filter-album .search-words").html(str_album_widget_label);
    }
    
    if (global_params.fields.cat.sub_inc) {
      $("#search-sub-cats").prop("checked", true);
    }

    $(".filter-album .filter-actions .clear").on('click', function () {
      $(".filter-album .search-params input[value='AND']");
      related_categories_ids = [];
      $(".selected-categories-container").empty();
      $("#search-sub-cats").prop('checked', false);
    });

    PS_params.categories = global_params.fields.cat.words.length > 0 ? global_params.fields.cat.words : '';
    PS_params.categories_withsubs = global_params.fields.cat.sub_inc;

    empty_filters_list.push(PS_params.categories);
  }
  
  // Setup author filter
  $("#authors").each(function() {
    $(this).selectize({
      plugins: ['remove_button'],
      maxOptions:$(this).find("option").length,
      items: global_params.fields.author ? global_params.fields.author.words : null,
    });
    if (global_params.fields.author) {
      $(".filter-authors").css("display", "flex");
      $(".filter-manager-controller.author").prop("checked", true);

      author_search_str = "";
      $("#authors")[0].selectize.getValue().forEach(id => {
        author_search_str += $("#authors")[0].selectize.getItem(id).text().replace(/\(\d+ \w+\)×/, '').trim() + ", ";
      });

      if (global_params.fields.author.words && global_params.fields.author.words.length > 0) {
        $(".filter-authors").addClass("filter-filled");
        $(".filter.filter-authors .search-words").text(author_search_str.slice(0, -2));
      } else {
        $(".filter.filter-authors .search-words").text(str_author_widget_label);
      }
      
      $(".filter-authors .filter-actions .clear").on('click', function () {
        $("#authors")[0].selectize.clear();
      });

      PS_params.authors = global_params.fields.author.words.length > 0 ? global_params.fields.author.words : '';

      empty_filters_list.push(PS_params.authors);
    }
  });

  // Setup added_by filter
  if (global_params.fields.added_by) {
    $(".filter-added_by").css("display", "flex");
    $(".filter-manager-controller.added_by").prop("checked", true);

    if (global_params.fields.added_by && global_params.fields.added_by.length > 0) {
      $(".filter-added_by").addClass("filter-filled");

      added_by_names = [];

      $(".added_by-option").each(function () {
        input = $(this).find('input');
        added_by_id = parseInt(input.attr('name'));

        if (jQuery.inArray(added_by_id, global_params.fields.added_by) >= 0) {
          input.prop('checked', true);
          added_by_names.push($(this).find('.added_by-name').text());
        }
      });

      $(".filter.filter-added_by .search-words").text(added_by_names.join(', '));

    } else {
      $(".filter.filter-added_by .search-words").text(str_added_by_widget_label);
    }

    $(".filter-added_by .filter-actions .clear").on('click', function () {
      $(".filter-added_by .added_by-option input").prop("checked", false);
    });

    PS_params.added_by = global_params.fields.added_by.length > 0 ? global_params.fields.added_by : '';

    empty_filters_list.push(PS_params.added_by);
  }

  // Setup filetypes filter
  if (global_params.fields.filetypes) {
    $(".filter-filetypes").css("display", "flex");
    $(".filter-manager-controller.filetypes").prop("checked", true);

    filetypes_search_str = "";
    global_params.fields.filetypes.forEach(ft => {
      filetypes_search_str += ft + ", ";
    });
  
    if (global_params.fields.filetypes && global_params.fields.filetypes.length > 0) {
      $(".filter-filetypes").addClass("filter-filled");
      $(".filter.filter-filetypes .search-words").text(filetypes_search_str.toUpperCase().slice(0, -2));

      $(".filetypes-option input").each(function () {
        if (global_params.fields.filetypes.includes($(this).attr('name'))) {
          $(this).prop('checked', true);
        }
      });
    } else {
      $(".filter.filter-filetypes .search-words").text(str_filetypes_widget_label);
    }

    $(".filter-filetypes .filter-actions .clear").on('click', function () {
      $(".filter-filetypes .filetypes-option input").prop("checked", false);
    });

    PS_params.filetypes = global_params.fields.filetypes.length > 0 ? global_params.fields.filetypes : '';

    empty_filters_list.push(PS_params.filetypes);
  }

  // Setup Ratio filter
  if (global_params.fields.ratios) {
    $(".filter-ratios").css("display", "flex");
    $(".filter-manager-controller.ratios").prop("checked", true);

    ratios_search_str = "";
    global_params.fields.ratios.forEach(ft => {
      ratios_search_str += str_ratios_label[ft] + ", ";
    });
  
    if (global_params.fields.ratios && global_params.fields.ratios.length > 0) {
      $(".filter-ratios").addClass("filter-filled");
      $(".filter.filter-ratios .search-words").text(ratios_search_str.slice(0, -2));

      $(".ratios-option input").each(function () {
        if (global_params.fields.ratios.includes($(this).attr('name'))) {
          $(this).prop('checked', true);
        }
      });
    } else {
      $(".filter.filter-ratios .search-words").text(str_ratio_widget_label);
    }

    $(".filter-ratios .filter-actions .clear").on('click', function () {
      $(".filter-ratios .ratios-option input").prop("checked", false);
    });

    PS_params.ratios = global_params.fields.ratios.length > 0 ?  global_params.fields.ratios  : '';

    empty_filters_list.push(PS_params.ratios);
  }

  // Setup rating filter
  if (global_params.fields.ratings) {

    $(".filter-ratings").css("display", "flex");
    $(".filter-manager-controller.ratings").prop("checked", true);

    ratings_search_str = "";
    global_params.fields.ratings.forEach(function(ft){
      if(0 == ft )
      {
        ratings_search_str = str_no_rating + ", ";
      }
      else
      {
        ratings_search_str = str_between_rating.split("%d");
        ratings_search_str = ratings_search_str[0] + (ft-1) + ratings_search_str[1] + ft + ratings_search_str[2];
      }
    });
  
    if (global_params.fields.ratings && global_params.fields.ratings.length > 0) {
      $(".filter-ratings").addClass("filter-filled");
      $(".filter.filter-ratings .search-words").text(ratings_search_str);

      $(".ratings-option input").each(function () {
        if (global_params.fields.ratings.includes($(this).attr('name'))) {
          $(this).prop('checked', true);
        }
      });
    } else {
      $(".filter.filter-ratings .search-words").text(str_rating_widget_label);
    }

    $(".filter-ratings .filter-actions .clear").on('click', function () {
      $(".filter-ratings .ratings-option input").prop("checked", false);
    });

    PS_params.ratings = global_params.fields.ratings.length > 0 ?  global_params.fields.ratings  : '';

    empty_filters_list.push(PS_params.ratings);
  }

  // Setup filesize filter
  if (global_params.fields.filesize_min != null && global_params.fields.filesize_max != null) {

    $(".filter-filesize").css("display", "flex");
    $(".filter-manager-controller.filesize").prop("checked", true);
    $(".filter.filter-filesize .slider-info").html(sprintf(sliders.filesizes.text,sliders.filesizes.selected.min,sliders.filesizes.selected.max,));

    $('[data-slider=filesizes]').pwgDoubleSlider(sliders.filesizes);

    $('[data-slider=filesizes]').on("slidestop", function(event, ui) {
      var min = $('[data-slider=filesizes]').find('[data-input=min]').val();
      var max = $('[data-slider=filesizes]').find('[data-input=max]').val();

      $('input[name=filter_filesize_min_text]').val(min).trigger('change');
      $('input[name=filter_filesize_max_text]').val(max).trigger('change');

    });

    if( global_params.fields.filesize_min != null && global_params.fields.filesize_max > 0) {
      $(".filter-filesize").addClass("filter-filled");
      $(".filter.filter-filesize .search-words").html(sprintf(sliders.filesizes.text,sliders.filesizes.selected.min,sliders.filesizes.selected.max,));
    }
    else 
    {
      $(".filter.filter-filesize .search-words").text(str_filesize_widget_label);
    }

    $(".filter-filesize .filter-actions .clear").on('click', function () {
      updateFilters('filesize', 'add');
      $(".filter-filesize").trigger("click");
      $('[data-slider=filesizes]').pwgDoubleSlider(sliders.filesizes);
      if ($(".filter-filesize").hasClass("filter-filled")) {
        $(".filter-filesize").removeClass("filter-filled")
        $(".filter.filter-filesize .search-words").text(str_filesize_widget_label);
      }
    });

    PS_params.filesize_min = global_params.fields.filesize_min  != null ?  global_params.fields.filesize_min  : '';
    PS_params.filesize_max = global_params.fields.filesize_max  != null ?  global_params.fields.filesize_max  : '';

    empty_filters_list.push(PS_params.filesize_min);
    empty_filters_list.push(PS_params.filesize_max);
  }

  // Setup Height filter
  if (global_params.fields.height_min != null && global_params.fields.height_max != null) {
    $(".filter-height").css("display", "flex");
    $(".filter-manager-controller.height").prop("checked", true);
    $(".filter.filter-height .slider-info").html(sprintf(sliders.heights.text,sliders.heights.selected.min,sliders.heights.selected.max,));

    $('[data-slider=heights]').pwgDoubleSlider(sliders.heights);

    if( global_params.fields.height_min > 0 && global_params.fields.height_max > 0) {
      $(".filter-height").addClass("filter-filled");
      $(".filter.filter-height .search-words").html(sprintf(sliders.heights.text,sliders.heights.selected.min,sliders.heights.selected.max,));
    }
    else 
    {
      $(".filter.filter-height .search-words").text(str_height_widget_label);
    }

    $(".filter-height .filter-actions .clear").on('click', function () {
      updateFilters('height', 'add');
      $(".filter-height").trigger("click");
      $('[data-slider=heights]').pwgDoubleSlider(sliders.heights);
      if ($(".filter-height").hasClass("filter-filled")) {
        $(".filter-height").removeClass("filter-filled")
        $(".filter.filter-height .search-words").text(str_height_widget_label);
      }
    });

    PS_params.height_min = global_params.fields.height_min  != null ?  global_params.fields.height_min  : '';
    PS_params.height_max = global_params.fields.height_max  != null ?  global_params.fields.height_max  : '';

    empty_filters_list.push(PS_params.height_min);
    empty_filters_list.push(PS_params.height_max);
  }

  // Setup Width filter
  if (global_params.fields.width_min != null && global_params.fields.width_max != null) {
    $(".filter-width").css("display", "flex");
    $(".filter-manager-controller.width").prop("checked", true);
    $(".filter.filter-width .slider-info").html(sprintf(sliders.widths.text,sliders.widths.selected.min,sliders.widths.selected.max,));

    $('[data-slider=widths]').pwgDoubleSlider(sliders.widths);

    if( global_params.fields.width_min > 0 && global_params.fields.width_max > 0) {
      $(".filter-width").addClass("filter-filled");
      $(".filter.filter-width .search-words").html(sprintf(sliders.widths.text,sliders.widths.selected.min,sliders.widths.selected.max,));
    }
    else 
    {
      $(".filter.filter-width .search-words").text(str_width_widget_label);
    }

    $(".filter-width .filter-actions .clear").on('click', function () {
      updateFilters('width', 'add');
      $(".filter-width").trigger("click");
      $('[data-slider=widths]').pwgDoubleSlider(sliders.widths);
      if ($(".filter-width").hasClass("filter-filled")) {
        $(".filter-width").removeClass("filter-filled")
        $(".filter.filter-width .search-words").text(str_width_widget_label);
      }
    });

    PS_params.width_min = global_params.fields.width_min  != null ?  global_params.fields.width_min  : '';
    PS_params.width_max = global_params.fields.width_max  != null ?  global_params.fields.width_max  : '';

    empty_filters_list.push(PS_params.width_min);
    empty_filters_list.push(PS_params.width_max);
  }


  // Adapt no result message
  if ($(".filter-filled").length === 0) {
    $(".mcs-no-result .text .top").html(str_empty_search_top_alt);
    $(".mcs-no-result .text .bot").html(str_empty_search_bot_alt);
  }

  if (!empty_filters_list.every(param => param === "" || param === null)) {
    $(".clear-all").addClass("clickable");
    $(".clear-all.clickable").on('click', function () {
      exclude_params = ['search_id', 'allwords_mode', 'allwords_fields', 'tags_mode', 'categories_withsubs'];
      for (const key in PS_params) {
        if (!exclude_params.includes(key)) {
          if("date_posted_custom" == key)
          {
            PS_params[key] = [];
          }
          else
          {
            PS_params[key] = '';
          }
        }
      }
      performSearch(PS_params, true);
    });
  }

  /**
   * Filter Manager
   */
  $(".filter-manager").on('click', function () {
    $(".filter-manager-popin").show();
  });

  $(document).on('keyup', function (e) {
    // 27 is 'Escape'
    if(e.keyCode === 27) {
      $(".filter-manager-popin .filter-manager-close").trigger('click');
    }
    // 13 is 'Enter'
    if (e.keyCode === 13) {
      $('.filter-form .filter-validate').each(function () {
        if ($(this).is(':visible')) {
          $(this).trigger('click');
        }
      })
    }
  });
  
  $(".filter-manager-popin").on('click', function(e) {
    if ($(this).is(e.target) && $(this).has(e.target).length === 0) {
      $(".filter-manager-popin .filter-manager-close").trigger('click');
    }
  });

  $(".filter-manager-popin .filter-cancel, .filter-manager-popin .filter-manager-close").on('click', function () {
    $(".filter-manager-popin").hide();
    $(".filter-manager-controller-container input").each(function (e) {
      if ($(this).is(':checked')) {
        if (!$(".filter.filter-" + $(this).data("wid")).is(':visible')) {
          $(this).prop('checked', false);
        }
      } else {
        if ($(".filter.filter-" + $(this).data("wid")).is(':visible')) {
          $(this).prop('checked', true);
        }
      }
    });
  });

  $(".filter-manager-popin .filter-validate").on('click', function () {
    $(".filter-manager-controller-container input").each(function (e) {
      if ($(this).is(':checked')) {
        if (!$(".filter.filter-" + $(this).data("wid")).is(':visible')) {
          updateFilters($(this).data("wid"), 'add');
        }
      } else {
        if ($(".filter.filter-" + $(this).data("wid")).is(':visible')) {
          updateFilters($(this).data("wid"), 'del');
        }
      }
    });
    // Set second param to true to trigger reload
    performSearch(PS_params ,true);
  });

  /**
   * Tags & Albums found
   */
  $(".mcs-tags-found").on('click', function () {
    $(".tags-found-popin").show();
  });
  $(".mcs-albums-found").on('click', function () {
    $(".albums-found-popin").show();
  });

  $(document).on('keyup', function (e) {
    // 27 is 'Escape'
    if(e.keyCode === 27) {
      $(".tags-found-popin .tags-found-close").trigger('click');
      $(".albums-found-popin .albums-found-close").trigger('click');
    }
  });
  
  $(".tags-found-popin").on('click', function(e) {
    if ($(this).is(e.target) && $(this).has(e.target).length === 0) {
      $(".tags-found-popin .tags-found-close").trigger('click');
    }
  });
  $(".tags-found-close").on('click', function () {
    $(".tags-found-popin").hide();
  });

  $(".albums-found-popin").on('click', function(e) {
    if ($(this).is(e.target) && $(this).has(e.target).length === 0) {
      $(".albums-found-popin .albums-found-close").trigger('click');
    }
  });
  $(".albums-found-close").on('click', function () {
    $(".albums-found-popin").hide();
  })


  /**
   * Filter Word
   */
  $(".filter-word").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form")) {
      return;
    }
    $(".filter-word-form").toggle(0, function () {
      
      if ($(this).is(':visible')) {
        $(".filter-word").addClass("show-filter-dropdown");
        $("#word-search").focus();
      } else {
        $(".filter-word").removeClass("show-filter-dropdown");

        global_params.fields.allwords = {};
        global_params.fields.allwords.words = $("#word-search").val();
        global_params.fields.allwords.mode = $(".word-search-options input:checked").attr('value');
        
        PS_params.allwords = $("#word-search").val();
        PS_params.allwords_mode = $(".word-search-options input:checked").attr('value');

        new_fields = [];
        $(".filter-word-form .search-params input:checked").each(function () {
          if ($(this).attr("name") == "tags") {
            global_params.fields.search_in_tags = true;
          }
          new_fields.push($(this).attr("name"));
        });
        if ($(".filter-word-form .search-params input[name='tags']:checked").length == 0) {
          delete global_params.fields.search_in_tags;
        }
        global_params.fields.allwords.fields = new_fields;
        PS_params.allwords_fields = new_fields.length > 0 ? new_fields : '';
      }
    });
  });
  $(".filter-word .filter-validate").on("click", function () {
    $(".filter-word").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-word .filter-actions .delete").on("click", function () {
    updateFilters('word', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-word").hasClass("filter-filled")) {
      $(".filter-word").hide();
      $(".filter-manager-controller.word").prop("checked", false);
    }
  });

  /**
   * Filter Tag
   */
  $(".filter-tag").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
    }
    $(".filter-tag-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-tag").addClass("show-filter-dropdown");
      } else {
        $(".filter-tag").removeClass("show-filter-dropdown");
        global_params.fields.tags = {};
        global_params.fields.tags.mode = $(".filter-tag-form .search-params input:checked").val();
        global_params.fields.tags.words = $("#tag-search")[0].selectize.getValue();

        PS_params.tags = $("#tag-search")[0].selectize.getValue().length > 0 ? $("#tag-search")[0].selectize.getValue() : '';
        PS_params.tags_mode = $(".filter-tag-form .search-params input:checked").val();
      }
    });
  });
  $(".filter-tag .filter-validate").on("click", function () {
    $(".filter-tag").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-tag .filter-actions .delete").on("click", function () {
    updateFilters('tag', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-tag").hasClass("filter-filled")) {
      $(".filter-tag").hide();
      $(".filter-manager-controller.tags").prop("checked", false);
    }
  });

  /**
   * Filter Date posted
   */
  $(".filter-date_posted").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form")) {
      return;
    }
    $(".filter-date_posted-form").toggle(0, function () {
      if ($(this).is(':visible'))
      {
        $(".filter-date_posted").addClass("show-filter-dropdown");
      }
      else 
      {
        $(".filter-date_posted").removeClass("show-filter-dropdown");

        var presetValue = $(".preset_posted_date .date_posted-option input:checked").val();

        global_params.fields.date_posted.preset = presetValue;
        PS_params.date_posted_preset = presetValue != null ? presetValue : "";
        
        if ('custom' == presetValue)
        {
          var customDates = [];

          $(".custom_posted_date .date_posted-option input:checked").each(function(){
            customDates.push($(this).val());
          });

          global_params.fields.date_posted.custom = customDates;
          PS_params.date_posted_custom = customDates.length > 0 ? customDates : "";
        }
      
      }
    });
  });

  $(".filter-date_posted .filter-validate").on("click", function () {
    $(".filter-date_posted").trigger("click");
    performSearch(PS_params, true);
  });
  
  $(".filter-date_posted .filter-actions .delete").on("click", function () {
    updateFilters('date_posted', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-date_posted").hasClass("filter-filled")) {
      $(".filter-date_posted").hide();
      $(".filter-manager-controller.date").prop("checked", false);
    }
  });

  /**
   * Filter Album
   */
  $(".filter-album").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove-item")) {
      return;
    }
    $(".filter-album-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-album").addClass("show-filter-dropdown");
      } else {
        $(".filter-album").removeClass("show-filter-dropdown");
        global_params.fields.cat = {};
        global_params.fields.cat.words = related_categories_ids;
        // global_params.fields.cat.search_params = $(".filter-form.filter-album-form .search-params input:checked").val().toLowerCase();
        global_params.fields.cat.sub_inc = $("input[name='search-sub-cats']:checked").length != 0;

        PS_params.categories = related_categories_ids.length > 0 ? related_categories_ids : '';
        PS_params.categories_withsubs = $("input[name='search-sub-cats']:checked").length != 0;
      }
    });
  });
  $(".filter-album .filter-validate").on("click", function () {
    $(".filter-album").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-album .filter-actions .delete").on("click", function () {
    updateFilters('album', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-album").hasClass("filter-filled")) {
      $(".filter-album").hide();
      $(".filter-manager-controller.album").prop("checked", false);
    }
  });

  $(".add-album-button").on("click", function () {
    open_album_selector();
  });

  /**
   * Author Widget
   */
  $(".filter-authors").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
    }
    $(".filter-author-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-authors").addClass("show-filter-dropdown");
      } else {
        $(".filter-authors").removeClass("show-filter-dropdown");
        global_params.fields.author = {};
        global_params.fields.author.mode = "OR";
        global_params.fields.author.words = $("#authors")[0].selectize.getValue();

        PS_params.authors = $("#authors")[0].selectize.getValue().length > 0 ? $("#authors")[0].selectize.getValue() : '';
      }
    });
  });
  $(".filter-authors .filter-validate").on("click", function () {
    $(".filter-authors").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-authors .filter-actions .delete").on("click", function () {
    updateFilters('authors', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-authors").hasClass("filter-filled")) {
      $(".filter-authors").hide();
      $(".filter-manager-controller.author").prop("checked", false);
    }
  });

  /**
   * Added by Widget
   */
  $(".filter-added_by").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
    }
    $(".filter-added_by-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-added_by").addClass("show-filter-dropdown");
      } else {
        $(".filter-added_by").removeClass("show-filter-dropdown");
        global_params.fields.added_by = {};
        global_params.fields.added_by.mode = "OR";

        added_by_array = []
        $(".added_by-option input:checked").each(function () {
          added_by_array.push($(this).attr('name'));
        });

        global_params.fields.added_by.words = added_by_array;

        PS_params.added_by = added_by_array.length > 0 ? added_by_array : '';
      }
    });
  });
  $(".filter-added_by .filter-validate").on("click", function () {
    $(".filter-added_by").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-added_by .filter-actions .delete").on("click", function () {
    updateFilters('added_by', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-added_by").hasClass("filter-filled")) {
      $(".filter-added_by").hide();
      $(".filter-manager-controller.added_by").prop("checked", false);
    }
  });

  /**
   * File type Widget
   */
  $(".filter-filetypes").on("click", function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
    }
    $(".filter-filetypes-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-filetypes").addClass("show-filter-dropdown");
      } else {
        $(".filter-filetypes").removeClass("show-filter-dropdown");

        filetypes_array = []
        $(".filetypes-option input:checked").each(function () {
          filetypes_array.push($(this).attr('name'));
        });

        global_params.fields.filetypes = filetypes_array;

        PS_params.filetypes = filetypes_array.length > 0 ? filetypes_array : '';
      }
    });
  });

  $(".filter-filetypes .filter-validate").on("click", function () {
    $(".filter-filetypes").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-filetypes .filter-actions .delete").on("click", function () {
    updateFilters('filetypes', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-filetypes").hasClass("filter-filled")) {
      $(".filter-filetypes").hide();
      $(".filter-manager-controller.filetypes").prop("checked", false);
    }
  });

  /**
   * Ratios widget
   */
    $(".filter-ratios").on('click', function (e) {
      if ($(".filter-form").has(e.target).length != 0 ||
          $(e.target).hasClass("filter-form") ||
          $(e.target).hasClass("remove")) {
        return;
      }
      $(".filter-ratios-form").toggle(0, function () {
        if ($(this).is(':visible')) {
          $(".filter-ratios").addClass("show-filter-dropdown");
        } else {
          $(".filter-ratios").removeClass("show-filter-dropdown");

          ratios_array = []
          $(".ratios-option input:checked").each(function () {
            ratios_array.push($(this).attr('name'));
          });

          global_params.fields.ratios = ratios_array;

          PS_params.ratios = ratios_array.length > 0 ? ratios_array : '';
        }
      });
    });

    $(".filter-ratios .filter-validate").on("click", function () {
      $(".filter-ratios").trigger("click");
      performSearch(PS_params, true);
    });
    $(".filter-ratios .filter-actions .delete").on("click", function () {
      updateFilters('ratios', 'del');
      performSearch(PS_params, true);
      if (!$(".filter-ratios").hasClass("filter-filled")) {
        $(".filter-ratios").hide();
        $(".filter-manager-controller.ratios").prop("checked", false);
      }
    });

  /**
   * Rating widget
   */
  $(".filter-ratings").on('click', function (e) {
    if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
    }
    $(".filter-ratings-form").toggle(0, function () {
      if ($(this).is(':visible')) {
        $(".filter-ratings").addClass("show-filter-dropdown");
      } else {
        $(".filter-ratings").removeClass("show-filter-dropdown");
        ratings_array = []

        $(".ratings-option input:checked").each(function () {
          ratings_array.push($(this).attr('name'));
        });

        global_params.fields.ratings = ratings_array;
        PS_params.ratings = ratings_array.length > 0 ? ratings_array : '';
          
      }
    });
  });

  $(".filter-ratings .filter-validate").on("click", function () {
    $(".filter-ratings").trigger("click");
    performSearch(PS_params, true);
  });
  $(".filter-ratings .filter-actions .delete").on("click", function () {
    updateFilters('ratings', 'del');
    performSearch(PS_params, true);
    if (!$(".filter-ratings").hasClass("filter-filled")) {
      $(".filter-ratings").hide();
      $(".filter-manager-controller.ratings").prop("checked", false);
    }
  });

  /**
   * Filesize widget
   */
    $(".filter-filesize").on('click', function (e) {
      if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
      }
      $(".filter-filesize-form").toggle(0, function () {
        if ($(this).is(':visible')) {
          $(".filter-filesize").addClass("show-filter-dropdown");
        } else {
          $(".filter-filesize").removeClass("show-filter-dropdown");
        }
      });

    });
    $(".filter-filesize .filter-validate").on("click", function () {
      filesize_min = Math.floor(($('input[name=filter_filesize_min]').val())*1024)
      filesize_max = Math.ceil(($('input[name=filter_filesize_max]').val())*1024)

      global_params.fields.filesize_min = filesize_min;
      global_params.fields.filesize_max = filesize_max;

      PS_params.filesize_min = filesize_min;
      PS_params.filesize_max = filesize_max;

      $(".filter-filesize").trigger("click");
      performSearch(PS_params, true);
    });

    $(".filter-filesize .filter-actions .delete").on("click", function () {
      updateFilters('filesize', 'del');
      performSearch(PS_params, true);
      if (!$(".filter-filesize").hasClass("filter-filled")) {
        $(".filter-filesize").hide();
        $(".filter-manager-controller.filesize").prop("checked", false);
      }
    });

  /**
   * Height widget
   */
    $(".filter-height").on('click', function (e) {
      if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
      }
      $(".filter-height-form").toggle(0, function () {
        if ($(this).is(':visible')) {
          $(".filter-height").addClass("show-filter-dropdown");
        } else {
          $(".filter-height").removeClass("show-filter-dropdown");
        }
      });

    });
    $(".filter-height .filter-validate").on("click", function () {
      height_min = $('input[name=filter_height_min]').val()
      height_max = $('input[name=filter_height_max]').val()

      global_params.fields.height_min = height_min;
      global_params.fields.height_max = height_max;

      PS_params.height_min = height_min;
      PS_params.height_max = height_max;

      $(".filter-height").trigger("click");
      performSearch(PS_params, true);
    });

    $(".filter-height .filter-actions .delete").on("click", function () {
      updateFilters('height', 'del');
      performSearch(PS_params, true);
      if (!$(".filter-height").hasClass("filter-filled")) {
        $(".filter-height").hide();
        $(".filter-manager-controller.height").prop("checked", false);
      }
    });

  /**
   * Width widget
   */
    $(".filter-width").on('click', function (e) {
      if ($(".filter-form").has(e.target).length != 0 ||
        $(e.target).hasClass("filter-form") ||
        $(e.target).hasClass("remove")) {
      return;
      }
      $(".filter-width-form").toggle(0, function () {
        if ($(this).is(':visible')) {
          $(".filter-width").addClass("show-filter-dropdown");
        } else {
          $(".filter-width").removeClass("show-filter-dropdown");
        }
      });

    });
    $(".filter-width .filter-validate").on("click", function () {
      width_min = $('input[name=filter_width_min]').val()
      width_max = $('input[name=filter_width_max]').val()

      global_params.fields.width_min = width_min;
      global_params.fields.width_max = width_max;

      PS_params.width_min = width_min;
      PS_params.width_max = width_max;

      $(".filter-width").trigger("click");
      performSearch(PS_params, true);
    });

    $(".filter-width .filter-actions .delete").on("click", function () {
      updateFilters('width', 'del');
      performSearch(PS_params, true);
      if (!$(".filter-width").hasClass("filter-filled")) {
        $(".filter-width").hide();
        $(".filter-manager-controller.width").prop("checked", false);
      }
    });
})

function performSearch(params, reload = false) {
  $.ajax({
    url: "ws.php?format=json&method=pwg.images.filteredSearch.create",
    type:"POST",
    dataType: "json",
    data: params,
    success:function(data) {
      if (reload && typeof data.result.search_url !== 'undefined') {
       reloadPage(data.result.search_url);
      }
    },
    error:function(e) {
      console.log(e);
      $(".filter-form ").append('<p class="error">Error</p>')
      $(".filter-validate").find(".validate-text").css("display", "block");
      $(".filter-validate").find(".loading").hide();
      $(".remove-filter").removeClass(prefix_icon + 'spin6 animate-spin').addClass(prefix_icon + 'cancel');
    },
  });
}

function add_related_category(cat_id, cat_link_path) {
    $(".selected-categories-container").append(
      "<div class='breadcrumb-item'>" +
        "<span class='link-path'>" + cat_link_path + "</span><span id="+ cat_id + " class='mcs-icon " + prefix_icon + "cancel remove-item'></span>" +
      "</div>"
    );

    related_categories_ids.push(cat_id);
    $(".invisible-related-categories-select").append("<option selected value="+ cat_id +"></option>");

    $("#"+ cat_id).on("click", function () {
      remove_related_category($(this).attr("id"));
    });

    close_album_selector();
}

function remove_related_category(cat_id) {
  $("#" + cat_id).parent().remove();

  cat_to_remove_index = related_categories_ids.indexOf(parseInt(cat_id));
  if (cat_to_remove_index > -1) {
    related_categories_ids.splice(cat_to_remove_index, 1);
  }
  if (related_categories_ids.length === 0) {
    related_categories_ids = [];
  }
}

function updateFilters(filterName, mode) {
  switch (filterName) {
    case 'word':
      if (mode == 'add') {
        global_params.fields.allwords = {};

        PS_params.allwords = '';
        PS_params.allwords_mode = 'AND';
        PS_params.allwords_fields = [];
      } else if (mode == 'del') {
        delete global_params.fields.allwords;

        delete PS_params.allwords;
        delete PS_params.allwords_mode;
        delete PS_params.allwords_fields;
      }
      break;

    case 'tag':
      if (mode == 'add') {
        global_params.fields.tags = {};

        PS_params.tags = '';
        PS_params.tags_mode = 'AND';
      } else if (mode == 'del') {
        delete global_params.fields.tags;

        delete PS_params.tags;
        delete PS_params.tags_mode;
      }
      break;

    case 'album':
      if (mode == 'add') {
        global_params.fields.cat = {};

        PS_params.categories = '';
        PS_params.categories_withsubs = false;
      } else if (mode == 'del') {
        delete global_params.fields.cat;

        delete PS_params.categories;
        delete PS_params.categories_withsubs;
      }
      break;

    case 'date_posted':
      if (mode == 'add') {
        global_params.fields['date_posted'] = {};
        global_params.fields.date_posted.preset = '';
        global_params.fields.date_posted.custom = [];

        PS_params.date_posted_preset = '';
        PS_params.date_posted_custom = [];

      } else if (mode == 'del') {
        delete global_params.fields.date_posted.preset;
        delete global_params.fields.date_posted.custom;

        delete PS_params.date_posted_preset;
        delete PS_params.date_posted_custom;
      }
      break;

    case 'filesize':
      if (mode == 'add') {
        global_params.fields.filesize_min = '';
        global_params.fields.filesize_max = '';

        PS_params.filesize_min = '';
        PS_params.filesize_max = '';

      } else if (mode == 'del') {
        delete global_params.fields.filesize_min;
        delete global_params.fields.filesize_max;

        delete PS_params.filesize_min;
        delete PS_params.filesize_max;
      }
      break;

    case 'height':
      if (mode == 'add') {
        global_params.fields.height_min = '';
        global_params.fields.height_max = '';
  
        PS_params.height_min = '';
        PS_params.height_max = '';

      } else if (mode == 'del') {
        delete global_params.fields.height_min;
        delete global_params.fields.height_max;

        delete PS_params.height_min;
        delete PS_params.height_max;
      }
      break;

    case 'width':
      if (mode == 'add') {
        global_params.fields.width_min = '';
        global_params.fields.width_max = '';

        PS_params.width_min = '';
        PS_params.width_max = '';

      } else if (mode == 'del') {
        delete global_params.fields.width_min;
        delete global_params.fields.width_max;

        delete PS_params.width_min;
        delete PS_params.width_max;
      }
      break;

    default:
      if (mode == 'add') {
        global_params.fields[filterName] = {};

        PS_params[filterName] = '';
      } else if (mode == 'del') {
        delete global_params.fields[filterName];

        delete PS_params[filterName];
      }
      break;
  }
}

function reloadPage(url){
  window.location.href = url;
}

/**
 * Replace the filter_form elements if they exceed the window
 */
function resize_filter_form(){
  $('.form_mobile_arrow').remove();
  $('.filter').each(function() {
    const window_width = $(window).innerWidth();
    const left_distance = $(this).offset().left;
    const filter_form = $(this).find($('.filter-form'));
    const filter_form_width = filter_form.innerWidth();
    const too_left = (left_distance + $(this).innerWidth()) - filter_form_width;
    const is_desktop = window.matchMedia("(min-width: 600px)").matches;
    filter_form.css('left', '0px');
    const margin_left = is_desktop ? 15 : 0;

    if(left_distance + filter_form_width > window_width) {
      const check_left = too_left < 0 ? Math.abs(too_left - margin_left) : 0;
      const mobile_marg = is_desktop ? 0 : 2;
      const replace_form_width = - filter_form_width + $(this).innerWidth() + check_left - mobile_marg;
      filter_form.css('left', replace_form_width+'px');
    }
    if(!is_desktop){
      const left_arrow = $(this).offset().left + ($(this).innerWidth() / 2);
      filter_form.prepend('<svg width="10" height="10" viewBox="0 0 14 14" class="form_mobile_arrow" style="left:'+left_arrow+'px"><polygon class="arrow-border" points="7,0 14,14 0,14"/><polygon class="arrow-fill" points="7,1 13.5,14 0.5,14"/></svg>');
    }
  });
}
$(window).on('load', function() {
  resize_filter_form();
});
$(window).on('resize', function() {
  resize_filter_form();
});