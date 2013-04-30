<?php
/**
 * nginx upload handler- /uploadHandler.php
 *
 * This file is called from nginx (upload_pass) after finishing uploading the
 * file
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 * @see http://wiki.nginx.org/HttpUploadModule
 * @see http://www.grid.net.ru/nginx/resumable_uploads.en.html
 * @return json with file_name, file_content_type, file_path, file_size
 */

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: application/json; charset=UTF-8');
exit(json_encode($_REQUEST));
