{if $isWebmaster == 1}
    {combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
    {combine_script id='sys' load='footer' path='admin/themes/default/js/maintenance_sys.js'}
    <fieldset id="activities-system">
        <div class="tab-header">
            <div class="tab-object">
                <p>{'Object'|translate}</p>
            </div>
            <div class="tab-action">
                <p>{'Action'|translate}</p>
            </div>
            <div class="tab-users">
                <p>{'Users'|translate}</p>
            </div>
            <div class="tab-date">
                <p>{'Date'|translate}</p>
            </div>
            <div class="tab-details">
                <p>{'Details'|translate}</p>
            </div>
        </div>

        <div class="loading">
            <span class="icon-spin6 animate-spin"></span>
        </div>

        <div class="tab-body line" id="body_example">
            <div class="tab-body-object">
                <p>
                    <span class="icon_object"> </span>
                    <span class="text_object">Object</span>
                </p>
            </div>
            <div class="tab-body-action">
                <p class="color_action">
                    <span class="icon_action"> </span>
                    <span class="text_action">Action</span>
                </p>
            </div>
            <div class="tab-body-users">
                <p>
                    <span class="icon_user"></span>
                    <span class="text_username">Username</span>
                </p>
            </div>
            <div class="tab-body-date">
                <p>
                    <span class="icon icon-clock"></span>
                    <span class="text_date">Date</span>
                    <span class="text_hour">Hour</span>
                </p>
            </div>
            <div class="tab-body-details">
                <p class="detail-item" id="line_details_example">
                    <span class="icon_details"></span>
                    <span class="text_details">Details</span>
                </p>
            </div>
        </div>

        <div id="tab-body-content"></div>

    </fieldset>
    <style>
        #body_example {
            display: none;
        }

        #line_details_example {
            display: none;
        }

        .icon-th-list {
            padding: 0;
            font-size: unset;
        }

        .loading {
            font-size: 40px;
        }

        .major-infos {
            position: relative;
        }

        .major-infos::before {
            content: '';
            display: block;
            width: 8px;
            height: 100%;
            background-color: #F4AB4F;
            position: absolute;
            left: -8px;
        }

        .tab-header,
        .tab-body {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr 1.2fr 2fr;
            text-align: start;
        }

        .tab-body-object,
        .tab-body-users,
        .tab-body-date {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-right: 5px;
        }

        .tab-body-object p,
        .tab-body-users p,
        .tab-body-date p {
            display: inline;
        }

        .tab-body-action,
        .tab-body-details {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-right: 5px;
            padding: 10px;
        }

        .tab-header p {
            color: #9e9e9e;
            font-size: 1.1em;
            font-weight: bold;
        }

        .tab-body {
            min-height: 40px;
            align-content: center;
            align-items: center;
            margin-bottom: 10px;
        }

        .tab-body p {
            font-weight: bold;
            margin: 0;
        }

        .tab-body-object .icon_object {
            margin-left: 10px;
            margin-right: 0.4em;
        }

        .tab-body-action .color_action {
            display: inline;
            border-radius: 23px;
            padding: 5px 15px 5px 7px;
        }

        .tab-body-action .icon_action {
            margin-right: 0.3em;
        }

        .tab-body-users p {
            display: flex;
            align-items: center;
        }

        .tab-body-users .icon_user {
            width: 30px;
            height: 30px;
            min-width: 30px;
            border-radius: 50%;
            margin-right: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            font-size: 17px;
        }

        .tab-body-date .icon {
            font-weight: bold;
        }

        .tab-body-date .text_hour {
            font-weight: normal;
        }

        .tab-body-details p {
            display: inline;
            align-items: center;
            border-radius: 3px !important;
            padding: 4px !important;
            padding-right: 8px !important;
            margin-right: 5px !important;
            font-weight: normal;
        }
        @media (min-width: 1350px) {
            .tab-header,
            .tab-body {
            grid-template-columns: 1fr 1fr 1fr 1.7fr 2fr;
            }
        }
        @media (min-width: 1650px) {
            .tab-header,
            .tab-body {
            grid-template-columns: 1fr 1.2fr 1fr 1.6fr 3fr;
            }
        }
    </style>
{/if}