{include file='include/colorbox.inc.tpl'}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}
{combine_css path="admin/themes/default/fontello/css/animation.css" order=10} {* order 10 is required, see issue 1080 *}
{footer_script}
{* <!-- USERS --> *}
var usersCache = new UsersCache({
  serverKey: '{$CACHE_KEYS.users}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});
const nb_users = {$nb_users};

const additional_filt_type = '{$ADDITIONAL_FILT.type}';
const additional_filt_value = {if $ADDITIONAL_FILT.type} {$ADDITIONAL_FILT.value} {else} null {/if};

const color_icons = ["icon-red", "icon-blue", "icon-yellow", "icon-purple", "icon-green"];
var activity_page = 1;
let current_page_offset = 0;
let page_offsets = [0];
let actual_page = 1;
let end_page = false;
let uid_filter;
let action_filter;
let object_filter;
let date_min_filter = '{$ACTIVITY_DATES.min}';
let date_max_filter = '{$ACTIVITY_DATES.max}';

const date_min = '{$ACTIVITY_DATES.min}';
const date_max = '{$ACTIVITY_DATES.max}';

const page_ellipsis = '<span>...</span>'
const page_item = '<a data-page="%d">%d</a>';
var create_selecter = true;
const users_key = "{"Users"|@translate}";

const line_key = "{'%s line'|translate}";
const lines_key = "{'%s lines'|translate}";

{*<-- Translation keys -->*}

var actionType_add = "{'add'|translate}";
var actionType_delete = "{'deletion'|translate}";
var actionType_move = "{'move'|translate}";
var actionType_edit = "{'edit'|translate}";
var actionType_login = "{'login'|translate}";
var actionType_logout = "{'logout'|translate}";

{* Album keys *}

var actionInfos_album_added = "{'%d album added'|translate}";
var actionInfos_album_deleted = "{'%d album deleted'|translate}";
var actionInfos_album_edited = "{'%d album edited'|translate}";
var actionInfos_album_moved = "{'%d album moved'|translate}";

var actionInfos_albums_added = "{'%d albums added'|translate}";
var actionInfos_albums_deleted = "{'%d albums deleted'|translate}";
var actionInfos_albums_edited = "{'%d albums edited'|translate}";
var actionInfos_albums_moved = "{'%d albums moved'|translate}";

{* User keys *}

var actionInfos_user_added = "{'%d user added'|translate}";
var actionInfos_user_deleted = "{'%d user deleted'|translate}";
var actionInfos_user_edited = "{'%d user edited'|translate}";
var actionInfos_user_logged_in = "{'%d user logged in'|translate}";
var actionInfos_user_logged_out = "{'%d user logged out'|translate}";

var actionInfos_users_added = "{'%d users added'|translate}";
var actionInfos_users_deleted = "{'%d users deleted'|translate}";
var actionInfos_users_edited = "{'%d users edited'|translate}";
var actionInfos_users_logged_in = "{'%d users logged in'|translate}";
var actionInfos_users_logged_out = "{'%d users logged out'|translate}";

{* Photo keys *}

var actionInfos_photo_added = "{'%d photo added'|translate}";
var actionInfos_photo_deleted = "{'%d photo deleted'|translate}";
var actionInfos_photo_edited = "{'%d photo edited'|translate}";
var actionInfos_photo_moved = "{'%d photo moved'|translate}";

var actionInfos_photos_added = "{'%d photos added'|translate}";
var actionInfos_photos_deleted = "{'%d photos deleted'|translate}";
var actionInfos_photos_edited = "{'%d photos edited'|translate}";
var actionInfos_photos_moved = "{'%d photos moved'|translate}";

{* Group keys *}

var actionInfos_group_added = "{'%d group added'|translate}";
var actionInfos_group_deleted = "{'%d group deleted'|translate}";
var actionInfos_group_edited = "{'%d group edited'|translate}";
var actionInfos_group_moved = "{'%d group moved'|translate}";

var actionInfos_groups_added = "{'%d groups added'|translate}";
var actionInfos_groups_deleted = "{'%d groups deleted'|translate}";
var actionInfos_groups_edited = "{'%d groups edited'|translate}";
var actionInfos_groups_moved = "{'%d groups moved'|translate}";

{* Tags keys *}

var actionInfos_tag_added = "{'%d tag added'|translate}";
var actionInfos_tag_deleted = "{'%d tag deleted'|translate}";
var actionInfos_tag_edited = "{'%d tag edited'|translate}";
var actionInfos_tag_moved = "{'%d tag moved'|translate}";

var actionInfos_tags_added = "{'%d tags added'|translate}";
var actionInfos_tags_deleted = "{'%d tags deleted'|translate}";
var actionInfos_tags_edited = "{'%d tags edited'|translate}";
var actionInfos_tags_moved = "{'%d tags moved'|translate}";

{/footer_script}

{combine_script id='user_activity' load='async' require='jquery' path='admin/themes/default/js/user_activity.js'}
<div class="container"> 
    <div>
        <div class="activity-header">
            <div class="user_activity_end_options">
                <a class="download_csv tiptip" title="{'Download all activities'|translate}" href="admin.php?page=user_activity&type=download_logs"> 
                    <i class="icon-download"> </i>
                </a>
                <div id="activityMoreFilters" class="activity-more-filters">
                    <span class="icon-filter"></span>{'Filters'|@translate}
                </div>
            </div>
        </div>
        <div id="activityMoreFiltersContent" class="activity-more-filters-content">
            <div class="activity-select">
                <span class="activity-select"> {'User'|translate} </span>
            
                <select class="user-selecter" placeholder="---" single>
                    <option value="none">
                        <span class='username_filter'>---</span>
                    </option>
                    {foreach from=$ulist item=user}
                        <option value="{$user.id}">
                            <span class='username_filter'>{$user.username}</span>
                            <span class='nb_lines_str'>
                                {'(%d)'|translate:$user.nb_lines}
                            </span>
                        </option>
                    {/foreach}
                </select>
            </div>

            <div class="activity-select">
                <span class="activity-select"> {'Action'|translate} </span>
            
                <select class="action-selecter" placeholder="---" single>
                    <option value="none">
                        <span class='action_filter'>---</span>
                    </option>
                    {foreach from=$ACTIONS item=action}
                        <option value="{$action.value}">
                            <span class='action_filter'>
                                {ucfirst($action.object)|translate}
                                /
                                {if $action.action == 'delete'}
                                    {'deletion'|translate : $action.object}
                                {else}
                                    {$action.action|translate}
                                {/if}
                                {' (%d)'|translate : $action.counter}
                            </span>
                        </option>
                    {/foreach}
                </select>
            </div>
            
            <div class="activity-select">
                <span class="activity-select">{'Start-Date'|translate}</span>
                <input 
                    class="activity-date-selecter"
                    type="date"
                    id="date_min_activity"
                    value="{$ACTIVITY_DATES.min}"
                    min="{$ACTIVITY_DATES.min}"
                    max="{$ACTIVITY_DATES.max}"
                />
            </div>

            <div class="activity-select">
                <span class="activity-select">{'End-Date'|translate}</span>
                <input 
                    class="activity-date-selecter"
                    type="date"
                    id="date_max_activity"
                    value="{$ACTIVITY_DATES.max}"
                    min="{$ACTIVITY_DATES.min}"
                    max="{$ACTIVITY_DATES.max}"
                />
            </div>

            {if $ADDITIONAL_FILT.type}
            <div class="additional-filters-section">
                <div class="additional-filters-info">
                    {'Additional filters'|translate}
                </div>
                <div class="additional-filters">
                    <div class="activity-filter-container">
                    {if $ADDITIONAL_FILT.type == 'photo'}
                        <span class="icon-picture">{$ADDITIONAL_FILT.name}</span>
                    {else if $ADDITIONAL_FILT.type == 'album'}
                        <span class="icon-folder-open">{$ADDITIONAL_FILT.name}</span>
                    {else}
                        <span class="icon-group">{$ADDITIONAL_FILT.name}</span>
                    {/if}
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>

  <div class="pagination-container">
      <div class="pagination-arrow left">
        <span class="icon-left-open"></span>
      </div>
      <div class="pagination-item-container">
      </div>
      <div class="user-update-spinner icon-spin6 animate-spin"></div>
      <div class="pagination-arrow rigth">
        <span class="icon-left-open"></span>
      </div>
    </div>

    <div class="activity-noresult">
        {'No results'|translate}
    </div>

    

    <div class="tab-title">
        <div class="action-title">
            {'Action'|translate}
        </div>

        <div class="date-title">
            {'Date'|translate}
        </div>

        <div class="user-title">
            {'User'|translate}
        </div>

        <div class="detail-title">
            {'Details'|translate}
        </div>
    </div>


    <div class="tab">
    <div class="loading"> 
        <span class="icon-spin6 animate-spin"> </span>
    </div>
        <div class="line hide" id="-1">
            <div class="action-section">
                <div class="action-type">
                    <span class="action-icon"></span>
                    <span class="action-name"> Edit </span>
                </div>
                <div class="action-infos">
                    <span class="action-infos-test"> T </span>
                </div>
            </div>

            <div class="date-section">
                <span class="icon-clock"> </span>
                <span class="date-day">1 Janvier 1970</span>
                <span class="date-hour">a 00:00</span>
            </div> 

            <div class="user-section">
                <div class="user-pic">
                </div>
                <div class="user-name">
                    Username
                </div>
            </div>

            <div class="detail-section">
                <div class="detail-item detail-item-1">
                    detail 1
                </div>
                <div class=" detail-item detail-item-2">
                    detail 2
                </div>
                <div class="detail-item detail-item-3">
                    detail 3
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.container {
    padding: 0 30px;
}

.container,
.tab {
    display: flex;
    flex-direction: column;
}

.tab-title {
    display: flex;
    flex-direction: row;
}

.hide {
    display: none !important;
}

.tab-title div {
    text-align: left;
    font-size: 1.1em;
    font-weight: bold;

    margin: 0 20px 10px 0px;

    color: #9e9e9e;

    padding-bottom: 5px;
}

.tab-title div:first-child {
    margin: 0 0 10px 35px;
}

.tab-title .action-title, 
.line .action-section {
    min-width: 320px;
    max-width: 340px;
}
.tab-title .action-title {
    min-width: 298px !important;
}

.tab-title .date-title, 
.line .date-section {
    min-width: 280px;
    max-width: 300px;
}

.tab-title .user-title, 
.line .user-section {
    min-width: 200px;
    max-width: 250px;
}


.line .action-section,
.line .date-section,
.line .user-section,
.tab-title .action-title,
.tab-title .date-title,
.tab-title .user-title {
    text-align: left;
    {* width: 22%; *}
}

.line .action-section,
.line .date-section,
.line .user-section {
    margin: 0 20px 0 0;
}

.line .detail-section,
.tab-title .detail-title {
    display: flex;
    flex-grow: 1;
    margin-right: 0;
}

.action-section {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.action-type {
    margin: 0 10px 0 15px;
    padding: 3px 10px;
    border-radius: 20px;

    white-space: nowrap;
}

.action-infos {
    display: flex;
    flex-direction: row;
}

.action-infos span {
    margin-right: 5px;
}

.date-section .date-day {
    font-weight: bold;
}

.user-section {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.user-section .user-pic {
    width: 30px;
    height: 30px;

    min-width: 30px;

    border-radius: 50%;

    margin-right: 10px;

    display: flex;

    justify-content: center;
    align-items: center;

    font-weight: 600;
    font-size: 17px;
}

.user-section .user-name {
    font-weight: bold;
}

/* Activity Header */

.activity-header {
    display: flex;
    flex-direction: row;
    width: 100%;
}

div:has(> .activity-header) {
    margin-bottom: 38px;
}

.activity-select span {
    font-size: 15px;
    font-weight: bold;
}

.user-selecter, .action-selecter {
    width: 230px;
    margin-top: 10px;
}

.actions-filters{
    margin-left: 25%;
}

.user_activity_end_options{
    margin-left: auto;
    display: flex;
}

.activity-noresult{
    opacity: 0.3;
    text-align: center;
    font-weight: bold;
    font-size: 32px;
    display: none;
}

.activity-more-filters{
    margin-left: 14px;
    justify-content: center;
    cursor: pointer;
    padding: 10px;
    text-align: center;
    font-weight: bold;
    width:70px;
}

.activity-more-filters.extend-padding{
    padding-bottom: 10px;
}

.activity-more-filters, .activity-more-filters-content{
    background-color: #F3F3F3;
}

.activity-more-filters-content{
    display: flex;
    position: relative;
    flex-direction: row;
    font-weight: normal;
    padding : 23px 0px 22px 24px;
    width: auto;
}

.activity-period-info{
    margin-bottom : 30px;
    font-weight: bold;
}

.additional-filters-section{
    margin-left: 5%;
}

.additional-filters-info{
    margin-bottom : 18px;
    font-weight: bold;
}

.additional-filters{
    display: flex;
}

.activity-filter-container span::before{
    margin-right: 6px;
}

.activity-filter-container .icon-cancel{
    margin-left: 5px;
}

.activity-date-selecter{
    display: block;
    height: 25.5px;
    width: 130px;
    margin-top: 10px;
    font-size: 12px;
    font-weight: bold;
}

/* Selectize */
.selectize-control.single.user-selecter, .selectize-control.single.action-selecter {
    height: 30px;
}

.selectize-control.single .selectize-input {
    height: 30px;
    padding: 0 10px;

    display: flex;
    align-items: center;
    justify-content: left;
}

.selectize-input {
    text-align: left;
}

.selectize-control.single .selectize-input input{
    height: 30px;
}

.selectize-dropdown {
    text-align: left;
}

.cancel-icon {
    margin: 0 0 0 10px !important;

    cursor: pointer;
}

.loading {
    font-size: 25px;
}

.action-section::before {
    margin: 0 -5px 0 10px;
    opacity: 0.6;
}
</style>