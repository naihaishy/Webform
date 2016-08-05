<?php 



/*
	@引入表单文件
  @webapply.php是网站管理招募报名表单
  @applyit.php 是技术部申请表单
  @staff.php 是部员申请表单 
*/
require_once('webapply.php');
require_once('applyit.php');
require_once('staff.php');



/*
	@引入邮件发送文件
*/
require_once('send-email.php');





//重写排版
//add_action('admin_head', 'rebulid_list_table_css');
//	function rebulid_list_table_css() {
//	    echo '<style type="text/css">';
//	    echo '.column-cb		  { width:5% ; overflow:hidden }';
//	    echo '.column-name 	  { width:10%; overflow:hidden }';
//	    echo '.column-sex 	  { width:3% ; overflow:hidden }';
//	    echo '.column-tel 	 { width:15% ; overflow:hidden }';
//	    echo '.column-email  { width:25% ; overflow:hidden }';
//	    echo '.column-groups { width:12% ; overflow:hidden }';
//	    echo '.column-major  { width:30% ; overflow:hidden }';
//	    echo '.column-applytime { width:12%; overflow:hidden }';
//	    echo '</style>';
//	}
	


