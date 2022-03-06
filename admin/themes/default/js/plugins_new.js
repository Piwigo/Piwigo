// <-- Define sort orders -->
var sortOrder = 'date';
var sortPlugins = (function (a, b) {
    if (sortOrder == 'downloads' || sortOrder == 'revision' || sortOrder == 'date')
        return parseInt($(a).data(sortOrder))
            < parseInt($(b).data(sortOrder)) ? 1 : -1;
    else
        return $(a).data(sortOrder).toLowerCase()
            > $(b).data(sortOrder).toLowerCase() ? 1 : -1;
});

$(function () {

    // <-- Set the advanced filters -->

    let betaTestPlugins = $('#showBetaTestPlugin')[0].hasAttribute('checked');

    // object that remember filters states (initialized later)
    let filters = {};

    // toggle advanced filter's panel
    $(".advanced-filter-btn").click(advanced_filter_button_click);
    $(".advanced-filter span.icon-cancel").click(advanced_filter_hide);

    function advanced_filter_button_click() {
        if (!$(".advanced-filter").hasClass("advanced-filter-open")) {
            advanced_filter_show();
        } else {
            advanced_filter_hide();
        }
    }

    function advanced_filter_show() {
        $(".advanced-filter-btn, .advanced-filter").addClass("advanced-filter-open");
    }

    function advanced_filter_hide() {
        $(".advanced-filter-btn, .advanced-filter").removeClass("advanced-filter-open");
    }

    jQuery('select[name="selectOrder"]').change(function () {
        sortOrder = this.value;
        $('.pluginBox').sortElements(sortPlugins);
        $.get("admin.php?plugins_new_order=" + sortOrder);
    });

    jQuery('#search').on("input", function () {
        applyFilter('search', this.value.toUpperCase());
        jQuery("#search").trigger("click");
    });

    $('.search-cancel').on('click', () => {
        applyFilter('search', '');
    })

    $(".buttonInstall").each(function () {
        let plugin_name = $(this).closest(".pluginBox").data('name');
        $(this).pwg_jconfirm_follow_href({
            alert_title: str_install_title.replace("%s", plugin_name),
            alert_confirm: str_confirm_msg,
            alert_cancel: str_cancel_msg
        });
    });

    jQuery('.certification').tipTip({
        'delay': 0,
        'fadeIn': 200,
        'fadeOut': 200
    });

    $('.pluginRating').each((i, node) => {
        let ratingContainer = $(node);
        let rating = ratingContainer.data('rating');
        displayStars(ratingContainer.find('.rating-star-container'), rating);
    })

    // put default values in the select
    let authorNames = [{ value: '', text: "-" }];
    let tagsNames = [{ value: '', text: "-" }]

    // read all plugin boxes to get author and tags
    $('.pluginBox').each((i,el) => {
        let author = $(el).data('author');
        author.split(', ').forEach(name => {
            if (!authorNames.find(el => el.value == name)) {
                authorNames.push({ value: name, text: name})
            }
        });

        let tags = $(el).data('tags');
        tags.split(', ').forEach(tag => {
            if (!tagsNames.find(el => el.value == tag)) {
                tagsNames.push({ value: tag, text: tag })
            }
        });
    })

    // initialize the Selectize control
    $select = $('#author-filter').selectize({
        onChange: function (value) {
            applyFilter('author', value);
        },
        plugins: ['remove_button'],
    });

    // fetch the instance
    let selectizeAuthor = $select[0].selectize;
    selectizeAuthor.addOption(authorNames);

    // initialize the Selectize control
    $select = $('#tag-filter').selectize({
        onChange: function (value) {
            applyFilter('tag', value);
        },
        plugins: ['remove_button'],
    });

    // fetch the instance
    let selectizeTag = $select[0].selectize;
    selectizeTag.addOption(tagsNames);

    $('.notation-filter-slider').slider({
        range: 'min',
        value: 0,
        min: 0,
        max: 5,
        step: 0.5,
        slide: function(event, ui) {
            updateRatingFilterLabel(ui.value);
            applyFilter("rating", ui.value);
        }
    })

    $('.revision-date-filter-slider').slider({
        range: 'min',
        value: 0,
        min: 0,
        max: 6,
        slide: function (event, ui) {
            let month;
            [month, _] = value_to_month(ui.value);
            updateRevisionFilterLabel(ui.value);
            applyFilter("revision", month);
        }
    });

    // All the slider values and it's corresponding month's number and label
    function value_to_month(val) {
        switch (val) {
            case 6:
                return [1, str_x_month.replace('%d', 1)];
                break;
            case 5:
                return [3, str_x_months.replace('%d', 3)];
                break;
            case 4:
                return [6, str_x_months.replace('%d', 6)];
                break;
            case 3:
                return [12, str_x_year.replace('%d', 1)];
                break;
            case 2:
                return [24, str_x_years.replace('%d', 2)];
                break;
            case 1:
                return [60, str_x_years.replace('%d', 5)];
                break;
            default:
                return [Number.MAX_SAFE_INTEGER, str_from_begining];
                break;
        }
    }

    // The certification filter dosen't include incompatible if the beta-test option is not checked
    let minCertification = betaTestPlugins ? -1 : 0;

    $('.certification-filter-slider').slider({
        range: 'min',
        value: minCertification,
        min: minCertification,
        max: 3,
        slide: function (event, ui) {
            updateCertificationFilterLabel(ui.value);
            applyFilter("certification", ui.value);
        }
    });

    // Diffrence between two dates, in months
    function monthDiff(d1, d2) {
        var months;
        months = (d2.getFullYear() - d1.getFullYear()) * 12;
        months -= d1.getMonth();
        months += d2.getMonth();
        return months <= 0 ? 0 : months;
    }

    updateRatingFilterLabel(0);
    updateCertificationFilterLabel(minCertification);
    updateRevisionFilterLabel(0);

    function displayStars(element, rating) {

        element.find('span').addClass('icon-star-empty');
        element.find('span i').attr('class','');

        rating = Math.round(rating * 2);

        if (rating % 2 == 1) {
            $(element).find('span[data-star=' + ((rating - 1) / 2) + '] i').addClass('icon-star-half')
            rating -= 1;
        }

        while (rating > 0) {
            rating -= 2;
            $(element).find('span[data-star=' + (rating / 2) + '] i').addClass('icon-star')
            $(element).find('span[data-star=' + (rating / 2) + ']').removeClass('icon-star-empty')
        }
    }

    // Updates labels when input change

    function updateRatingFilterLabel(value) {
        displayStars($('.advanced-filter-rating .rating-star-container'), value);
    }

    function updateCertificationFilterLabel(value) {
        let certifNode = $('.advanced-filter-certification .certification');
        certifNode.attr('data-certification', value);
        certifNode.attr('title', strs_certification[String(value)]);
        certifNode.tipTip({
            'delay': 0,
            'fadeIn': 200,
            'fadeOut': 200
        });
    }

    function updateRevisionFilterLabel(val) {
        let label;
        [_, label] = value_to_month(val);
        $('.revision-date').html(label);
    }


    // <-- Apply advanced filters -->

    // object that remember filters states
    filters = {
        "search": $('#search').val(),
        "author": '',
        "tag": '',
        "rating": $('.notation-filter-slider').slider('value'),
        "certification": $('.certification-filter-slider').slider('value'),
        "revision": value_to_month($('.certification-filter-slider').slider('value'))[0],
    }

    selectizeAuthor.setValue('');
    selectizeTag.setValue('');


    function applyFilter(changed, value) {

        filters[changed] = value;

        sort((pluginBox) => {
            let pluginRating = pluginBox.find('.pluginRating').data('rating') || 0;
            let pluginCertification = pluginBox.find('.certification').data('certification');
            let pluginAuthors = pluginBox.data('author').split(', ');
            let pluginName = pluginBox.data('name').toUpperCase();
            let pluginTags = pluginBox.data('tags').split(', ');
            let pluginRevisionOld = monthDiff(new Date(pluginBox.data('revision')*1000), new Date()); // number of months between the last revision date and now
            
            return (pluginRating >= filters.rating)
                && (pluginCertification >= filters.certification)
                && (filters.search === '' || pluginName.indexOf(filters.search) != -1)
                && (filters.author === '' || pluginAuthors.includes(filters.author))
                && (filters.tag === '' || pluginTags.includes(filters.tag))
                && pluginRevisionOld <= filters['revision'];
        })
    }

    // Display or not plugin with a function handler
    function sort(sortFunction) {
        $('.pluginBox').each((i, el) => {
            if (sortFunction($(el))) {
                $(el).show();
            } else {
                $(el).hide();
            }
        })
    }

    function clearSort() {
        $('.pluginBox').show();
    }

    // Crop the names of plugins if there are too long
    $('.pluginName span').each((i,el) => {
        let name = $(el)
        if (name.html().length > 30) {
            name.html(name.html().slice(0,30) + '...');
        }
    })

    $('#showBetaTestPlugin').on('change', (e) => {

        $('.beta-test-plugin-switch .slider').addClass('loading');

        let queryParams = new URLSearchParams(window.location.search);

        queryParams.set("beta-test", e.currentTarget.checked.toString());

        history.replaceState(null, null, "?" + queryParams.toString());

        window.location.reload(true);
    })

});