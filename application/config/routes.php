<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';

$route['tags'] = "tags/add";
$route['tags/(:num)'] = "tags/index/$1";

$route['users'] = "users/index";
$route['users/(:num)'] = "users/info/$1";
$route['users/(:num)/(:any)'] = "users/$2/$1";

$route['events'] = "events/index";
$route['events/(:num)'] = "events/info/$1";
$route['events/(:num)/(:any)'] = "events/$2/$1";
$route['events/(:num)/(:any)/(:num)'] = "events/$2/$1/$3";

/*
/events/(id)/logo						POST		上传活动logo（仅允许组织活动的用户提交）
/events/(id)/agenda						GET			获取活动议程（对组织活动的用户显示编辑入口）
/events/(id)/agenda						POST		修改活动议程（仅允许组织活动的用户提交）
/events/(id)/organizers					GET			获取活动组织者列表（对组织活动的用户显示编辑入口）
/events/(id)/organizers					POST		添加活动组织者（仅允许组织活动的用户提交）
/events/(id)/organizers/(id)			POST		移除一个活动组织者（仅允许组织活动的用户提交）
/events/(id)/attendees					GET			获取报名成功的参加者列表（对组织活动的用户显示等待审核列表和编辑入口）
/events/(id)/attendees					POST		向一个非活动组织者的用户发出邀请（仅允许组织活动的用户提交）
/events/(id)/attendees/(id)				POST		审核用户报名申请（仅允许组织活动的用户提交）
/events/(id)/attendees/(id)				POST		报名/取消报名/接受邀请/拒绝邀请（仅允许非活动组织者的用户提交）
/events/(id)/watchers					GET			获取活动关注者列表
/events/(id)/watchers					POST		关注该活动
/events/(id)/watchers/(id)				POST		取消关注（仅允许传入登录用户自己的id）
/events/(id)/tags						GET			获取活动标签列表（对组织活动的用户显示编辑入口）
/events/(id)/tags						POST		增加一个标签（仅允许组织活动的用户提交）
/events/(id)/tags/(id)					POST		移除一个标签（仅允许组织活动的用户提交）
*/


/* End of file routes.php */
/* Location: ./application/config/routes.php */