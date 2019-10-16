<?php
define( "PCLERROR_LIB", 1 );
define( 'PCLZIP_READ_BLOCK_SIZE', 2048 );
define( 'PCLZIP_SEPARATOR', ',' );
define( 'PCLZIP_ERROR_EXTERNAL', 0 );
define( 'PCLZIP_TEMPORARY_DIR', '' );
define( 'PCLZIP_TEMPORARY_FILE_RATIO', 0.47 );
define( 'PCLZIP_ERR_USER_ABORTED', 2 );
define( 'PCLZIP_ERR_NO_ERROR', 0 );
define( 'PCLZIP_ERR_WRITE_OPEN_FAIL', -1 );
define( 'PCLZIP_ERR_READ_OPEN_FAIL', -2 );
define( 'PCLZIP_ERR_INVALID_PARAMETER', -3 );
define( 'PCLZIP_ERR_MISSING_FILE', -4 );
define( 'PCLZIP_ERR_FILENAME_TOO_LONG', -5 );
define( 'PCLZIP_ERR_INVALID_ZIP', -6 );
define( 'PCLZIP_ERR_BAD_EXTRACTED_FILE', -7 );
define( 'PCLZIP_ERR_DIR_CREATE_FAIL', -8 );
define( 'PCLZIP_ERR_BAD_EXTENSION', -9 );
define( 'PCLZIP_ERR_BAD_FORMAT', -10 );
define( 'PCLZIP_ERR_DELETE_FILE_FAIL', -11 );
define( 'PCLZIP_ERR_RENAME_FILE_FAIL', -12 );
define( 'PCLZIP_ERR_BAD_CHECKSUM', -13 );
define( 'PCLZIP_ERR_INVALID_ARCHIVE_ZIP', -14 );
define( 'PCLZIP_ERR_MISSING_OPTION_VALUE', -15 );
define( 'PCLZIP_ERR_INVALID_OPTION_VALUE', -16 );
define( 'PCLZIP_ERR_ALREADY_A_DIRECTORY', -17 );
define( 'PCLZIP_ERR_UNSUPPORTED_COMPRESSION', -18 );
define( 'PCLZIP_ERR_UNSUPPORTED_ENCRYPTION', -19 );
define( 'PCLZIP_ERR_INVALID_ATTRIBUTE_VALUE', -20 );
define( 'PCLZIP_ERR_DIRECTORY_RESTRICTION', -21 );
define( 'PCLZIP_OPT_PATH', 77001 );
define( 'PCLZIP_OPT_ADD_PATH', 77002 );
define( 'PCLZIP_OPT_REMOVE_PATH', 77003 );
define( 'PCLZIP_OPT_REMOVE_ALL_PATH', 77004 );
define( 'PCLZIP_OPT_SET_CHMOD', 77005 );
define( 'PCLZIP_OPT_EXTRACT_AS_STRING', 77006 );
define( 'PCLZIP_OPT_NO_COMPRESSION', 77007 );
define( 'PCLZIP_OPT_BY_NAME', 77008 );
define( 'PCLZIP_OPT_BY_INDEX', 77009 );
define( 'PCLZIP_OPT_BY_EREG', 77010 );
define( 'PCLZIP_OPT_BY_PREG', 77011 );
define( 'PCLZIP_OPT_COMMENT', 77012 );
define( 'PCLZIP_OPT_ADD_COMMENT', 77013 );
define( 'PCLZIP_OPT_PREPEND_COMMENT', 77014 );
define( 'PCLZIP_OPT_EXTRACT_IN_OUTPUT', 77015 );
define( 'PCLZIP_OPT_REPLACE_NEWER', 77016 );
define( 'PCLZIP_OPT_STOP_ON_ERROR', 77017 );
define( 'PCLZIP_OPT_EXTRACT_DIR_RESTRICTION', 77019 );
define( 'PCLZIP_OPT_TEMP_FILE_THRESHOLD', 77020 );
define( 'PCLZIP_OPT_ADD_TEMP_FILE_THRESHOLD', 77020 ); // alias
define( 'PCLZIP_OPT_TEMP_FILE_ON', 77021 );
define( 'PCLZIP_OPT_ADD_TEMP_FILE_ON', 77021 ); // alias
define( 'PCLZIP_OPT_TEMP_FILE_OFF', 77022 );
define( 'PCLZIP_OPT_ADD_TEMP_FILE_OFF', 77022 ); // alias
define( 'PCLZIP_ATT_FILE_NAME', 79001 );
define( 'PCLZIP_ATT_FILE_NEW_SHORT_NAME', 79002 );
define( 'PCLZIP_ATT_FILE_NEW_FULL_NAME', 79003 );
define( 'PCLZIP_ATT_FILE_MTIME', 79004 );
define( 'PCLZIP_ATT_FILE_CONTENT', 79005 );
define( 'PCLZIP_ATT_FILE_COMMENT', 79006 );
define( 'PCLZIP_CB_PRE_EXTRACT', 78001 );
define( 'PCLZIP_CB_POST_EXTRACT', 78002 );
define( 'PCLZIP_CB_PRE_ADD', 78003 );
define( 'PCLZIP_CB_POST_ADD', 78004 );

function PclErrorLog(int $p_error_code=0, string $p_error_string="") { }
function PclErrorFatal(string $p_file, int $p_line, string $p_error_string="") { }
function PclErrorReset() { }
function PclErrorCode():int { return 1; }
function PclErrorString():string { return ''; }

class PclZip {
    function __construct(string $p_zipname) { }
    /** @return array|int */
    function create(array $p_filelist) { return 0; }
    /** @return array|int */
    function add(array $p_filelist) { return 0; }
    /** @return array|int */
    function listContent() { return 0; }
    /** @return array|int */
    function extract(...$args) { return 0; }
    /**
     * @param int|int[] $p_index
     * @return array|int
     **/
    function extractByIndex($p_index) { return 0; }
    /** @return array|int */
    function delete() { return 0; }
    /**
     * @param int|int[] $p_index
     * @return array|int
     **/
    function deleteByIndex($p_index) { return 0; }
    /** @return array|int */
    function properties() { return 0; }
    /**
     * @param string|PclZip $p_archive
     * @return array|int
     **/
    function duplicate($p_archive) { return 0; }
    /**
     * @param string|PclZip $p_archive_to_add
     * @return array|int
     **/
    function merge($p_archive_to_add) { return 0; }
    function errorCode():int { return 0; }
    function errorName(bool $p_with_code=false):string { return ''; }
    function errorInfo(bool $p_full=false):string { return ''; }
}
