<?php 
/*
Plugin Name: Send Email
Plugin URI: blog.zhfsky.com/sendmail
Description: 发送邮件
Author: Naihai
Author URI:www.zhfsky.com
Version: 0.0.1

*/
 




 //保存数据
if(isset($_POST['option_save'])) {
		//至少填写一个
		if( (empty($_POST['send_email_subject']) && empty($_POST['send_email_to']) && empty($_POST['send_email_message']) ) ){ 
			
			function option_save_error(){echo '<div class="notice notice-error is-dismissible"><p>抱歉,不能全为空！</p></div>';}
			add_action( 'admin_notices', 'option_save_error' ); 

		}
		
		else{
								    //处理数据   
    $option1 = stripslashes($_POST['send_email_subject']); 
    $option2 = stripslashes($_POST['send_email_to']);   
    $option3 = stripslashes($_POST['send_email_message']);     
    update_option('send_email_subject', $option1);//更新选项 
    update_option('send_email_to', $option2);//更新选项 
    update_option('send_email_message', $option3);//更新选项  
    function option_save_success(){	echo '<div class="is-dismissible  notice notice-success "><p>保存成功！</p></div>';} 
    add_action( 'admin_notices', 'option_save_success' );
			}
	
}
 
/*
	@其他页面调用send email  使用 参数 address= 进行传递

	@使用urlencode()进行邮箱地址转换，该插件使用urldecode取回原字符串
	@获取原邮箱地址后直接显示
*/
//function get_email_address($show_send_email_to){
//		$email_address_query =$_GET['address'];
//		$show_send_email_to =	urldecode($email_address_query);
//		return $show_send_email_to;
//	
//	}



function send_email_menu(){      
    add_menu_page( '发送邮件', '发送邮件', 'read_private_pages', 'send_email','send_naihai_wp_mail_smtp_options_page','dashicons-email',86);      
}     
add_action('admin_menu', 'send_email_menu');   


// Define the function
function send_naihai_wp_mail_smtp_options_page() {
	
	// Load the options
	global  $phpmailer;
	

	 
	if (isset($_POST['send_email_action']) && isset($_POST['send_email_subject']) && isset($_POST['send_email_message']) && isset($_POST['send_email_to'])) {
		
		
    $to = stripslashes($_POST['send_email_to']); 
    $subject = stripslashes($_POST['send_email_subject']);   
    $message = stripslashes($_POST['send_email_message']); 
		$headers = "Content-Type: text/html; charset=\"".get_option('blog_charset')."\"\n";
		
		$result =wp_mail($to, $subject, $message, $headers);
		if($result){echo '<div class="notice notice-success is-dismissible "><p>发送成功！</p></div>';}
			else {echo '<div class="notice notice-error is-dismissible "><p>发送失败！</p></div>';}
		
	

	} //end if 
	
   global $show_draft_tag,$show_send_email_to, $show_send_email_subject, $show_send_email_message;
	if(isset($_POST['show_draft']) && $_POST['show_draft'] =='显示上次保存内容'){ $show_draft_tag = true;} 
			elseif(isset($_POST['show_draft']) && $_POST['show_draft'] =='不显示上次保存内容'){$show_draft_tag = false;}
			
	if($show_draft_tag){ 
			$show_draft_button_name = '不显示上次保存内容' ;
			$show_send_email_to = get_option('send_email_to');
			$show_send_email_subject = get_option('send_email_subject');
			$show_send_email_message = get_option('send_email_message');
		 
		} 
	elseif(!empty($_GET['address']) || !empty($_GET['info'])) {
		  if(!empty($_GET['address']))$show_send_email_to =	urldecode($_GET['address']);
	  	if(!empty($_GET['info']))	$show_send_email_message = urldecode($_GET['info']);
			$show_draft_button_name = '显示上次保存内容' ;
			}	
	else{
		
			$show_send_email_to = '';
			$show_send_email_subject = '';
			$show_send_email_message = '';
			$show_draft_button_name = '显示上次保存内容' ;
				
				}
		
		
 
	
 unset($phpmailer);
	?>
	
	
	
	
<div class="wrap">

<h1>发送邮件</h1>

		<form method="POST">
			<table class="optiontable form-table">
				<tr valign="top">
					<th scope="row"><label for="subject">主题</label></th>
					<td><input name="send_email_subject" type="text" id="send_email_subject" value="<?php echo $show_send_email_subject;?>" size="40" class="code" />
					<span class="description">请输入发信主题</span>&nbsp;<span style="color:green;">请慎重使用,发送邮箱为官网邮箱:web@hustca.com</span></td>
				</tr>	
					<tr valign="top">
					<th scope="row"><label for="to">收件地址</label></th>
					<td><input name="send_email_to" type="text" id="send_email_to" value="<?php echo $show_send_email_to;?>" size="40" class="code" />
					<span class="description">请输入收件人地址 &nbsp;发送多个请以逗号隔开  </span>[ <span style="color:green;">英文半角 , </span>]</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="send_email_message">内容</label></th>
					<td>
							<?php wp_editor( $show_send_email_message, 'send_email_message',array('teeny' => true ));  ?>	
							 
					</td>
				</tr>
			</table>
		<p class="submit"><input type="submit" name="send_email_action" id="send_email_action" class="button-primary" value="发送邮件" /></p>
		<p><input type="submit" name="option_save" class="button-primary"  id="submit"  value="保存" /></p>
		<p><input type="submit" name="show_draft" class="button-primary"  id="submit"  value="<?php echo $show_draft_button_name;?>" /></p>
		</form>

</div>



<?php
	
} // End of send_naihai_wp_mail_smtp_options_page() function definition



 


?>