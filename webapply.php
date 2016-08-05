<?php

/*

Description: 官网所有表单数据--webapply
Version: 1.0
Author: naihai
Author URI:  http://www.zhfsky.com
*/

//require_once('webform.php');

class WP_Customers_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'webapply', 'hustca' ), //singular name of the listed records
			'plural'   => __( 'webapplys', 'hustca' ), //plural name of the listed records
			'ajax'     => false  //does this table support ajax?
				)
		 );
	}


	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM hustca_webapply";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;
if(current_user_can('manage_options')){

		$wpdb->delete(
			"hustca_webapply",	array( 'ID' =>  $id ), array( '%d' )
		);
		self::delete_message_success_ifo();
	}else self::delete_message_no_perssion_ifo();
	} 


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM hustca_webapply";

		return $wpdb->get_var( $sql );
	}


 

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'sex': if($item['sex']=='female')  return $item['sex'] ='女'; else return $item['sex']='男';
			case 'tel': 
			case 'email':
			case 'it_group':
			case 'major':
			case 'applytime':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-email[]" value="%s" />', $item['email']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions =array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">删除</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		);

		return $title . $this->row_actions( $actions );
	}

			/**
	 * Send email using smtp
	 *
	 * @send email plugin author:naihai 
	 *
	 * @return string
	 */
		function column_email( $item ) {

		//$email_nonce = wp_create_nonce( 'sp_delete_customer' );

		$email = '<strong>' . $item['email'] . '</strong>';
		$nh ="\n";
		$actions = array(
			'email' => sprintf( '<a href="?page=%s&action=%s&address=%s&info=%s">发送邮件</a>','send_email', 'mail', 
			urlencode( $item['email'] ), urlencode('姓名:'.$item['name'].$nh.'手机号码:'.$item['tel'].$nh.'专业:'.$item['major']))
		);

		return $email . $this->row_actions( $actions );
	}
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'name'    => __( '姓名', 'hustca' ),
			'sex' => __( '性别', 'hustca' ),
			'tel'    => __( '手机号码', 'hustca' ),
			'email' =>__( '邮箱', 'hustca' ),
			'it_group' =>__( '组别', 'hustca' ),
			'major' =>__( '专业', 'hustca' ),
			'applytime' =>__( '报名日期', 'hustca' )
		);

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'tel' => array( 'tel', false ),
			'sex' => array( 'sex', true ),
			'applytime' => array( 'applytime', false )
		);
		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions =array(
			'bulk-email' => '群发邮件'
		);
		return $actions;
	}
		 
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
				));

		$this->items = self::get_customers( $per_page, $current_page );
	}
	
		//删除消息提醒 成功删除
	public function delete_message_success_ifo(){

			$out='';
			$out .='<div class="notice notice-success is-dismissible"><p>成功删除!</p><br/><p><a href="http://www.hustca.com/news/wp-admin/admin.php?page=hustca_form">返回表单页面</a></p></div>';
			$out .='<div class="notice notice-warning is-dismissible"><p>请慎重操作，删除不可挽回！</p>	</div>';
			echo $out; 
		 
		}
		//删除消息提醒 删除失败 或者无权限
	public function delete_message_no_perssion_ifo(){

			$outl='';
			$outl .='<div class="notice notice-error is-dismissible"><p>对不起，你没有权限删除！!</p></div>';
			$outl .='<div class="notice is-dismissible"><p><a href="http://www.hustca.com/news/wp-admin/admin.php?page=hustca_form">返回表单页面</a></p></div>';
			$outl .='<div class="notice notice-warning is-dismissible"><p>请慎重操作，删除不可挽回！</p></div>';
			echo $outl; 
		 
		}
			
			
	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( '删除失败' );
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );
				
				
		            // wp_redirect( esc_url_raw(add_query_arg(array('page' => 'hustca_webapply'), 'http://www.hustca.com/news/wp-admin/admin.php')) );
				exit;
			}

		}

 
 	 // If the  email bulk action is triggered
		if  ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-email' )   
		 {

			$email_bluks = $_POST['bulk-email'] ;
			$emails = implode(',',$email_bluks);
			$base = admin_url().'/admin.php';
			$area_params['page'] ='send_email';
			$area_params['action'] ='email';
  		$area_params['address'] =$emails;
  		$area_params['info'] ='已经自动填充到收件人的邮箱地址：'.$emails;
	    wp_redirect(add_query_arg( $area_params, $base ));

		}
 
 
	}

}





class WP_SP_Plugin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $customers_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {

		$hook = add_submenu_page(
			'hustca_form',
			'Webapply所有表单数据',
			'网管表单',
			'read_private_pages',
			'hustca_webapply',
			array( $this, 'plugin_settings_page' )
		);

		add_action( "load-$hook", array( $this, 'screen_option' ));

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>官网表单</h2>
			<h3>网管招募</h3>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder ">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->customers_obj->prepare_items();
								$this->customers_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = array(
			'label'   => '每页的项目数',
			'default' => 10,
			'option'  => 'customers_per_page'
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new WP_Customers_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


add_action( 'plugins_loaded', function () {
	WP_SP_Plugin::get_instance();
} );


 