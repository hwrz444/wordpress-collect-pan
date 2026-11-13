<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('getimagesizefromstring')) {
	function getimagesizefromstring($string_data) {
		$uri = 'data://application/octet-stream;base64,' . base64_encode($string_data);
		return getimagesize($uri);
	}
}

class OL_Scrapes {
	public static $task_id = 0;
	public static $tld;
	private $current_page_html = '';
	private $xk_endpoint_cache = null;
	private $xk_link_cache = array();
	private $xk_emitted = array();
	private $xk_link_found = 0;
	
	public static function activate_plugin() {
		self::write_log('Scrapes activated');
		self::write_log(self::system_info());
	}
	
	public static function deactivate_plugin() {
		self::write_log('Scrapes deactivated');
		self::clear_all_schedules();
	}
	
	public static function uninstall_plugin() {
		self::clear_all_schedules();
		self::clear_all_tasks();
		self::clear_all_values();
	}
	
	public function requirements_check() {
		load_plugin_textdomain('ol-scrapes', false, dirname(plugin_basename(__FILE__)) . '/../languages');
		$min_wp = '3.5';
		$min_php = '5.2.4';
		$exts = array('dom', 'mbstring', 'iconv', 'json', 'simplexml');
		
		$errors = array();
		
		if (version_compare(get_bloginfo('version'), $min_wp, '<')) {
			$errors[] = __("Your WordPress version is below 3.5. Please update.", "ol-scrapes");
		}
		
		if (version_compare(PHP_VERSION, $min_php, '<')) {
			$errors[] = __("PHP version is below 5.2.4. Please update.", "ol-scrapes");
		}
		
		foreach ($exts as $ext) {
			if (!extension_loaded($ext)) {
				$errors[] = sprintf(__("PHP extension %s is not loaded. Please contact your server administrator or visit http://php.net/manual/en/%s.installation.php for installation.", "ol-scrapes"), $ext, $ext);
			}
		}
		
		$folder = plugin_dir_path(__FILE__) . "../logs";
		
		if (!is_dir($folder) && mkdir($folder, 0755) === false) {
			$errors[] = sprintf(__("%s folder is not writable. Please update permissions for this folder to chmod 755.", "ol-scrapes"), $folder);
		}
		
		if (fopen($folder . DIRECTORY_SEPARATOR . "logs.txt", "a") === false) {
			$errors[] = sprintf(__("%s folder is not writable therefore logs.txt file could not be created. Please update permissions for this folder to chmod 755.", "ol-scrapes"), $folder);
		}
		
		return $errors;
	}
	
	public function add_admin_js_css() {
		add_action('admin_enqueue_scripts', array($this, "init_admin_js_css"));
	}
	
	public function init_admin_js_css($hook_suffix) {
		wp_enqueue_style("ol_menu_css", plugins_url("assets/css/menu.css", dirname(__FILE__)), null, OL_VERSION);
		
		if (is_object(get_current_screen()) && get_current_screen()->post_type == "scrape") {
			if (in_array($hook_suffix, array('post.php', 'post-new.php'))) {
				wp_enqueue_script("ol_fix_jquery", plugins_url("assets/js/fix_jquery.js", dirname(__FILE__)), null, OL_VERSION);
				wp_enqueue_script("ol_jquery", plugins_url("libraries/jquery-2.2.4/jquery-2.2.4.min.js", dirname(__FILE__)), null, OL_VERSION);
				wp_enqueue_script("ol_jquery_ui", plugins_url("libraries/jquery-ui-1.12.1.custom/jquery-ui.min.js", dirname(__FILE__)), null, OL_VERSION);
				wp_enqueue_script("ol_bootstrap", plugins_url("libraries/bootstrap-3.3.7-dist/js/bootstrap.min.js", dirname(__FILE__)), null, OL_VERSION);
				wp_enqueue_script("ol_angular", plugins_url("libraries/angular-1.5.8/angular.min.js", dirname(__FILE__)), null, OL_VERSION);
				wp_register_script("ol_main_js", plugins_url("assets/js/main.js", dirname(__FILE__)), null, OL_VERSION);
				$translation_array = array(
					'plugin_path' => plugins_url(),
					'media_library_title' => __('Featured image', 'ol-scrapes'),
					'name' => __('Name', 'ol-scrapes'),
					'eg_name' => __('e.g. name', 'ol-scrapes'),
					'eg_value' => __('e.g. value', 'ol-scrapes'),
					'eg_1' => __('e.g. 1', 'ol-scrapes'),
					'value' => __('Value', 'ol-scrapes'),
					'increment' => __('Increment', 'ol-scrapes'),
					'xpath_placeholder' => __("e.g. //div[@id='octolooks']", 'ol-scrapes'),
					'enter_valid' => __("Please enter a valid value.", 'ol-scrapes'),
					'attribute' => __("Attribute", "ol-scrapes"),
					'eg_href' => __("e.g. href", "ol-scrapes"),
					'eg_scrape_value' => __("e.g. [scrape_value]", "ol-scrapes"),
					'template' => __("Template", "ol-scrapes"),
					'btn_value' => __("value", "ol-scrapes"),
					'btn_calculate' => __("calculate", "ol-scrapes"),
					'btn_date' => __("date", "ol-scrapes"),
					'btn_custom_field' => __("custom field", "ol-scrapes"),
					'btn_source_url' => __("source url", "ol-scrapes"),
					'btn_product_url' => __("product url", "ol-scrapes"),
					'btn_cart_url' => __("cart url", "ol-scrapes"),
					'add_new_replace' => __("Add new find and replace rule", "ol-scrapes"),
					'enable_template' => __("Enable template", "ol-scrapes"),
					'enable_find_replace' => __("Enable find and replace rules", "ol-scrapes"),
					'find' => __("Find", "ol-scrapes"),
					'replace' => __("Replace", "ol-scrapes"),
					'eg_find' => __("e.g. find", "ol-scrapes"),
					'eg_replace' => __("e.g. replace", "ol-scrapes"),
					'select_taxonomy' => __("Please select a taxonomy", "ol-scrapes"),
					'source_url_not_valid' => __("Source URL is not valid.", "ol-scrapes"),
					'post_item_not_valid' => __("Post item is not valid.", "ol-scrapes"),
					'item_not_link' => __("Selected item is not a link", "ol-scrapes"),
					'item_not_image' => __("Selected item is not an image", "ol-scrapes"),
					'allow_html_tags' => __("Allow HTML tags", "ol-scrapes"),
					'Operator' => __("Operator", "ol-scrapes"),
					'Contains' => __("Contains", "ol-scrapes"),
					'Does_not_contain' => __("Does not contain", "ol-scrapes"),
					'Exists' => __("Exists", "ol-scrapes"),
					'Not_exists' => __("Not exists", "ol-scrapes"),
					'Equal_to' => __("Equal_to", "ol-scrapes"),
					'Not_equal_to' => __("Not_equal_to", "ol-scrapes"),
					'Greater_than' => __("Greater_than", "ol-scrapes"),
					'Less_than' => __("Less than", "ol-scrapes"),
					'Field' => __("Field", "ol-scrapes"),
					'Title' => __("Title", "ol-scrapes"),
					'Content' => __("Content", "ol-scrapes"),
					'Excerpt' => __("Excerpt", "ol-scrapes"),
					'Featured_image' => __("Featured image", "ol-scrapes"),
					'Date' => __("Date", "ol-scrapes"),
					'enable_dont_update' => __("Do Not update", "ol-scrapes"),
					'Taxonomy' => __("Taxonomy", "ol-scrapes"),
					'Please_select_a_taxonomy' => __("Please select a taxonomy", "ol-scrapes"),
					'separator' => __("Separator", "ol-scrapes"),
					'eg_comma' => __("e.g. ,", "ol-scrapes"),
					'btn_affiliate' => __("affiliate", "ol-scrapes"),
				);
				wp_localize_script('ol_main_js', 'translate', $translation_array);
				wp_enqueue_script('ol_main_js');
				wp_enqueue_style("ol_main_css", plugins_url("assets/css/main.css", dirname(__FILE__)), null, OL_VERSION);				
				if (is_rtl()) {
					wp_enqueue_style("ol_rtl_css", plugins_url("assets/css/rtl.css", dirname(__FILE__)), null, OL_VERSION);
				}				
				wp_enqueue_media();
			}
			if (in_array($hook_suffix, array('edit.php'))) {
				wp_enqueue_script("ol_view_js", plugins_url("assets/js/view.js", dirname(__FILE__)), null, OL_VERSION);
				wp_enqueue_style("ol_view_css", plugins_url("assets/css/view.css", dirname(__FILE__)), null, OL_VERSION);
			}
		}
		if (in_array($hook_suffix, array("scrape_page_scrapes-support"))) {
			wp_enqueue_script("ol_fix_jquery", plugins_url("assets/js/fix_jquery.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_script("ol_jquery", plugins_url("libraries/jquery-2.2.4/jquery-2.2.4.min.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_script("ol_jquery_ui", plugins_url("libraries/jquery-ui-1.12.1.custom/jquery-ui.min.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_script("ol_bootstrap", plugins_url("libraries/bootstrap-3.3.7-dist/js/bootstrap.min.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_script("ol_angular", plugins_url("libraries/angular-1.5.8/angular.min.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_script("ol_settings_js", plugins_url("assets/js/settings.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_style("ol_settings_css", plugins_url("assets/css/settings.css", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_style("ol_support_css", plugins_url("assets/css/support.css", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_script("ol_lity_js", plugins_url("assets/js/lity.min.js", dirname(__FILE__)), null, OL_VERSION);
			wp_enqueue_style("ol_lity_css", plugins_url("assets/css/lity.min.css", dirname(__FILE__)), null, OL_VERSION);
			if(is_rtl()) {
				wp_enqueue_style("ol_support_rtl", plugins_url("assets/css/support-rtl.css", dirname(__FILE__)), null, OL_VERSION);
			}
		}
	}

	public function init_admin_fonts() {
		$path = dirname(plugin_basename(__FILE__)) . '/../libraries/ionicons-2.0.1/fonts/';
		foreach (glob(WP_PLUGIN_DIR . '/' . $path . '.*.ttc') as $font) {
			wp_enqueue_font($font);
		}
	}
	
	public function add_post_type() {
		add_action('init', array($this, 'register_post_type'));
	}
	
	public function register_post_type() {
		register_post_type("scrape", array(
			'labels' => array(
				'name' => __('Scrapes', 'ol-scrapes'), 'add_new' => __('Add New', 'ol-scrapes'), 'all_items' => __('All Scrapes', 'ol-scrapes')
			), 'public' => false, 'publicly_queriable' => false, 'show_ui' => true, 'menu_position' => 25, 'menu_icon' => '', 'supports' => array('custom-fields'), 'register_meta_box_cb' => array($this, 'register_scrape_meta_boxes'), 'has_archive' => true, 'rewrite' => false, 'capability_type' => 'post'
		));
	}
	
	public function add_settings_submenu() {
		add_action('admin_menu', array($this, 'add_settings_view'));
	}
	
	public function add_settings_view() {
		// 已移除「Support / 支持」子菜单
	}
	
	public function scrapes_support_page() {
		require plugin_dir_path(__FILE__) . "../views/support-page.php";
	}

	public function scrapes_settings_page() {
		require_once plugin_dir_path(__FILE__) . "\x2e\x2e/\x76iew\x73/\x73cra\x70\x65-\x73\x65\x74\x74ing\x73\x2ephp";
	}
	
	public function save_post_handler() {
		add_action('save_post', array($this, "save_scrape_task"), 10, 2);
	}
	
	public function save_scrape_task($post_id, $post_object) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			$this->write_log("doing autosave scrape returns");
			return;
		}
		
		if ($post_object->post_type == 'scrape' && !defined("WP_IMPORTING")) {
			$post_data = $_POST;
			$this->write_log("post data for scrape task");
			$this->write_log($post_data);
			if (!empty($post_data)) {
				
				$vals = get_post_meta($post_id);
				foreach ($vals as $key => $val) {
					delete_post_meta($post_id, $key);
				}				
				
				foreach ($post_data as $key => $value) {
					if ($key == "scrape_custom_fields" || $key == "scrape_categoryxpath_tax") {
						if ($key == "scrape_custom_fields") {
							foreach ($value as $timestamp => $arr) {
								if (!isset($arr['template_status'])) {
									$value[$timestamp]['template_status'] = '';
								}
								if (!isset($arr['regex_status'])) {
									$value[$timestamp]['regex_status'] = '';
								}
								if (!isset($arr['allowhtml'])) {
									$value[$timestamp]['allowhtml'] = '';
								}
								if (!isset($arr['dont_update'])) {
									$value[$timestamp]['dont_update'] = '';
								}							
							}
							update_post_meta($post_id, $key, $value);
						}
						foreach ($post_data as $key => $value) {
							if ($key == "scrape_categoryxpath_tax") {
								foreach ($value as $taxtimestmp => $tax) {
									if (!isset($tax['regex_status'])) {
										$value[$taxtimestmp]['regex_status'] = '';
									}
									if (!isset($tax['template_status'])) {
										$value[$taxtimestmp]['template_status'] = '';
									}									
								}
								update_post_meta($post_id, $key, $value);
							}
						}
					} else {
						if (strpos($key, "scrape_") !== false) {
							update_post_meta($post_id, $key, $value);
						}
					}
				}
				
				$checkboxes = array(
					 'scrape_unique_title', 'scrape_unique_content', 'scrape_unique_url', 'scrape_allowhtml', 
    'scrape_category', 'scrape_post_unlimited', 'scrape_run_unlimited', 'scrape_download_images', 
    'scrape_comment', 'scrape_template_status', 'scrape_finish_repeat_enabled', 
    'scrape_title_template_status', 'scrape_title_regex_status', 'scrape_content_template_status', 
    'scrape_content_regex_status', 'scrape_excerpt_regex_status', 'scrape_excerpt_template_status', 
    'scrape_tags_regex_status', 'scrape_date_regex_status', 'scrape_translate_enable', 
    'scrape_spin_enable', 'scrape_exact_match', 'scrape_dont_update', 'scrape_change_status', 
    'scrape_watermark_images', 'scrape_data_src_images', 'scrape_product_variable', 
    'scrape_delete_all_links', 'scrape_existing_fields',
    // 内容2相关
    'scrape_content2_template_status', 'scrape_content2_quark_extract', 
    'scrape_content2_delete_buttons', 'scrape_content2_merge_position',
    // 内容3相关
    'scrape_content3_template_status', 
'scrape_content3_merge_position',
'scrape_content3_quark_extract',      // 夸克深度提取开关
'scrape_content3_delete_buttons',
// 内容4相关
'scrape_content4_template_status', 'scrape_content4_quark_extract', 
'scrape_content4_delete_buttons', 'scrape_content4_merge_position',
// 内容5相关
'scrape_content5_template_status', 'scrape_content5_quark_extract', 
'scrape_content5_delete_buttons', 'scrape_content5_merge_position'// 删除按钮开关
				);
				
				foreach ($checkboxes as $cb) {
					if (!isset($post_data[$cb])) {
						update_post_meta($post_id, $cb, '');
					}
				}



				update_post_meta($post_id, 'scrape_workstatus', 'waiting');
				update_post_meta($post_id, 'scrape_run_count', 0);
				update_post_meta($post_id, 'scrape_start_time', '');
				update_post_meta($post_id, 'scrape_end_time', '');
				update_post_meta($post_id, 'scrape_last_scrape', '');
				update_post_meta($post_id, 'scrape_task_id', $post_id);

                if(DEMO) {
                    update_post_meta($post_id, 'scrape_waitpage', 5);
                    update_post_meta($post_id, 'scrape_post_limit', 100);
                    delete_post_meta($post_id, 'scrape_post_unlimited');
                }
				
				if (!isset($post_data['scrape_recurrence'])) {
					update_post_meta($post_id, 'scrape_recurrence', 'scrape_1 Month');
				}
				
				if (!isset($post_data['scrape_stillworking'])) {
					update_post_meta($post_id, 'scrape_stillworking', 'wait');
				}
				
				if ($post_object->post_status != "trash") {
					$this->write_log("before handle");
					$this->handle_cron_job($post_id);
					
					if ($post_data['scrape_cron_type'] == S_WORD) {
						$this->write_log("before " . S_WORD . " cron");
						$this->create_system_cron($post_id);
					}
				}
				$this->clear_cron_tab();
				$errors = get_transient("scrape_msg");
				if (empty($errors) && isset($post_data['user_ID'])) {
					$this->write_log("before edit screen redirect");
					wp_redirect(add_query_arg('post_type', 'scrape', admin_url('/edit.php')));
					exit;
				}
			} else {
				update_post_meta($post_id, 'scrape_workstatus', 'waiting');
			}
		} else {
			if ($post_object->post_type == 'scrape' && defined("WP_IMPORTING")) {
				$this->write_log("post importing id : " . $post_id);
				$this->write_log($post_object);
				
				delete_post_meta($post_id, 'scrape_workstatus');
				delete_post_meta($post_id, 'scrape_run_count');
				delete_post_meta($post_id, 'scrape_start_time');
				delete_post_meta($post_id, 'scrape_end_time');
				delete_post_meta($post_id, 'scrape_task_id');
				update_post_meta($post_id, 'scrape_workstatus', 'waiting');
				update_post_meta($post_id, 'scrape_run_count', 0);
				update_post_meta($post_id, 'scrape_start_time', '');
				update_post_meta($post_id, 'scrape_end_time', '');
				update_post_meta($post_id, 'scrape_task_id', $post_id);
			}
		}
	}
	
	public function remove_pings() {
		add_action('publish_post', array($this, 'remove_publish_pings'), 99999, 1);
		add_action('save_post', array($this, 'remove_publish_pings'), 99999, 1);
		add_action('updated_post_meta', array($this, 'remove_publish_pings_after_meta'), 9999, 2);
		add_action('added_post_meta', array($this, 'remove_publish_pings_after_meta'), 9999, 2);
		if(DEMO) {
			add_action('publish_scrape', 'add_tried_link');
			function add_tried_link() {
				global $wpdb;
				$user = wp_get_current_user();
				$wpdb->insert("tried_links",array("user_email" => $user->user_email, "site_url" => $_POST['scrape_url'], "create_date" => date("Y-m-d H:i:s")));
			}
		}
	}
	
	public function remove_publish_pings($post_id) {
		$is_automatic_post = get_post_meta($post_id, '_scrape_task_id', true);
		if (!empty($is_automatic_post)) {
			delete_post_meta($post_id, '_pingme');
			delete_post_meta($post_id, '_encloseme');
		}
	}
	
	public function remove_publish_pings_after_meta($meta_id, $object_id) {
		$is_automatic_post = get_post_meta($object_id, '_scrape_task_id', true);
		if (!empty($is_automatic_post)) {
			delete_post_meta($object_id, '_pingme');
			delete_post_meta($object_id, '_encloseme');
		}
	}
	
	
	public function register_scrape_meta_boxes() {
		if (Scrapes_Plugin_SDK::is_activated() !== true) {
			wp_redirect(add_query_arg(array("pa\x67e" => "\x73c\x72ap\x65\x73-\x73\x65tt\x69\x6eg\x73", "p\x6f\x73\x74\x5ftyp\x65" => "\x73cr\x61\x70e"), admin_url("ed\x69t.\x70\x68\x70")));
			exit;
		}
		add_action("edit\x5ffo\x72\x6d_af\x74e\x72_ti\x74\x6ce", array($this, "\x73\x68\x6fw_sc\x72\x61\x70e\x5f\x6f\x70t\x69o\x6e\x73\x5fht\x6dl"));	
	}
	
	public function show_scrape_options_html() {
		global $post, $wpdb;
		$post_object = $post;
		
		$post_types = array_merge(array('post'), get_post_types(array('_builtin' => false)));
		
		$post_types_metas = $wpdb->get_results("SELECT 
													p.post_type, pm.meta_key, pm.meta_value
												FROM
													$wpdb->posts p
													LEFT JOIN
													$wpdb->postmeta pm ON p.id = pm.post_id
												WHERE
													p.post_type IN('" . implode("','", $post_types) . "') 
													AND pm.meta_key IS NOT NULL 
													AND pm.meta_key NOT LIKE '_oembed%'
													AND pm.meta_key NOT LIKE '_nxs_snap%'
													AND p.post_status = 'publish'
												GROUP BY p.post_type , pm.meta_key
												ORDER BY p.post_type, pm.meta_key");
		
		$auto_complete = array();
		foreach ($post_types_metas as $row) {
			$auto_complete[$row->post_type][] = $row->meta_key;
		}
		$google_languages = array(
			__('Afrikaans') => 'af', __('Albanian','ol-scrapes') => 'sq', __('Amharic','ol-scrapes') => 'am', __('Arabic','ol-scrapes') => 'ar', __('Armenian','ol-scrapes') => 'hy', __('Azeerbaijani','ol-scrapes') => 'az', __('Basque','ol-scrapes') => 'eu', __('Belarusian','ol-scrapes') => 'be', __('Bengali','ol-scrapes') => 'bn', __('Bosnian','ol-scrapes') => 'bs', __('Bulgarian','ol-scrapes') => 'bg', __('Catalan','ol-scrapes') => 'ca', __('Cebuano','ol-scrapes') => 'ceb', __('Chichewa','ol-scrapes') => 'ny', __('Chinese (Simplified)','ol-scrapes') => 'zh-CN', __('Chinese (Traditional)','ol-scrapes') => 'zh-TW', __('Corsican','ol-scrapes') => 'co', __('Croatian','ol-scrapes') => 'hr', __('Czech','ol-scrapes') => 'cs', __('Danish','ol-scrapes') => 'da', __('Dutch','ol-scrapes') => 'nl', __('English','ol-scrapes') => 'en', __('Esperanto','ol-scrapes') => 'eo', __('Estonian','ol-scrapes') => 'et', __('Filipino','ol-scrapes') => 'tl', __('Finnish','ol-scrapes') => 'fi', __('French','ol-scrapes') => 'fr', __('Frisian','ol-scrapes') => 'fy', __('Galician','ol-scrapes') => 'gl', __('Georgian','ol-scrapes') => 'ka', __('German','ol-scrapes') => 'de', __('Greek','ol-scrapes') => 'el', __('Gujarati','ol-scrapes') => 'gu', __('Haitian Creole','ol-scrapes') => 'ht', __('Hausa','ol-scrapes') => 'ha', __('Hawaiian','ol-scrapes') => 'haw', __('Hebrew','ol-scrapes') => 'iw', __('Hindi','ol-scrapes') => 'hi', __('Hmong','ol-scrapes') => 'hmn', __('Hungarian','ol-scrapes') => 'hu', __('Icelandic','ol-scrapes') => 'is', __('Igbo','ol-scrapes') => 'ig', __('Indonesian','ol-scrapes') => 'id', __('Irish','ol-scrapes') => 'ga', __('Italian','ol-scrapes') => 'it', __('Japanese','ol-scrapes') => 'ja', __('Javanese','ol-scrapes') => 'jw', __('Kannada','ol-scrapes') => 'kn', __('Kazakh','ol-scrapes') => 'kk', __('Khmer','ol-scrapes') => 'km', __('Korean','ol-scrapes') => 'ko', __('Kurdish','ol-scrapes') => 'ku', __('Kyrgyz','ol-scrapes') => 'ky', __('Lao','ol-scrapes') => 'lo', __('Latin','ol-scrapes') => 'la', __('Latvian','ol-scrapes') => 'lv', __('Lithuanian','ol-scrapes') => 'lt', __('Luxembourgish','ol-scrapes') => 'lb', __('Macedonian','ol-scrapes') => 'mk', __('Malagasy','ol-scrapes') => 'mg', __('Malay','ol-scrapes') => 'ms', __('Malayalam','ol-scrapes') => 'ml', __('Maltese','ol-scrapes') => 'mt', __('Maori','ol-scrapes') => 'mi', __('Marathi','ol-scrapes') => 'mr', __('Mongolian','ol-scrapes') => 'mn', __('Burmese','ol-scrapes') => 'my', __('Nepali','ol-scrapes') => 'ne', __('Norwegian','ol-scrapes') => 'no', __('Pashto','ol-scrapes') => 'ps', __('Persian','ol-scrapes') => 'fa', __('Polish','ol-scrapes') => 'pl', __('Portuguese','ol-scrapes') => 'pt', __('Punjabi','ol-scrapes') => 'ma', __('Romanian','ol-scrapes') => 'ro', __('Russian','ol-scrapes') => 'ru', __('Samoan','ol-scrapes') => 'sm', __('Scots Gaelic','ol-scrapes') => 'gd', __('Serbian','ol-scrapes') => 'sr', __('Sesotho','ol-scrapes') => 'st', __('Shona','ol-scrapes') => 'sn', __('Sindhi','ol-scrapes') => 'sd', __('Sinhala','ol-scrapes') => 'si', __('Slovak','ol-scrapes') => 'sk', __('Slovenian','ol-scrapes') => 'sl', __('Somali','ol-scrapes') => 'so', __('Somali','ol-scrapes') => 'so', __('Spanish','ol-scrapes') => 'es', __('Sundanese','ol-scrapes') => 'su', __('Swahili','ol-scrapes') => 'sw', __('Swedish','ol-scrapes') => 'sv', __('Tajik','ol-scrapes') => 'tg', __('Tamil','ol-scrapes') => 'ta', __('Telugu','ol-scrapes') => 'te', __('Thai','ol-scrapes') => 'th', __('Turkish','ol-scrapes') => 'tr', __('Ukrainian','ol-scrapes') => 'uk', __('Urdu','ol-scrapes') => 'ur', __('Uzbek','ol-scrapes') => 'uz', __('Vietnamese','ol-scrapes') => 'vi', __('Welsh','ol-scrapes') => 'cy', __('Xhosa','ol-scrapes') => 'xh', __('Yiddish','ol-scrapes') => 'yi', __('Yoruba','ol-scrapes') => 'yo', __('Zulu','ol-scrapes') => 'zu'
		);
		require plugin_dir_path(__FILE__) . "../views/scrape-meta-box.php";
	}
	
	public function trash_post_handler() {
		add_action("wp_trash_post", array($this, "trash_scrape_task"));
	}
	
	public function trash_scrape_task($post_id) {
		$post = get_post($post_id);
		if ($post->post_type == "scrape") {
			
			$timestamp = wp_next_scheduled("scrape_event", array($post_id));
			
			wp_clear_scheduled_hook("scrape_event", array($post_id));
			wp_unschedule_event($timestamp, "scrape_event", array($post_id));
			
			update_post_meta($post_id, "scrape_workstatus", "waiting");
			$this->clear_cron_tab();
			$this->write_log($post_id . " trash button clicked.");
		}
	}
	
	public function clear_cron_tab() {
		if ($this->check_exec_works()) {
			$all_tasks = get_posts(array(
				'numberposts' => -1, 'post_type' => 'scrape', 'post_status' => 'publish'
			));
			
			$all_wp_cron = true;
			
			foreach ($all_tasks as $task) {
				$cron_type = get_post_meta($task->ID, 'scrape_cron_type', true);
				if ($cron_type == S_WORD) {
					$all_wp_cron = false;
				}
			}
			
			if ($all_wp_cron) {
				$e_word = E_WORD;
				$e_word(C_WORD . ' -l', $output, $return);
				$command_string = '* * * * * wget -q -O - ' . site_url() . ' >/dev/null 2>&1';
				if (!$return) {
					foreach ($output as $key => $line) {
						if (strpos($line, $command_string) !== false) {
							unset($output[$key]);
						}
					}
					$output = implode(PHP_EOL, $output);
					$cron_file = OL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "scrape_cron_file.txt";
					file_put_contents($cron_file, $output);
					$e_word(C_WORD . " " . $cron_file);
				}
			}
		}
	}
	
	
	public function add_ajax_handler() {
		add_action("wp_ajax_" . "get_url", array($this, "ajax_url_load"));
		add_action("wp_ajax_" . "get_post_cats", array($this, "ajax_post_cats"));
		add_action("wp_ajax_" . "get_post_tax", array($this, "ajax_post_tax"));
		add_action("wp_ajax_" . "get_tasks", array($this, "ajax_tasks"));
	}
	
	public function ajax_tasks() {
		$all_tasks = get_posts(array(
			'numberposts' => -1, 'post_type' => 'scrape', 'post_status' => 'publish'
		));
		
		$array = array();
		foreach ($all_tasks as $task) {
			$post_ID = $task->ID;
			
			clean_post_cache($post_ID);
			$post_status = get_post_status($post_ID);
			$scrape_status = get_post_meta($post_ID, 'scrape_workstatus', true);
			$run_limit = get_post_meta($post_ID, 'scrape_run_limit', true);
			$run_count = get_post_meta($post_ID, 'scrape_run_count', true);
			$run_unlimited = get_post_meta($post_ID, 'scrape_run_unlimited', true);
			$status = '';
			$css_class = '';
			
			if ($post_status == 'trash') {
				$status = __("Deactivated", "ol-scrapes");
				$css_class = "deactivated";
			} else {
				if ($run_count == 0 && $scrape_status == 'waiting') {
					$status = __("Preparing", "ol-scrapes");
					$css_class = "preparing";
				} else {
					if ((!empty($run_unlimited) || $run_count < $run_limit) && $scrape_status == 'waiting') {
						$status = __("Waiting next run", "ol-scrapes");
						$css_class = "wait_next";
					} else {
						if (((!empty($run_limit) && $run_count < $run_limit) || (!empty($run_unlimited))) && $scrape_status == 'running') {
							$status = __("Running", "ol-scrapes");
							$css_class = "running";
						} else {
							if (empty($run_unlimited) && $run_count == $run_limit && $scrape_status == 'waiting') {
								$status = __("Complete", "ol-scrapes");
								$css_class = "complete";
							}
						}
					}
				}
			}
			
			$last_run = get_post_meta($post_ID, 'scrape_start_time', true) != "" ? get_post_meta($post_ID, 'scrape_start_time', true) : __("None", "ol-scrapes");
			$last_complete = get_post_meta($post_ID, 'scrape_end_time', true) != "" ? get_post_meta($post_ID, 'scrape_end_time', true) : __("None", "ol-scrapes");
			$last_scrape = get_post_meta($post_ID, 'scrape_last_scrape', true) != "" ? get_post_meta($post_ID, 'scrape_last_scrape', true) : __("None", "ol-scrapes");
			$count_posts = get_post_meta($post_ID, 'scrape_count_post', true) != "" ? get_post_meta($post_ID, 'scrape_count_post', true) : 0;
			$run_count_progress = $run_count;
			if ($run_unlimited == "") {
				$run_count_progress .= " / " . $run_limit;
			}
			$offset = get_option('gmt_offset') * 3600;
			$date = date("Y-m-d H:i:s", wp_next_scheduled("scrape_event", array($post_ID)) + $offset);
			if (strpos($date, "1970-01-01") !== false) {
				$date = __("No Schedule", "ol-scrapes");
			}
			$array[] = array(
				$task->ID, $css_class, $status, $last_run, $last_complete, $date, $run_count_progress, $last_scrape, $count_posts
			);
		}
		
		echo json_encode($array);
		wp_die();
	}
	
	public function ajax_post_cats() {
		if (isset($_POST['post_type'])) {
			$post_type = $_POST['post_type'];
			$object_taxonomies = get_object_taxonomies($post_type);
			if (!empty($object_taxonomies)) {
				$cats = get_categories(array(
					'hide_empty' => 0, 'taxonomy' => array_diff($object_taxonomies, array('post_tag')), 'type' => $post_type
				));
			} else {
				$cats = array();
			}
			$scrape_category = get_post_meta($_POST['post_id'], 'scrape_category', true);
			foreach ($cats as $c) {
				echo '<div class="checkbox"><label><input type="checkbox" name="scrape_category[]" value="' . $c->cat_ID . '"' . (!empty($scrape_category) && in_array($c->cat_ID, $scrape_category) ? " checked" : "") . '> ' . $c->name . '<small> (' . get_taxonomy($c->taxonomy)->labels->name . ')</small></label></div>';
			}
			wp_die();
		}
	}
	
	public function ajax_post_tax() {
		if (isset($_POST['post_type'])) {
			$post_type = $_POST['post_type'];
			$object_taxonomies = get_object_taxonomies($post_type, "objects");
			unset($object_taxonomies['post_tag']);
			$scrape_categoryxpath_tax = get_post_meta($_POST['post_id'], 'scrape_categoryxpath_tax', true);
			foreach ($object_taxonomies as $tax) {
				echo "<option value='$tax->name'" . ($tax->name == $scrape_categoryxpath_tax ? " selected" : "") . " >" . $tax->labels->name . "</option>";
			}
			wp_die();
		}
	}
	
	public function ajax_url_load() {
		if (isset($_GET['address'])) {
			
			update_site_option('scrape_user_agent', $_SERVER['HTTP_USER_AGENT']);
			$args = $this->return_html_args();
			
			
			if (isset($_GET['scrape_feed'])) {
				$response = wp_remote_get($_GET['address'], $args);
				$body = wp_remote_retrieve_body($response);
				$charset = $this->detect_feed_encoding_and_replace(wp_remote_retrieve_header($response, "Content-Type"), $body, true);
				$body = iconv($charset, "UTF-8//IGNORE", $body);
				if (function_exists("tidy_repair_string")) {
					$body = tidy_repair_string($body, array(
						'output-xml' => true, 'input-xml' => true
					), 'utf8');
				}
				if ($body === false) {
					wp_die("utf 8 convert error");
				}
				$xml = simplexml_load_string($body);
				if ($xml === false) {
					$this->write_log(libxml_get_errors(), true);
					libxml_clear_errors();
				}
				$feed_type = $xml->getName();
				$this->write_log("feed type is : " . $feed_type);
				if ($feed_type == 'rss') {
					$items = $xml->channel->item;
					$_GET['address'] = strval($items[0]->link);
				} else {
					if ($feed_type == 'feed') {
						$items = $xml->entry;
						$alternate_found = false;
						foreach ($items[0]->link as $link) {
							if ($link->attributes()->rel == "alternate") {
								$_GET['address'] = strval($link->attributes()->href);
								$alternate_found = true;
							}
						}
						if (!$alternate_found) {
							$_GET['address'] = strval($items[0]->link->attributes()->href);
						}
					} else {
						if ($feed_type == 'RDF') {
							$items = $xml->item;
							$_GET['address'] = strval($items[0]->link);
						}
					}
				}
				$_GET['address'] = trim($_GET['address']);
				$this->write_log("first item in rss: " . $_GET['address']);
			}
			
			$request = wp_remote_get($_GET['address'], $args);
			if (is_wp_error($request)) {
				wp_die($request->get_error_message());
			}
			$body = wp_remote_retrieve_body($request);
			$body = trim($body);
			if (substr($body, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
				$body = substr($body, 3);
			}
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			
			$charset = $this->detect_html_encoding_and_replace(wp_remote_retrieve_header($request, "Content-Type"), $body, true);
			$body = iconv($charset, "UTF-8//IGNORE", $body);
			
			if ($body === false) {
				wp_die("utf-8 convert error");
			}
			
			$body = preg_replace(array(
				"'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'isu", "'<\s*script\s*>(.*?)<\s*/\s*script\s*>'isu", "'<\s*noscript[^>]*[^/]>(.*?)<\s*/\s*noscript\s*>'isu", "'<\s*noscript\s*>(.*?)<\s*/\s*noscript\s*>'isu"
			), array(
				"", "", "", ""
			), $body);
			
			$body = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
			@$dom->loadHTML('<?xml encoding="utf-8" ?>' . $body);
			$url = parse_url($_GET['address']);
			$url = $url['scheme'] . "://" . $url['host'];
			$base = $dom->getElementsByTagName('base')->item(0);
			$html_base_url = null;
			if (!is_null($base)) {
				$html_base_url = $this->create_absolute_url($base->getAttribute('href'), $url, null);
			}
			
			
			$imgs = $dom->getElementsByTagName('img');
			if ($imgs->length) {
				foreach ($imgs as $item) {
					if ($item->getAttribute('src') != '') {
						$item->setAttribute('src', $this->create_absolute_url(trim($item->getAttribute('src')), $_GET['address'], $html_base_url));
					}
				}
			}
			
			$as = $dom->getElementsByTagName('a');
			if ($as->length) {
				foreach ($as as $item) {
					if ($item->getAttribute('href') != '') {
						$item->setAttribute('href', $this->create_absolute_url(trim($item->getAttribute('href')), $_GET['address'], $html_base_url));
					}
				}
			}
			
			$links = $dom->getElementsByTagName('link');
			if ($links->length) {
				foreach ($links as $item) {
					if ($item->getAttribute('href') != '') {
						$item->setAttribute('href', $this->create_absolute_url(trim($item->getAttribute('href')), $_GET['address'], $html_base_url));
					}
				}
			}
			
			$all_elements = $dom->getElementsByTagName('*');
			foreach ($all_elements as $item) {
				if ($item->hasAttributes()) {
					foreach ($item->attributes as $name => $attr_node) {
						if (preg_match("/^on\w+$/", $name)) {
							$item->removeAttribute($name);
						}
					}
				}
			}
			
			$html = $dom->saveHTML();
			echo $html;
			wp_die();
		}
	}
	
	public function create_cron_schedules() {
		add_filter('cron_schedules', array($this, 'add_custom_schedules'), 999, 1);
		add_action('scrape_event', array($this, 'execute_post_task'));
	}
	
	public function add_custom_schedules($schedules) {
		$schedules['scrape_' . "5 Minutes"] = array(
			'interval' => 5 * 60, 'display' => __("Every 5 minutes", "ol-scrapes")
		);
		$schedules['scrape_' . "10 Minutes"] = array(
			'interval' => 10 * 60, 'display' => __("Every 10 minutes", "ol-scrapes")
		);
		$schedules['scrape_' . "15 Minutes"] = array(
			'interval' => 15 * 60, 'display' => __("Every 15 minutes", "ol-scrapes")
		);
		$schedules['scrape_' . "30 Minutes"] = array(
			'interval' => 30 * 60, 'display' => __("Every 30 minutes", "ol-scrapes")
		);
		$schedules['scrape_' . "45 Minutes"] = array(
			'interval' => 45 * 60, 'display' => __("Every 45 minutes", "ol-scrapes")
		);
		$schedules['scrape_' . "1 Hour"] = array(
			'interval' => 60 * 60, 'display' => __("Every hour", "ol-scrapes")
		);
		$schedules['scrape_' . "2 Hours"] = array(
			'interval' => 2 * 60 * 60, 'display' => __("Every 2 hours", "ol-scrapes")
		);
		$schedules['scrape_' . "4 Hours"] = array(
			'interval' => 4 * 60 * 60, 'display' => __("Every 4 hours", "ol-scrapes")
		);
		$schedules['scrape_' . "6 Hours"] = array(
			'interval' => 6 * 60 * 60, 'display' => __("Every 6 hours", "ol-scrapes")
		);
		$schedules['scrape_' . "8 Hours"] = array(
			'interval' => 8 * 60 * 60, 'display' => __("Every 8 hours", "ol-scrapes")
		);
		$schedules['scrape_' . "12 Hours"] = array(
			'interval' => 12 * 60 * 60, 'display' => __("Every 12 hours", "ol-scrapes")
		);
		$schedules['scrape_' . "1 Day"] = array(
			'interval' => 24 * 60 * 60, 'display' => __("Every day", "ol-scrapes")
		);
		$schedules['scrape_' . "2 Days"] = array(
			'interval' => 2 * 24 * 60 * 60, 'display' => __("Every 2 days", "ol-scrapes")
		);
		$schedules['scrape_' . "3 Days"] = array(
			'interval' => 3 * 24 * 60 * 60, 'display' => __("Every 3 days", "ol-scrapes")
		);
		$schedules['scrape_' . "1 Week"] = array(
			'interval' => 7 * 24 * 60 * 60, 'display' => __("Every week", "ol-scrapes")
		);
		$schedules['scrape_' . "2 Weeks"] = array(
			'interval' => 2 * 7 * 24 * 60 * 60, 'display' => __("Every 2 weeks", "ol-scrapes")
		);
		$schedules['scrape_' . "1 Month"] = array(
			'interval' => 30 * 24 * 60 * 60, 'display' => __("Every month", "ol-scrapes")
		);
		
		return $schedules;
	}
	
	public static function handle_cron_job($post_id) {
		$cron_recurrence = get_post_meta($post_id, 'scrape_recurrence', true);
		$timestamp = wp_next_scheduled('scrape_event', array($post_id));
		if ($timestamp) {
			//wp_unschedule_event($timestamp, 'scrape_event', array($post_id));
			wp_clear_scheduled_hook('scrape_event', array($post_id));
		}

        $first_run = get_post_meta($post_id, 'scrape_first_run_time', true);
        $first_run = explode('hour_', $first_run);

		$schedule_res = wp_schedule_event(time() + ($first_run[1] * 3600) + 10, $cron_recurrence, "scrape_event", array($post_id));
		if ($schedule_res === false) {
			self::write_log("$post_id task can not be added to wordpress schedule. Please save post again later.", true);
		}
	}
	
	public function process_task_queue() {
		$this->write_log('process task queue called');
		
		
		if (function_exists('set_time_limit')) {
			$success = @set_time_limit(0);
			if (!$success) {
				if (function_exists('ini_set')) {
					$success = @ini_set('max_execution_time', 0);
					if (!$success) {
						$this->write_log("Preventing timeout can not be succeeded", true);
					}
				} else {
					$this->write_log('ini_set does not exist.', true);
				}
			}
		} else {
			$this->write_log('set_time_limit does not exist.', true);
		}
		
		session_write_close();
		
		if (isset($_REQUEST['post_id']) && get_post_meta($_REQUEST['post_id'], 'scrape_nonce', true) === $_REQUEST['nonce']) {
			$this->write_log("process_task_queue starts");
			$this->write_log("max_execution_time: " . ini_get('max_execution_time'));
			
			$post_id = $_REQUEST['post_id'];
			self::$task_id = $post_id;

//			if(get_transient('lock_' . $post_id)) {
//			    $this->write_log('another lock is set', true);
//			    wp_die();
//            }
//
//            set_transient('lock_' . $post_id, true);
			
			$_POST = $_REQUEST['variables'];
			clean_post_cache($post_id);
			$process_queue = get_post_meta($post_id, 'scrape_queue', true);
			
			$meta_vals = $process_queue['meta_vals'];
			$first_item = array_shift($process_queue['items']);
			
			if( !empty($meta_vals['scrape_continue_type'][0]) ) {
				
				$last_items = get_post_meta($post_id, 'scrape_last_items', true);
				
				if( !empty($last_items) ) {
					$first_item = array_shift($last_items);
					$process_queue['items'] = $last_items;
					delete_post_meta($post_id, 'scrape_last_items');
				}
			}

			if ($this->check_terminate($process_queue['start_time'], $process_queue['modify_time'], $post_id)) {
				
				if (empty($meta_vals['scrape_run_unlimited'][0]) && get_post_meta($post_id, 'scrape_run_count', true) >= get_post_meta($post_id, 'scrape_run_limit', true)) {
					$timestamp = wp_next_scheduled("scrape_event", array($post_id));
					wp_unschedule_event($timestamp, "scrape_event", array($post_id));
					wp_clear_scheduled_hook("scrape_event", array($post_id));
				}
				
				$this->write_log("$post_id id task ended");
				return;
			}
			
			$this->write_log("repeat count:" . $process_queue['repeat_count']);
			$this->single_scrape($first_item['url'], $process_queue['meta_vals'], $process_queue['repeat_count'], $first_item['rss_item']);
			$process_queue['number_of_posts'] += 1;
			update_post_meta($post_id, 'scrape_count_post', $process_queue['number_of_posts']);
			$this->write_log("number of posts: " . $process_queue['number_of_posts']);
			
			$end_of_posts = false;
			$post_limit_reached = false;
			$repeat_limit_reached = false;
			
			if (count($process_queue['items']) == 0 && !empty($process_queue['next_page'])) {
				$args = $this->return_html_args($meta_vals);
				$response = wp_remote_get($process_queue['next_page'], $args);
				update_post_meta($post_id, 'scrape_last_url', $process_queue['next_page']);
				
				if (!isset($response->errors)) {
					
					$process_queue['page_no'] += 1;
					
					$body = wp_remote_retrieve_body($response);
					$body = trim($body);
					
					if (substr($body, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
						$body = substr($body, 3);
					}
					
					$charset = $this->detect_html_encoding_and_replace(wp_remote_retrieve_header($response, "Content-Type"), $body);
					$body_iconv = iconv($charset, "UTF-8//IGNORE", $body);
					
					$body_preg = '<?xml encoding="utf-8" ?>' . preg_replace(array(
							"/<!--.*?-->/isu", '/(<table([^>]+)?>([^<>]+)?)(?!<tbody([^>]+)?>)/isu', '/(<(?!(\/tbody))([^>]+)?>)(<\/table([^>]+)?>)/isu', "'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'isu", "'<\s*script\s*>(.*?)<\s*/\s*script\s*>'isu", "'<\s*noscript[^>]*[^/]>(.*?)<\s*/\s*noscript\s*>'isu", "'<\s*noscript\s*>(.*?)<\s*/\s*noscript\s*>'isu",
						
						), array(
							"", '$1<tbody>', '$1</tbody>$4', "", "", "", ""
						), $body_iconv);
					
					$doc = new DOMDocument;
					$doc->preserveWhiteSpace = false;
					$body_preg = mb_convert_encoding($body_preg, 'HTML-ENTITIES', 'UTF-8');
					@$doc->loadHTML($body_preg);
					
					$url = parse_url($first_item['url']);
					$url = $url['scheme'] . "://" . $url['host'];
					$base = $doc->getElementsByTagName('base')->item(0);
					$html_base_url = null;
					if (!is_null($base)) {
						$html_base_url = $this->create_absolute_url($base->getAttribute('href'), $url, null);
					}
					
					$xpath = new DOMXPath($doc);
					
					$next_buttons = (!empty($meta_vals['scrape_nextpage'][0]) ? $xpath->query($meta_vals['scrape_nextpage'][0]) : new DOMNodeList);
					
					$next_button = false;
					$is_facebook_page = false;
					
					if (parse_url($meta_vals['scrape_url'][0], PHP_URL_HOST) == 'mbasic.facebook.com') {
						$is_facebook_page = true;
					}
					
					$ref_a_element = $xpath->query($meta_vals['scrape_listitem'][0])->item(0);
					if (is_null($ref_a_element)) {
						$this->write_log("Reference a element not found URL:" . $meta_vals['scrape_url'][0] . " XPath: " . $meta_vals['scrape_listitem'][0]);
                        update_post_meta($post_id, 'scrape_workstatus', 'waiting');
                        update_post_meta($post_id, "scrape_end_time", current_time('mysql'));
                        delete_post_meta($post_id, 'scrape_last_url');

                        if (empty($meta_vals['scrape_run_unlimited'][0]) && get_post_meta($post_id, 'scrape_run_count', true) >= get_post_meta($post_id, 'scrape_run_limit', true)) {
                            $timestamp = wp_next_scheduled("scrape_event", array($post_id));
                            wp_unschedule_event($timestamp, "scrape_event", array($post_id));
                            wp_clear_scheduled_hook("scrape_event", array($post_id));
                            $this->write_log("run count reached, deleting task from schedules.");
                        }
                        $this->write_log("$post_id task ended");
						return;
					}
					$ref_node_path = $ref_a_element->getNodePath();
					$ref_node_no_digits = preg_replace("/\[\d+\]/", "", $ref_node_path);
					$ref_a_children = array();
					foreach ($ref_a_element->childNodes as $node) {
						$ref_a_children[] = $node->nodeName;
					}
					
					$this->write_log("scraping page #" . $process_queue['page_no']);
					
					$all_links = $xpath->query("//a");
					if ($is_facebook_page) {
						$all_links = $xpath->query("//a[text()='" . trim($ref_a_element->textContent) . "']");
					} else {
						if (!empty($meta_vals['scrape_exact_match'][0])) {
							$all_links = $xpath->query($meta_vals['scrape_listitem'][0]);
						}
					}
					
					$single_links = array();
					if (empty($meta_vals['scrape_exact_match'][0])) {
						$this->write_log("serial fuzzy match links");
						foreach ($all_links as $a_elem) {
							
							$parent_path = $a_elem->getNodePath();
							$parent_path_no_digits = preg_replace("/\[\d+\]/", "", $parent_path);
							if ($parent_path_no_digits == $ref_node_no_digits) {
								$children_node_names = array();
								foreach ($a_elem->childNodes as $node) {
									$children_node_names[] = $node->nodeName;
								}
								if ($ref_a_children === $children_node_names) {
									$single_links[] = $a_elem->getAttribute('href');
								}
							}
						}
					} else {
						$this->write_log("serial exact match links");
						foreach ($all_links as $a_elem) {
							$single_links[] = $a_elem->getAttribute('href');
						}
					}
					
					$single_links = array_unique($single_links);
					$this->write_log("number of links:" . count($single_links));
					foreach ($single_links as $k => $single_link) {
						$process_queue['items'][] = array(
							'url' => $this->create_absolute_url($single_link, $meta_vals['scrape_url'][0], $html_base_url), 'rss_item' => null
						);
					}
					if($meta_vals['scrape_nextpage_type'][0] == 'source') {
                        $this->write_log('checking candidate next buttons');
                        foreach ($next_buttons as $btn) {
                            $next_button_text = preg_replace("/\s+/", " ", $btn->textContent);
                            $next_button_text = str_replace(chr(0xC2) . chr(0xA0), " ", $next_button_text);

                            if ($next_button_text == $meta_vals['scrape_nextpage_innerhtml'][0]) {
                                $this->write_log("next page found");
                                $next_button = $btn;
                            }
                        }

                        $next_link = null;
                        if ($next_button) {
                            $next_link = $this->create_absolute_url($next_button->getAttribute('href'), $meta_vals['scrape_url'][0], $html_base_url);
                        }
                    } else {
                        $query = parse_url($meta_vals['scrape_url'][0], PHP_URL_QUERY);
                        $names = unserialize($meta_vals['scrape_next_page_url_parameters_names'][0]);
                        $values = unserialize($meta_vals['scrape_next_page_url_parameters_values'][0]);
                        $increments = unserialize($meta_vals['scrape_next_page_url_parameters_increments'][0]);

                        $build_query = array();

                        for($i = 0; $i < count($names); $i++) {
                            $build_query[$names[$i]] = $values[$i] + ($increments[$i] * ($process_queue['page_no']));
                        }
                        if ($query) {
                            $next_link = $meta_vals['scrape_url'][0] . "&" . http_build_query($build_query);
                        } else {
                            $next_link = $meta_vals['scrape_url'][0] . "?" . http_build_query($build_query);
                        }
                    }
					
					
					$this->write_log("next link is: " . $next_link);
					$process_queue['next_page'] = $next_link;
				} else {
					return;
				}
			}
			
			if (count($process_queue['items']) == 0 && empty($process_queue['next_page'])) {
				$end_of_posts = true;
				$this->write_log("end of posts.");
			}
			if (empty($meta_vals['scrape_post_unlimited'][0]) && !empty($meta_vals['scrape_post_limit'][0]) && $process_queue['number_of_posts'] == $meta_vals['scrape_post_limit'][0]) {
				$post_limit_reached = true;
				$this->write_log("post limit reached.");
			}
			$this->write_log("repeat count: " . $process_queue['repeat_count']);
			if (!empty($meta_vals['scrape_finish_repeat']) && $process_queue['repeat_count'] == $meta_vals['scrape_finish_repeat'][0]) {
				$repeat_limit_reached = true;
				$this->write_log("enable loop repeat limit reached.");
			}
			
			if ($end_of_posts || $post_limit_reached || $repeat_limit_reached) {
				update_post_meta($post_id, 'scrape_workstatus', 'waiting');
				update_post_meta($post_id, "scrape_end_time", current_time('mysql'));
				
				if( empty($meta_vals['scrape_continue_type'][0]) ) {
					delete_post_meta($post_id, 'scrape_last_url');
				} elseif( count($process_queue['items']) != 0 && !empty($meta_vals['scrape_continue_type'][0]) ) {					
					update_post_meta($post_id, 'scrape_last_items', $process_queue['items']);
				}
				
				if (empty($meta_vals['scrape_run_unlimited'][0]) && get_post_meta($post_id, 'scrape_run_count', true) >= get_post_meta($post_id, 'scrape_run_limit', true)) {
					$timestamp = wp_next_scheduled("scrape_event", array($post_id));
					wp_unschedule_event($timestamp, "scrape_event", array($post_id));
					wp_clear_scheduled_hook("scrape_event", array($post_id));
					$this->write_log("run count reached, deleting task from schedules.");
				}
				$this->write_log("$post_id task ended");
				return;
			}
			
			update_post_meta($post_id, 'scrape_queue', wp_slash($process_queue));
			
			sleep($meta_vals['scrape_waitpage'][0]);
			$nonce = wp_create_nonce('process_task_queue');
			update_post_meta($post_id, 'scrape_nonce', $nonce);
//			delete_transient('lock_' . $post_id);
			wp_remote_get(add_query_arg(array(
				'action' => 'process_task_queue', 'nonce' => $nonce, 'post_id' => $post_id, 'variables' => $_POST
			), admin_url('admin-ajax.php')), array(
				'timeout' => 3, 'blocking' => false, 'sslverify' => false,
			));
			$this->write_log("non blocking admin ajax called exiting");
		} else {
			$this->write_log('nonce failed, not trusted request');
		}
		wp_die();
	}
	
	public function queue() {
		add_action('wp_ajax_nopriv_' . 'process_task_queue', array($this, 'process_task_queue'));
	}
	
	public function execute_post_task($post_id) {
		global $meta_vals;

		if (class_exists('OLSC_License') && !OLSC_License::is_valid()) {
			$this->write_log('插件未授权，任务已终止。post_id=' . $post_id, true);
			return;
		}
		
		if (Scrapes_Plugin_SDK::is_activated() === true) {
			${"\x47\x4c\x4f\x42\x41L\x53"}["\x64\x6b\x73fkn"] = "\x70o\x73\x74\x5fi\x64";
			${"\x47\x4cO\x42A\x4c\x53"}["\x75nl\x76\x6e\x72\x67\x74\x70\x67\x76"] = "\x74\x61\x73\x6b\x5f\x69\x64";
			self::${${"\x47\x4cO\x42\x41\x4c\x53"}["un\x6c\x76n\x72g\x74\x70gv"]} = ${${"G\x4cOBAL\x53"}["\x64\x6b\x73\x66\x6bn"]};
		}
		$meta_vals = get_post_meta($post_id);
			
		$this->write_log("$post_id id task starting...");
		clean_post_cache($post_id);
		//clean_post_meta($post_id);
	
		if (empty($meta_vals['scrape_run_unlimited'][0]) && !empty($meta_vals['scrape_run_count']) && !empty($meta_vals['scrape_run_limit']) && $meta_vals['scrape_run_count'][0] >= $meta_vals['scrape_run_limit'][0]) {
			$this->write_log("run count limit reached. task returns");
			return;
		}
		if (!empty($meta_vals['scrape_workstatus']) && $meta_vals['scrape_workstatus'][0] == 'running' && $meta_vals['scrape_stillworking'][0] == 'wait') {
			$this->write_log($post_id . " wait until finish is selected. returning");
			return;
		}
		if (!empty($meta_vals['scrape_sync_run'][0])) {
			
			$run_count = get_post_meta($post_id, 'scrape_run_count', true);
			
			if( !empty($run_count) && $run_count != 0 ) {
				// Get Available Scrapes
				$all_tasks = get_posts(array(
					'numberposts' => -1, 'post_type' => 'scrape', 'post_status' => 'publish'
				));
				
				foreach ($all_tasks as $task) {
					$scrape_ID = $task->ID;
					$workstatus = get_post_meta($scrape_ID, 'scrape_workstatus', true);
					
					if( !empty($workstatus) && $workstatus == 'running' ) {
						$this->write_log($scrape_ID . " id task from scrape running. wait for the run to finish. returning ");
						return;
					}
				}
			}
		}
		
		$start_time = current_time('mysql');
		$modify_time = get_post_modified_time('U', null, $post_id);
		update_post_meta($post_id, "scrape_start_time", $start_time);
		update_post_meta($post_id, "scrape_end_time", '');
		update_post_meta($post_id, 'scrape_workstatus', 'running');
		$queue_items = array(
			'items' => array(), 'meta_vals' => $meta_vals, 'repeat_count' => 0, 'number_of_posts' => 0, 'page_no' => 1, 'start_time' => $start_time, 'modify_time' => $modify_time, 'next_page' => null
		);
		
		if ($meta_vals['scrape_type'][0] == 'single') {
			$queue_items['items'][] = array(
				'url' => $meta_vals['scrape_url'][0], 'rss_item' => null
			);
			update_post_meta($post_id, 'scrape_queue', wp_slash($queue_items));
		} else {
			if ($meta_vals['scrape_type'][0] == 'feed') {
				$this->write_log("rss xml download");
				$args = $this->return_html_args($meta_vals);
				$url = $meta_vals['scrape_url'][0];
				$response = wp_remote_get($url, $args);
				if (!isset($response->errors)) {
					$body = wp_remote_retrieve_body($response);
					$charset = $this->detect_feed_encoding_and_replace(wp_remote_retrieve_header($response, "Content-Type"), $body);
					$body = iconv($charset, "UTF-8//IGNORE", $body);
					if ($body === false) {
						$this->write_log("UTF8 Convert error from charset:" . $charset);
					}
					
					if (function_exists('tidy_repair_string')) {
						$body = tidy_repair_string($body, array(
							'output-xml' => true, 'input-xml' => true
						), 'utf8');
					}
					
					$xml = simplexml_load_string($body);
					
					if ($xml === false) {
						$this->write_log(libxml_get_errors(), true);
						libxml_clear_errors();
					}
					
					$namespaces = $xml->getNamespaces(true);
					
					$feed_type = $xml->getName();
					
					$feed_image = '';
					if ($feed_type == 'rss') {
						$items = $xml->channel->item;
						if (isset($xml->channel->image)) {
							$feed_image = $xml->channel->image->url;
						}
					} else {
						if ($feed_type == 'feed') {
							$items = $xml->entry;
							$feed_image = (!empty($xml->logo) ? $xml->logo : $xml->icon);
						} else {
							if ($feed_type == 'RDF') {
								$items = $xml->item;
								$feed_image = $xml->channel->image->attributes($namespaces['rdf'])->resource;
							}
						}
					}

					foreach ($items as $item) {
						
						$post_date = '';
						if ($feed_type == 'rss') {
							$post_date = $item->pubDate;
						} else {
							if ($feed_type == 'feed') {
								$post_date = $item->published;
							} else {
								if ($feed_type == 'RDF') {
									$post_date = $item->children($namespaces['dc'])->date;
								}
							}
						}
						
						$post_date = date('Y-m-d H:i:s', strtotime($post_date));
						
						if ($feed_type != 'feed') {
							$post_content = html_entity_decode($item->description, ENT_COMPAT, "UTF-8");
							$original_html_content = $post_content;
						} else {
							$post_content = html_entity_decode($item->content, ENT_COMPAT, "UTF-8");
							$original_html_content = $post_content;
						}
						
						if ($meta_vals['scrape_allowhtml'][0] != 'on') {
							$post_content = wp_strip_all_tags($post_content);
						}
						
						$post_content = trim($post_content);
						
						if (isset($namespaces['media'])) {
							$media = $item->children($namespaces['media']);
						} else {
							$media = $item->children();
						}
						
						if (isset($media->content) && $feed_type != 'feed') {
							$this->write_log("image from media:content");
							$url = (string)$media->content->attributes()->url;
							$featured_image_url = $url;
						} else {
							if (isset($media->thumbnail)) {
								$this->write_log("image from media:thumbnail");
								$url = (string)$media->thumbnail->attributes()->url;
								$featured_image_url = $url;
							} else {
								if (isset($item->enclosure)) {
									$this->write_log("image from enclosure");
									$url = (string)$item->enclosure['url'];
									$featured_image_url = $url;
								} else {
									if (isset($item->description) || (isset($item->content) && $feed_type == 'feed')) {
										$item_content = (isset($item->description) ? $item->description : $item->content);
										$this->write_log("image from description");
										$doc = new DOMDocument();
										$doc->preserveWhiteSpace = false;
										@$doc->loadHTML('<?xml encoding="utf-8" ?>' . html_entity_decode($item_content));
										
										$imgs = $doc->getElementsByTagName('img');
										
										if ($imgs->length) {
											$featured_image_url = $imgs->item(0)->attributes->getNamedItem('src')->nodeValue;
										}
									} else {
										if (!empty($feed_image)) {
											$this->write_log("image from channel");
											$featured_image_url = $feed_image;
										}
									}
								}
							}
						}
						
						$rss_item = array(
							'post_date' => strval($post_date), 'post_content' => strval($post_content), 'post_original_content' => $original_html_content, 'featured_image' => $this->create_absolute_url(strval($featured_image_url), $url, null), 'post_title' => strval($item->title)
						);
						if ($feed_type == 'feed') {
							$alternate_found = false;
							foreach ($item->link as $link) {
								$this->write_log($link->attributes()->rel);
								if ($link->attributes()->rel == 'alternate') {
									$single_url = strval($link->attributes()->href);
									$this->write_log('found alternate attribute link: ' . $single_url);
									$alternate_found = true;
								}
							}
							if (!$alternate_found) {
								$single_url = strval($item->link->attributes()->href);
							}
						} else {
							$single_url = strval($item->link);
						}
						
						$this->write_log('foreach items');
						$this->write_log($single_url);
						$this->write_log($rss_item);
						
						$queue_items['items'][] = array(
							'url' => $single_url, 'rss_item' => $rss_item
						);
					}
					
					update_post_meta($post_id, 'scrape_queue', wp_slash($queue_items));
				} else {
					$this->write_log($post_id . " http error:" . $response->get_error_message());
					if ($meta_vals['scrape_onerror'][0] == 'stop') {
						$this->write_log($post_id . " on error chosen stop. returning code " . $response->get_error_message(), true);
						return;
					}
				}
			} else {
				if ($meta_vals['scrape_type'][0] == 'list') {
					$args = $this->return_html_args($meta_vals);
					if (!empty($meta_vals['scrape_last_url']) && $meta_vals['scrape_run_type'][0] == 'continue') {
						$this->write_log("continues from last stopped url" . $meta_vals['scrape_last_url'][0]);
						$meta_vals['scrape_url'][0] = $meta_vals['scrape_last_url'][0];
					}
					
					$this->write_log("Serial scrape starts at URL:" . $meta_vals['scrape_url'][0]);
					
					$response = wp_remote_get($meta_vals['scrape_url'][0], $args);
					update_post_meta($post_id, 'scrape_last_url', $meta_vals['scrape_url'][0]);
					
					if (!isset($response->errors)) {
						$body = wp_remote_retrieve_body($response);
						$body = trim($body);
						
						if (substr($body, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
							$body = substr($body, 3);
						}
						
						$charset = $this->detect_html_encoding_and_replace(wp_remote_retrieve_header($response, "Content-Type"), $body);
						$body_iconv = iconv($charset, "UTF-8//IGNORE", $body);
						
						$body_preg = '<?xml encoding="utf-8" ?>' . preg_replace(array(
								"/<!--.*?-->/isu", '/(<table([^>]+)?>([^<>]+)?)(?!<tbody([^>]+)?>)/isu', '/(<(?!(\/tbody))([^>]+)?>)(<\/table([^>]+)?>)/isu', "'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'isu", "'<\s*script\s*>(.*?)<\s*/\s*script\s*>'isu", "'<\s*noscript[^>]*[^/]>(.*?)<\s*/\s*noscript\s*>'isu", "'<\s*noscript\s*>(.*?)<\s*/\s*noscript\s*>'isu",
							
							), array(
								"", '$1<tbody>', '$1</tbody>$4', "", "", "", ""
							), $body_iconv);
						
						$doc = new DOMDocument;
						$doc->preserveWhiteSpace = false;
						$body_preg = mb_convert_encoding($body_preg, 'HTML-ENTITIES', 'UTF-8');
						@$doc->loadHTML($body_preg);
						
						$url = parse_url($meta_vals['scrape_url'][0]);
						$url = $url['scheme'] . "://" . $url['host'];
						$base = $doc->getElementsByTagName('base')->item(0);
						$html_base_url = null;
						if (!is_null($base)) {
							$html_base_url = $this->create_absolute_url($base->getAttribute('href'), $url, null);
						}
						
						$xpath = new DOMXPath($doc);
						
						$next_buttons = (!empty($meta_vals['scrape_nextpage'][0]) ? $xpath->query($meta_vals['scrape_nextpage'][0]) : new DOMNodeList);
						
						$next_button = false;
						$is_facebook_page = false;
						
						if (parse_url($meta_vals['scrape_url'][0], PHP_URL_HOST) == 'mbasic.facebook.com') {
							$is_facebook_page = true;
						}
						
						$ref_a_element = $xpath->query($meta_vals['scrape_listitem'][0])->item(0);
						if (is_null($ref_a_element)) {
							$this->write_log("Reference a element not found URL:" . $meta_vals['scrape_url'][0] . " XPath: " . $meta_vals['scrape_listitem'][0]);
                            update_post_meta($post_id, 'scrape_workstatus', 'waiting');
                            update_post_meta($post_id, "scrape_end_time", current_time('mysql'));
                            delete_post_meta($post_id, 'scrape_last_url');

                            if (empty($meta_vals['scrape_run_unlimited'][0]) && get_post_meta($post_id, 'scrape_run_count', true) >= get_post_meta($post_id, 'scrape_run_limit', true)) {
                                $timestamp = wp_next_scheduled("scrape_event", array($post_id));
                                wp_unschedule_event($timestamp, "scrape_event", array($post_id));
                                wp_clear_scheduled_hook("scrape_event", array($post_id));
                                $this->write_log("run count reached, deleting task from schedules.");
                            }
                            $this->write_log("$post_id task ended");
                            return;
						}
						$ref_node_path = $ref_a_element->getNodePath();
						$ref_node_no_digits = preg_replace("/\[\d+\]/", "", $ref_node_path);
						$ref_a_children = array();
						foreach ($ref_a_element->childNodes as $node) {
							$ref_a_children[] = $node->nodeName;
						}
						
						$this->write_log("scraping page #" . $queue_items['page_no']);
						
						$all_links = $xpath->query("//a");
						if ($is_facebook_page) {
							$all_links = $xpath->query("//a[text()='" . trim($ref_a_element->textContent) . "']");
						} else {
							if (!empty($meta_vals['scrape_exact_match'][0])) {
								$all_links = $xpath->query($meta_vals['scrape_listitem'][0]);
							}
						}
						
						$single_links = array();
						if (empty($meta_vals['scrape_exact_match'][0])) {
							$this->write_log("serial fuzzy match links");
							foreach ($all_links as $a_elem) {
								
								$parent_path = $a_elem->getNodePath();
								$parent_path_no_digits = preg_replace("/\[\d+\]/", "", $parent_path);
								if ($parent_path_no_digits == $ref_node_no_digits) {
									$children_node_names = array();
									foreach ($a_elem->childNodes as $node) {
										$children_node_names[] = $node->nodeName;
									}
									if ($ref_a_children === $children_node_names) {
										$single_links[] = $a_elem->getAttribute('href');
									}
								}
							}
						} else {
							$this->write_log("serial exact match links");
							foreach ($all_links as $a_elem) {
								$single_links[] = $a_elem->getAttribute('href');
							}
						}
						
						$single_links = array_unique($single_links);
						$this->write_log("number of links:" . count($single_links));
						foreach ($single_links as $k => $single_link) {
							$queue_items['items'][] = array(
								'url' => $this->create_absolute_url($single_link, $meta_vals['scrape_url'][0], $html_base_url), 'rss_item' => null
							);
						}

						if($meta_vals['scrape_nextpage_type'][0] == 'source') {


                            $this->write_log('checking candidate next buttons');
                            foreach ($next_buttons as $btn) {
                                $next_button_text = preg_replace("/\s+/", " ", $btn->textContent);
                                $next_button_text = str_replace(chr(0xC2) . chr(0xA0), " ", $next_button_text);

                                if ($next_button_text == $meta_vals['scrape_nextpage_innerhtml'][0]) {
                                    $this->write_log("next page found");
                                    $next_button = $btn;
                                } else {
                                    $this->write_log($next_button_text . ' ' . $meta_vals['scrape_nextpage_innerhtml'][0] . ' does not match');
                                }
                            }
                            $next_link = null;
                            if ($next_button) {
                                $next_link = $this->create_absolute_url($next_button->getAttribute('href'), $meta_vals['scrape_url'][0], $html_base_url);
                            }
                        } else {
                            $query = parse_url($meta_vals['scrape_url'][0], PHP_URL_QUERY);
                            $names = unserialize($meta_vals['scrape_next_page_url_parameters_names'][0]);
                            $values = unserialize($meta_vals['scrape_next_page_url_parameters_values'][0]);
                            $increments = unserialize($meta_vals['scrape_next_page_url_parameters_increments'][0]);

                            $build_query = array();

                            for($i = 0; $i < count($names); $i++) {
                                $build_query[$names[$i]] = $values[$i] + ($increments[$i] * (1));
                            }
                            if ($query) {
                                $next_link = $meta_vals['scrape_url'][0] . "&" . http_build_query($build_query);
                            } else {
                                $next_link = $meta_vals['scrape_url'][0] . "?" . http_build_query($build_query);
                            }
                        }
						
						
						$this->write_log("next link is: " . $next_link);
						$queue_items['next_page'] = $next_link;
						update_post_meta($post_id, 'scrape_queue', wp_slash($queue_items));
					} else {
						$this->write_log($post_id . " http error in url " . $meta_vals['scrape_url'][0] . " : " . $response->get_error_message(), true);
						if ($meta_vals['scrape_onerror'][0] == 'stop') {
							$this->write_log($post_id . " on error chosen stop. returning code ", true);
							return;
						}
					}
				}
			}
		}
		
		$nonce = wp_create_nonce('process_task_queue');
		update_post_meta($post_id, 'scrape_nonce', $nonce);
		
		update_post_meta($post_id, "scrape_run_count", $meta_vals['scrape_run_count'][0] + 1);
		
		$this->write_log("$post_id id task queued...");
		
		wp_remote_get(add_query_arg(array('action' => 'process_task_queue', 'nonce' => $nonce, 'post_id' => $post_id, 'variables' => $_POST), admin_url('admin-ajax.php')), array(
			'timeout' => 3, 'blocking' => false, 'sslverify' => false
		));
		
	}
	
	public function single_scrape($url, $meta_vals, &$repeat_count = 0, $rss_item = null) {
	    error_log("=== SINGLE_SCRAPE 函数被调用，URL: " . $url);
		if (class_exists('OLSC_License') && !OLSC_License::is_valid()) {
			$this->write_log('插件未授权，已跳过采集：' . $url, true);
			return;
		}
		$this->xk_link_cache = array();
		$this->xk_emitted = array();
		global $wpdb, $new_id, $post_arr, $doc;
		
		update_post_meta($meta_vals['scrape_task_id'][0], 'scrape_last_scrape', current_time('mysql'));
		
		$args = $this->return_html_args($meta_vals);
		
		$is_facebook_page = false;
		$is_amazon = false;
		
		if (parse_url($url, PHP_URL_HOST) == 'mbasic.facebook.com') {
			$is_facebook_page = true;
		}
		
		if (preg_match("/(\/|\.)amazon\./", $meta_vals['scrape_url'][0])) {
			$is_amazon = true;
		}
		                // ========== 智能请求：使用无头浏览器 ==========
                error_log("【Puppeteer调试】进入 single_scrape，URL: " . $url);
                $use_puppeteer = false;
                
                // 需要无头浏览器的文章ID
                $need_puppeteer_ids = [4081, 15346];
                
                if (preg_match('/archives/(\d+)/', $url, $id_match)) {
                    error_log("【Puppeteer调试】匹配到文章ID: " . $id_match[1]);
                    if (in_array((int)$id_match[1], $need_puppeteer_ids)) {
                        $use_puppeteer = true;
                        error_log("【Puppeteer】文章ID {$id_match[1]} 使用无头浏览器");
                    }
                }
                
                error_log("【Puppeteer调试】use_puppeteer = " . ($use_puppeteer ? 'true' : 'false'));
                
                if ($use_puppeteer) {
                    $cache_key = 'puppeteer_' . md5($url);
                    $cached = get_transient($cache_key);
                    
                    if (!empty($cached)) {
                        error_log("【Puppeteer】使用缓存");
                        $response = array(
                            'body' => $cached,
                            'response' => array('code' => 200)
                        );
                    } else {
                        $cmd = 'node /www/wwwroot/awdj_top/puppeteer/render.js ' . escapeshellarg($url) . ' 2>&1';
                        error_log("【Puppeteer】执行命令: " . $cmd);
                        $html = shell_exec($cmd);
                        error_log("【Puppeteer】命令执行完成，输出长度: " . strlen($html));
                        
                        if (!empty($html) && strlen($html) > 5000) {
                            $response = array(
                                'body' => $html,
                                'response' => array('code' => 200)
                            );
                            set_transient($cache_key, $html, 24 * HOUR_IN_SECONDS);
                            error_log("【Puppeteer】成功获取HTML，长度: " . strlen($html));
                        } else {
                            error_log("【Puppeteer】失败，降级普通请求");
                            $response = wp_remote_get($url, $args);
                        }
                    }
                } else {
                    error_log("【Puppeteer】未启用，使用普通请求");
                    $response = wp_remote_get($url, $args);
                }
                // ========== 智能请求结束 ==========

		$scrape_count = get_site_option('ol_scrapes_scrape_count', ['current' => 0, 'total' => 0]);
		$scrape_count['total']++;
		update_site_option('ol_scrapes_scrape_count', $scrape_count);
		
		if (!isset($response->errors)) {
			$this->write_log("Single scraping started: " . $url);
			$body = $response['body'];
			$body = trim($body);
			
			if (substr($body, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
				$body = substr($body, 3);
			}
			
			$charset = $this->detect_html_encoding_and_replace(wp_remote_retrieve_header($response, "Content-Type"), $body);			
			$body_iconv = iconv($charset, "UTF-8//IGNORE", $body);
			$this->current_page_html = $body_iconv;
			$this->xk_endpoint_cache = null;
			unset($body);
			$body_preg = preg_replace(array(
				"/<!--.*?-->/isu", '/(<table([^>]+)?>([^<>]+)?)(?!<tbody([^>]+)?>)/isu', '/(<(?!(\/tbody))([^>]+)?>)(<\/table([^>]+)?>)/isu', "'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'isu", "'<\s*script\s*>(.*?)<\s*/\s*script\s*>'isu", "'<\s*noscript[^>]*[^/]>(.*?)<\s*/\s*noscript\s*>'isu", "'<\s*noscript\s*>(.*?)<\s*/\s*noscript\s*>'isu",
			
			), array(
				"", '$1<tbody>', '$1</tbody>$4', "", "", "", ""
			), $body_iconv);
			unset($body_iconv);
			$doc = new DOMDocument;
			//DOMObject('body');
				
			$doc->preserveWhiteSpace = false;
			$body_preg = mb_convert_encoding($body_preg, 'HTML-ENTITIES', 'UTF-8');
			@$doc->loadHTML('<?xml encoding="utf-8" ?>' . $body_preg);

			${"G\x4cO\x42A\x4c\x53"}["\x64hp\x65\x62\x79\x6ds"] = "\x78\x70\x61\x74\x68";
			if (Scrapes_Plugin_SDK::is_activated() === true) {
				$gnzcwtbppmph = "\x64\x6fc";
				${${"\x47\x4c\x4f\x42\x41\x4c\x53"}["d\x68\x70eb\x79\x6d\x73"]} = new DOMXPath(${$gnzcwtbppmph});
			}
			
			$parsed_url = parse_url($meta_vals['scrape_url'][0]);
			$parsed_url = $parsed_url['scheme'] . "://" . $parsed_url['host'];
			$base = $doc->getElementsByTagName('base')->item(0);
			$html_base_url = null;
			if (!is_null($base)) {
				$html_base_url = $this->create_absolute_url($base->getAttribute('href'), $parsed_url, null);
			}
			
			$ID = 0;
			
			$post_type = $meta_vals['scrape_post_type'][0];
			$enable_translate = !empty($meta_vals['scrape_translate_enable'][0]);
			if ($enable_translate) {
				$source_language = $meta_vals['scrape_translate_source'][0];
				$target_language = $meta_vals['scrape_translate_target'][0];
			}

			$enable_spin = !empty($meta_vals['scrape_spin_enable'][0]);
            if ($enable_spin) {
                $spin_email = $meta_vals['scrape_spin_email'][0];
                $spin_password = $meta_vals['scrape_spin_password'][0];
            }
			
			$post_date_type = $meta_vals['scrape_date_type'][0];
			if ($post_date_type == 'xpath') {
				$post_date = $meta_vals['scrape_date'][0];
				$node = $xpath->query($post_date);
				if ($node->length) {
					
					$node = $node->item(0);
					$post_date = $node->nodeValue;
					if (!empty($meta_vals['scrape_date_regex_status'][0])) {
						$regex_finds = unserialize($meta_vals['scrape_date_regex_finds'][0]);
						$regex_replaces = unserialize($meta_vals['scrape_date_regex_replaces'][0]);
						$combined = array_combine($regex_finds, $regex_replaces);
						foreach ($combined as $regex => $replace) {
							$post_date = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_date);
						}
						$this->write_log("date after regex:" . $post_date);
					}
					if ($is_facebook_page) {
						$this->write_log("facebook date original " . $post_date);
						if (preg_match_all("/just now/i", $post_date, $matches)) {
							$post_date = current_time('mysql');
						} else {
							if (preg_match_all("/(\d{1,2}) min(ute)?(s)?/i", $post_date, $matches)) {
								$post_date = date("Y-m-d H:i:s", strtotime($matches[1][0] . " minutes ago", current_time('timestamp')));
							} else {
								if (preg_match_all("/(\d{1,2}) h(ou)?r(s)?/i", $post_date, $matches)) {
									$post_date = date("Y-m-d H:i:s", strtotime($matches[1][0] . " hours ago", current_time('timestamp')));
								} else {
									$post_date = str_replace("Yesterday", date("F j, Y", strtotime("-1 day", current_time('timestamp'))), $post_date);
									if (!preg_match("/\d{4}/i", $post_date)) {
										$at_position = strpos($post_date, "at");
										if ($at_position !== false) {
											if (in_array(substr($post_date, 0, $at_position - 1), array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"))) {
												$post_date = date("F j, Y", strtotime("last " . substr($post_date, 0, $at_position - 1), current_time('timestamp'))) . " " . substr($post_date, $at_position + 2);
											} else {
												$post_date = substr($post_date, 0, $at_position) . " " . date("Y") . " " . substr($post_date, $at_position + 2);
											}
											
										} else {
											$post_date .= " " . date("Y");
										}
										
									}
								}
							}
						}
						$this->write_log("after facebook $post_date");
					}
					if(function_exists('convert_to_english')) {
						$option_ts = (get_option('ts_page_settings_is_active',true));
						if($option_ts['ts_active_jalali_date'] != 'off') {
							$post_date = convert_to_english($post_date);
						}
					}					
					$tmp_post_date = $post_date;
					$post_date = date_parse($post_date);
					if (!is_integer($post_date['year']) || !is_integer(($post_date['month'])) || !is_integer($post_date['day'])) {
						$this->write_log("date can not be parsed correctly. trying translations");
						$post_date = $tmp_post_date;
						$post_date = $this->translate_months($post_date);
						$this->write_log("date value: " . $post_date);
						$post_date = date_parse($post_date);
						if (!is_integer($post_date['year']) || !is_integer(($post_date['month'])) || !is_integer($post_date['day'])) {
							$this->write_log("translation is not accepted valid");
							$post_date = '';
						} else {
							$this->write_log("translation is accepted valid");
							if(class_exists('TS_Options')) {
								if(function_exists('wp_date')) {
									$post_time = array('hour' => $post_date['hour'], 'min' => $post_date['minute'], 'sec' => $post_date['second']);
									$post_date = date("Y-m-d H:i:s", mktime($post_date['hour'], $post_date['minute'], $post_date['second'], $post_date['month'], $post_date['day'], $post_date['year']));
									$save_post_date_jalali = $post_date;
									$post_date = za_jalali_to_geo($post_date, $post_time['hour'], $post_time['min'], $post_time['sec']);
								}
							} else {
								$post_date = date("Y-m-d H:i:s", mktime($post_date['hour'], $post_date['minute'], $post_date['second'], $post_date['month'], $post_date['day'], $post_date['year']));
							}
						}
					} else {
						$this->write_log("date parsed correctly");
						$post_date = date("Y-m-d H:i:s", mktime($post_date['hour'], $post_date['minute'], $post_date['second'], $post_date['month'], $post_date['day'], $post_date['year']));
						if(class_exists('TS_Options')) {
							if(function_exists('wp_date') && $option_ts['ts_active_jalali_date'] != 'off') {
								$save_post_date_jalali = $post_date;
								$post_time = explode(' ', $post_date);
								$post_time = explode(':', $post_time[1]);
								$post_date = za_jalali_to_geo($post_date, $post_time[0], $post_time[1], $post_time[2]);
								$this->write_log("date value: " . $post_date);
							}
						}						
					}
				} else {
					$post_date = '';
					$this->write_log("URL: " . $url . " XPath: " . $meta_vals['scrape_date'][0] . " returned empty for post date", true);
				}
			} else {
				if ($post_date_type == 'runtime') {
					$post_date = current_time('mysql');
					if(class_exists('TS_Options')) {
						if(function_exists('wp_date')) {
							$save_post_date_jalali = za_geo_to_jalali($post_date);
						}
					}					
				} else {
					if ($post_date_type == 'custom') {
						$post_date = $meta_vals['scrape_date_custom'][0];
						if(class_exists('TS_Options')) {
							if(function_exists('wp_date')) {
								$save_post_date_jalali = $post_date;
								$post_time = explode(' ', $post_date);
								$post_time = explode(':', $post_time[1]);
								$post_date = za_jalali_to_geo($post_date, $post_time[0], $post_time[1], $post_time[2]);
							}
						}						
					} else {
						if ($post_date_type == 'feed') {
							$post_date = $rss_item['post_date'];
						} else {
							$post_date = '';
						}
					}
				}
			}
			
			$post_meta_names = array();
			$post_meta_values = array();
			$post_meta_attributes = array();
			$post_meta_templates = array();
			$post_meta_regex_finds = array();
			$post_meta_regex_replaces = array();
			$post_meta_regex_statuses = array();
			$post_meta_template_statuses = array();
			$post_meta_allowhtmls = array();
			$post_meta_dont_updates = array();
			
			if (!empty($meta_vals['scrape_custom_fields'])) {
				$scrape_custom_fields = unserialize($meta_vals['scrape_custom_fields'][0]);
				foreach ($scrape_custom_fields as $timestamp => $arr) {
					$post_meta_names[] = $arr["name"];
					$post_meta_values[] = $arr["value"];
					$post_meta_attributes[] = $arr["attribute"];
					$post_meta_templates[] = $arr["template"];
					$post_meta_regex_finds[] = isset($arr["regex_finds"]) ? $arr["regex_finds"] : array();
					$post_meta_regex_replaces[] = isset($arr["regex_replaces"]) ? $arr["regex_replaces"] : array();
					$post_meta_regex_statuses[] = $arr['regex_status'];
					$post_meta_template_statuses[] = $arr['template_status'];
					$post_meta_allowhtmls[] = $arr['allowhtml'];
					$post_meta_dont_updates[] = $arr['dont_update'];
				}
			}
			
			$post_meta_name_values = array();
			if (!empty($post_meta_names) && !empty($post_meta_values)) {
				$post_meta_name_values = array_combine($post_meta_names, $post_meta_values);
			}
			
			$meta_input = array();
			
			$woo_active = false;
			$woo_price_metas = array('_price', '_sale_price', '_regular_price');
			$woo_decimal_metas = array('_height', '_length', '_width', '_weight');
			$woo_integer_metas = array('_download_expiry', '_download_limit', '_stock', 'total_sales', '_download_expiry', '_download_limit');
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			if (is_plugin_active('woocommerce/woocommerce.php')) {
				$woo_active = true;
			}
			
			$post_meta_index = 0;
			foreach ($post_meta_name_values as $key => $value) {
				if (stripos($value, "//") === 0) {
					$node = $xpath->query($value);
					if ($node->length) {
						$node = $node->item(0);
						if(class_exists('TS_Options')) {
							$option_ts = (get_option('ts_page_settings_is_active',true));
							$ts_tab_custom_field = array_key_exists("ts_active_custom_field", $option_ts) ? $option_ts['ts_active_custom_field'] : '';
						}
						if (!empty($post_meta_allowhtmls[$post_meta_index]) || $key == '_ts_meta_size' ||  $key == '_ts_meta_color' || $key == '_ts_meta_custom' || $key == $ts_tab_custom_field) {
							$value = $node->ownerDocument->saveXML($node);
							$value = $this->convert_html_links($value, $url, $html_base_url);
						} else {
							if (!empty($post_meta_attributes[$post_meta_index])) {
								$value = $node->getAttribute($post_meta_attributes[$post_meta_index]);
							} else {
								$value = $node->nodeValue;
							}
						}
						if(function_exists('convert_to_english')){
							if(!empty('_price') || !empty('_sale_price') || !empty('_regular_price') || !empty('_stock')) {
								if($option_ts['ts_active_persian_price'] != 'off'){
									$value = convert_to_english($value);
								}
							}
						}						
						
						$this->write_log("post meta $key : " . (string)$value);
                        if ($enable_spin) {
                            $value = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $value);
                        }
						if ($enable_translate) {
							if (class_exists('TS_Translate')) {
								$value = TS_Translate::translate($source_language, $target_language, $value);
								$value = $this->ordered_html_tag($value);
							}
						}
						
						if (!empty($post_meta_regex_statuses[$post_meta_index])) {
							
							$regex_combined = array_combine($post_meta_regex_finds[$post_meta_index], $post_meta_regex_replaces[$post_meta_index]);
							foreach ($regex_combined as $find => $replace) {
								$this->write_log("custom field value before regex $value");
								$value = preg_replace("/" . str_replace("/", "\/", $find) . "/isu", $replace, $value);
								$this->write_log("custom field value after regex $value");
							}
						}
					} else {
						$this->write_log("post meta $key : found empty.", true);
						$this->write_log("URL: " . $url . " XPath: " . $value . " returned empty for post meta $key", true);
						$value = '';
					}
				}
				
				if ($woo_active && $post_type == 'product') {
					if (in_array($key, $woo_price_metas)) {
						$value = $this->convert_str_to_woo_decimal($value);
					}
					if (in_array($key, $woo_decimal_metas)) {
						$value = floatval($value);
					}
					if (in_array($key, $woo_integer_metas)) {
						$value = intval($value);
					}
				}
				
				if (!empty($post_meta_template_statuses[$post_meta_index])) {
					$template_value = $post_meta_templates[$post_meta_index];
					$affiliate = $this->scrape_url_shorten($url);
					$affiliate = base64_encode($affiliate);
					$value = str_replace("[scrape_value]", $value, $template_value);
					if(class_exists('TS_Options')) {
						if(function_exists('wp_date')) {					
							$value = str_replace("[scrape_date]", $save_post_date_jalali, $value);
						}
					} else {
						$value = str_replace("[scrape_date]", $post_date, $value);
					}
					$value = str_replace("[scrape_url]", $url, $value);
					$value = str_replace("[affiliate]", $affiliate, $value);
					
					preg_match_all('/\[scrape_meta name="([^"]*)"\]/', $value, $matches);
					
					$full_matches = $matches[0];
					$name_matches = $matches[1];
					if (!empty($full_matches)) {
						$combined = array_combine($name_matches, $full_matches);
						
						foreach ($combined as $meta_name => $template_string) {
							$val = $meta_input[$meta_name];
							$value = str_replace($template_string, $val, $value);
						}
					}
					
					if (preg_match('/calc\((.*)\)/isu', $value, $matches)) {
						$full_text = $matches[0];
						$text = $matches[1];
						$calculated = $this->template_calculator($text);
						$value = str_replace($full_text, $calculated, $value);
					}
					
					if (preg_match('/\/([a-zA-Z0-9]{10})(?:[\/?]|$)/', $url, $matches)) {
						$value = str_replace("[scrape_asin]", $matches[1], $value);
					}
					
				}
				
				$meta_input[$key] = $value;
				$post_meta_index++;
				
				$this->write_log("final meta for " . $key . " is " . $value);
			}
			
			if ($woo_active && $post_type == 'product') {
				if (empty($meta_input['_price'])) {
					if (!empty($meta_input['_sale_price']) || !empty($meta_input['_regular_price'])) {
						$meta_input['_price'] = !empty($meta_input['_sale_price']) ? $meta_input['_sale_price'] : $meta_input['_regular_price'];
					}
				}
				if ( !empty($meta_input['_sale_price']) && !empty($meta_input['_regular_price']) ) {
					if( $meta_input['_sale_price'] == $meta_input['_regular_price'] ) {
						$meta_input['_sale_price'] = '';
					}
				}
				if (empty($meta_input['_visibility'])) {
					$meta_input['_visibility'] = 'visible';
				}
				if (empty($meta_input['_manage_stock'])) {
					$meta_input['_manage_stock'] = 'no';
					$meta_input['_stock_status'] = 'instock';
				}
				if (empty($meta_input['total_sales'])) {
					$meta_input['total_sales'] = 0;
				}
                if(empty($meta_input['_stock']) && empty($meta_input['_regular_price']) && empty($meta_input['_price'])) {
                    $meta_input['_manage_stock'] = 'no';
                    $meta_input['_stock_status'] = 'outofstock';
                }				
				if(!empty($meta_input['_product_attributes']) && !empty($meta_input['_ts_meta_size']) && empty($meta_input['_ts_meta_color'])) {
					$meta_attributes = $meta_input['_product_attributes'];
					$meta_box = $meta_input['_ts_meta_size'];
					$meta_box = preg_match_all('/(?<=>)\n*([^<]+)/', $meta_box, $matches);
					$meta_box = array_filter(array_map('trim', $matches[1]));
					$meta_box = array_unique($meta_box);
					$product_attributes = array($meta_attributes => $meta_box);
				}
				if(!empty($meta_input['_product_attributes']) && empty($meta_input['_ts_meta_size']) && !empty($meta_input['_ts_meta_color'])) {
					$meta_attributes = $meta_input['_product_attributes'];
					$meta_box = $meta_input['_ts_meta_color'];
					$meta_box = preg_match_all('/(?<=>)\n*([^<]+)/', $meta_box, $matches);
					$meta_box = array_filter(array_map('trim', $matches[1]));
					$meta_box = array_unique($meta_box);
					$meta_box = array_values($meta_box);
					$product_attributes = array($meta_attributes => $meta_box);
				}
				//$product_attributes = array_values($product_attributes);
				if(is_array($meta_box) && !empty($meta_box)) {
					if(!empty($meta_input['_ts_meta_size'])) {
						$meta_input['_ts_meta_size'] = __('The value received was successfully recorded!', 'ol-scrapes');
					}
					if(!empty($meta_input['_ts_meta_color'])) {
						$meta_input['_ts_meta_color'] = __('The value received was successfully recorded!', 'ol-scrapes');
					}
				} elseif(!empty($meta_input['_ts_meta_size']) && empty($meta_input['_product_attributes'])) {
					$meta_input['_ts_meta_size'] = __('The value received failed!', 'ol-scrapes');
				} elseif(!empty($meta_input['_ts_meta_color']) && empty($meta_input['_product_attributes'])) {
					$meta_input['_ts_meta_color'] = __('The value received failed!', 'ol-scrapes');

				} elseif(!empty($meta_input['_product_attributes']) && !empty($meta_input['_ts_meta_size']) && !empty($meta_input['_ts_meta_color'])) {
					$meta_input['_ts_meta_size'] = __('At present, both color and size are not received at the same time!', 'ol-scrapes');
					$meta_input['_ts_meta_color'] = __('At present, both color and size are not received at the same time!', 'ol-scrapes');
				} else {
					if(!empty($meta_input['_ts_meta_size'])) {
						$meta_input['_ts_meta_size'] = __('The value received failed!', 'ol-scrapes');
					}
					if(!empty($meta_input['_ts_meta_color'])) {
						$meta_input['_ts_meta_color'] = __('The value received failed!', 'ol-scrapes');
					}
				}				
			}
			
			$post_title = $this->trimmed_templated_value('scrape_title', $meta_vals, $xpath, $post_date, $save_post_date_jalali, $url, $meta_input, $rss_item);
			$this->write_log($post_title);
			
			$post_content_type = $meta_vals['scrape_content_type'][0];
			
			if ($post_content_type == 'auto') {
				$post_content = $this->convert_readable_html($body_preg);
				$original_html_content = $post_content;
				if ($enable_spin) {
				    $post_content = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $post_content);
                }
				if ($enable_translate) {
					if (class_exists('TS_Translate')) {
					    $post_content = TS_Translate::translate($source_language, $target_language, $post_content);
						$post_content = $this->ordered_html_tag($post_content);
				    }
				}				
				$post_content = $this->convert_html_links($post_content, $url, $html_base_url, $post_title);
				
				if($enable_translate) {
					$post_content = $this->replace_img_org_to_translate($post_content, $original_html_content);
				}				
				if (!empty($meta_vals['scrape_data_src_images'][0])) {
					$post_content = $this->fix_image_link_content($post_content, $original_html_content);
				}
				if(!empty($meta_vals['scrape_delete_all_links'][0])) {
					$post_content = preg_replace("/<\/?a(|\s+[^>]+)>/", '', $post_content);
				}
				
				if (!empty($meta_vals['scrape_content_regex_finds'])) {
					$regex_finds = unserialize($meta_vals['scrape_content_regex_finds'][0]);
					$regex_replaces = unserialize($meta_vals['scrape_content_regex_replaces'][0]);
					$combined = array_combine($regex_finds, $regex_replaces);
					foreach ($combined as $regex => $replace) {
						
						$this->write_log("content regex $regex");
						$this->write_log("content replace $replace");
						
						$this->write_log("regex before content");
						$this->write_log($post_content);
						$post_content = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_content);
						$this->write_log("regex after content");
						$this->write_log($post_content);
					}
				}
								
				if (empty($meta_vals['scrape_allowhtml'][0])) {
					$post_content = wp_strip_all_tags($post_content);
				}
			} else {
				if ($post_content_type == 'xpath') {
					$node = $xpath->query($meta_vals['scrape_content'][0]);
					if ($node->length) {
						$node = $node->item(0);
						$post_content = $node->ownerDocument->saveXML($node);
						$original_html_content = $post_content;
						if ($enable_spin) {
						    $post_content = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $post_content);
                        }
						if ($enable_translate) {
							if (class_exists('TS_Translate')) {
							    $post_content = TS_Translate::translate($source_language, $target_language, $post_content);
								$post_content = $this->ordered_html_tag($post_content);
							}
						}
						$post_content = $this->convert_html_links($post_content, $url, $html_base_url, $post_title);
						
						if($enable_translate) {
							$post_content = $this->replace_img_org_to_translate($post_content, $original_html_content);
						}				
						if (!empty($meta_vals['scrape_data_src_images'][0])) {
							$post_content = $this->fix_image_link_content($post_content, $original_html_content);
						}
						if(!empty($meta_vals['scrape_delete_all_links'][0])) {
							$post_content = preg_replace("/<\/?a(|\s+[^>]+)>/", '', $post_content);
						}						
						
						if (!empty($meta_vals['scrape_content_regex_finds'])) {
							$regex_finds = unserialize($meta_vals['scrape_content_regex_finds'][0]);
							$regex_replaces = unserialize($meta_vals['scrape_content_regex_replaces'][0]);
							$combined = array_combine($regex_finds, $regex_replaces);
							foreach ($combined as $regex => $replace) {
								$this->write_log("content regex $regex");
								$this->write_log("content replace $replace");
								
								$this->write_log("regex before content");
								$this->write_log($post_content);
								$post_content = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_content);
								$this->write_log("regex after content");
								$this->write_log($post_content);
							}
						}
						
						if (empty($meta_vals['scrape_allowhtml'][0])) {
							$post_content = wp_strip_all_tags($post_content);
						}
					} else {
						$this->write_log("URL: " . $url . " XPath: " . $meta_vals['scrape_content'][0] . " returned empty for post content", true);
						$post_content = '';
						$original_html_content = '';
					}
				} else {
					if ($post_content_type == 'feed') {
						$post_content = $rss_item['post_content'];
						if ($enable_spin) {
						    $post_content = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $post_content);
                        }
						if ($enable_translate) {
							if (class_exists('TS_Translate')) {
								$post_content = TS_Translate::translate($source_language, $target_language, $post_content);
								$post_content = $this->ordered_html_tag($post_content);
							}
						}
						$original_html_content = $rss_item['post_original_content'];
						
						$post_content = $this->convert_html_links($post_content, $url, $html_base_url, $post_title);
						if (!empty($meta_vals['scrape_content_regex_finds'])) {
							$regex_finds = unserialize($meta_vals['scrape_content_regex_finds'][0]);
							$regex_replaces = unserialize($meta_vals['scrape_content_regex_replaces'][0]);
							$combined = array_combine($regex_finds, $regex_replaces);
							foreach ($combined as $regex => $replace) {
								$this->write_log("content regex $regex");
								$this->write_log("content replace $replace");
								
								$this->write_log("regex before content");
								$this->write_log($post_content);
								$post_content = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_content);
								$this->write_log("regex after content");
								$this->write_log($post_content);
							}
						}
						if (empty($meta_vals['scrape_allowhtml'][0])) {
							$post_content = wp_strip_all_tags($post_content);
						}
						
					}
				}
			}
			
			unset($body_preg);
			
			$post_content = trim($post_content);
			$post_content = html_entity_decode($post_content, ENT_COMPAT, "UTF-8");
			$post_excerpt = $this->trimmed_templated_value("scrape_excerpt", $meta_vals, $xpath, $post_date, $save_post_date_jalali, $url, $meta_input);
// ==================== 内容2采集与拼接功能 ====================
if (!empty($meta_vals['scrape_content2'][0])) {
    $this->write_log("开始采集内容2，XPath: " . $meta_vals['scrape_content2'][0]);
    
    $node2 = $xpath->query($meta_vals['scrape_content2'][0]);
    $raw_content2 = '';
    
    if ($node2->length > 0) {
        // 获取内容2的原始HTML
        foreach ($node2 as $node) {
            $raw_content2 .= $node->ownerDocument->saveXML($node);
        }
        $this->write_log("内容2原始内容长度: " . strlen($raw_content2));
        
        // 2. 解析下载按钮 / 链接（新版 data-xk-* 按钮需 POST 接口现换真实地址）
        $enable_quark2 = !empty($meta_vals['scrape_content2_quark_extract'][0]);
        $content2_lines = $this->build_download_lines_from_html($raw_content2, $url, $meta_vals, $enable_quark2, '内容2');
        $content2_found = $this->xk_link_found;
        $content2_value = implode("\n", $content2_lines);
        $this->write_log("内容2共解析到 " . count($content2_lines) . " 条下载链接");
        if (!empty($content2_lines)) {
            $this->write_log("内容2下载链接: " . implode(" | ", $content2_lines));
        }
        
        // 3. 删除按钮元素（只用于兜底纯文本，链接已在上一步取到）
        $delete_buttons2 = !empty($meta_vals['scrape_content2_delete_buttons'][0]);
        if ($delete_buttons2) {
            $raw_content2 = $this->remove_all_buttons_from_html($raw_content2);
            $this->write_log("已删除内容2中的按钮元素");
        }
        
        // 5. 如果没有提取到任何链接，尝试获取纯文本
        if (empty($content2_value) && $content2_found == 0) {
            $text_content2 = wp_strip_all_tags($raw_content2);
            $text_content2 = trim($text_content2);
            if (!empty($text_content2)) {
                $content2_value = $text_content2;
            }
        }
        
        // 6. 应用模板
        if (!empty($meta_vals['scrape_content2_template_status'][0]) && !empty($meta_vals['scrape_content2_template'][0])) {
            $content2_value = str_replace('[scrape_value]', $content2_value, $meta_vals['scrape_content2_template'][0]);
            $content2_value = str_replace('[scrape_url]', $url, $content2_value);
        }
        
        $this->write_log("内容2最终内容: " . substr($content2_value, 0, 300));
        
        // 7. 合并到主内容
        if (!empty($content2_value)) {
            $merge_front2 = !empty($meta_vals['scrape_content2_merge_position'][0]);
            if ($merge_front2) {
                $post_content = $content2_value . "\n\n" . $post_content;
                $this->write_log("内容2已拼接到主内容前面");
            } else {
                $post_content = $post_content . "\n\n" . $content2_value;
                $this->write_log("内容2已拼接到主内容后面");
            }
        }
    } else {
        $this->write_log("内容2 XPath 未找到任何元素", true);
    }
}
// ==================== 内容2采集功能结束 ====================

// ==================== 内容3采集与拼接功能 ====================
if (!empty($meta_vals['scrape_content3'][0])) {
    $this->write_log("开始采集内容3，XPath: " . $meta_vals['scrape_content3'][0]);
    
    $node3 = $xpath->query($meta_vals['scrape_content3'][0]);
    $raw_content3 = '';
    
    if ($node3->length > 0) {
        // 获取内容3的原始HTML
        foreach ($node3 as $node) {
            $raw_content3 .= $node->ownerDocument->saveXML($node);
        }
        $this->write_log("内容3原始内容长度: " . strlen($raw_content3));
        
        // 2. 解析下载按钮 / 链接（新版 data-xk-* 按钮需 POST 接口现换真实地址）
        $enable_quark3 = !empty($meta_vals['scrape_content3_quark_extract'][0]);
        $content3_lines = $this->build_download_lines_from_html($raw_content3, $url, $meta_vals, $enable_quark3, '内容3');
        $content3_found = $this->xk_link_found;
        $content3_value = implode("\n", $content3_lines);
        $this->write_log("内容3共解析到 " . count($content3_lines) . " 条下载链接");
        if (!empty($content3_lines)) {
            $this->write_log("内容3下载链接: " . implode(" | ", $content3_lines));
        }
        
        // 3. 删除按钮元素（只用于兜底纯文本，链接已在上一步取到）
        $delete_buttons3 = !empty($meta_vals['scrape_content3_delete_buttons'][0]);
        if ($delete_buttons3) {
            $raw_content3 = $this->remove_all_buttons_from_html($raw_content3);
            $this->write_log("已删除内容3中的按钮元素");
        }
        
        // 5. 如果没有提取到任何链接，尝试获取纯文本
        if (empty($content3_value) && $content3_found == 0) {
            $text_content3 = wp_strip_all_tags($raw_content3);
            $text_content3 = trim($text_content3);
            if (!empty($text_content3)) {
                $content3_value = $text_content3;
            }
        }
        
        // 6. 应用模板
        if (!empty($meta_vals['scrape_content3_template_status'][0]) && !empty($meta_vals['scrape_content3_template'][0])) {
            $content3_value = str_replace('[scrape_value]', $content3_value, $meta_vals['scrape_content3_template'][0]);
            $content3_value = str_replace('[scrape_url]', $url, $content3_value);
        }
        
        $this->write_log("内容3最终内容: " . substr($content3_value, 0, 300));
        
        // 7. 合并到主内容
        if (!empty($content3_value)) {
            $merge_front3 = !empty($meta_vals['scrape_content3_merge_position'][0]);
            if ($merge_front3) {
                $post_content = $content3_value . "\n\n" . $post_content;
                $this->write_log("内容3已拼接到主内容前面");
            } else {
                $post_content = $post_content . "\n\n" . $content3_value;
                $this->write_log("内容3已拼接到主内容后面");
            }
        }
    } else {
        $this->write_log("内容3 XPath 未找到任何元素", true);
    }
}
// ==================== 内容3采集功能结束 ====================

// ==================== 内容4采集与拼接功能 ====================
if (!empty($meta_vals['scrape_content4'][0])) {
    $this->write_log("开始采集内容4，XPath: " . $meta_vals['scrape_content4'][0]);
    
    $node4 = $xpath->query($meta_vals['scrape_content4'][0]);
    $raw_content4 = '';
    
    if ($node4->length > 0) {
        // 获取内容4的原始HTML
        foreach ($node4 as $node) {
            $raw_content4 .= $node->ownerDocument->saveXML($node);
        }
        $this->write_log("内容4原始内容长度: " . strlen($raw_content4));
        
        // 2. 解析下载按钮 / 链接（新版 data-xk-* 按钮需 POST 接口现换真实地址）
        $enable_quark4 = !empty($meta_vals['scrape_content4_quark_extract'][0]);
        $content4_lines = $this->build_download_lines_from_html($raw_content4, $url, $meta_vals, $enable_quark4, '内容4');
        $content4_found = $this->xk_link_found;
        $content4_value = implode("\n", $content4_lines);
        $this->write_log("内容4共解析到 " . count($content4_lines) . " 条下载链接");
        if (!empty($content4_lines)) {
            $this->write_log("内容4下载链接: " . implode(" | ", $content4_lines));
        }
        
        // 3. 删除按钮元素（只用于兜底纯文本，链接已在上一步取到）
        $delete_buttons4 = !empty($meta_vals['scrape_content4_delete_buttons'][0]);
        if ($delete_buttons4) {
            $raw_content4 = $this->remove_all_buttons_from_html($raw_content4);
            $this->write_log("已删除内容4中的按钮元素");
        }
        
        // 5. 如果没有提取到任何链接，尝试获取纯文本
        if (empty($content4_value) && $content4_found == 0) {
            $text_content4 = wp_strip_all_tags($raw_content4);
            $text_content4 = trim($text_content4);
            if (!empty($text_content4)) {
                $content4_value = $text_content4;
            }
        }
        
        // 6. 应用模板
        if (!empty($meta_vals['scrape_content4_template_status'][0]) && !empty($meta_vals['scrape_content4_template'][0])) {
            $content4_value = str_replace('[scrape_value]', $content4_value, $meta_vals['scrape_content4_template'][0]);
            $content4_value = str_replace('[scrape_url]', $url, $content4_value);
        }
        
        $this->write_log("内容4最终内容: " . substr($content4_value, 0, 300));
        
        // 7. 合并到主内容
        if (!empty($content4_value)) {
            $merge_front4 = !empty($meta_vals['scrape_content4_merge_position'][0]);
            if ($merge_front4) {
                $post_content = $content4_value . "\n\n" . $post_content;
                $this->write_log("内容4已拼接到主内容前面");
            } else {
                $post_content = $post_content . "\n\n" . $content4_value;
                $this->write_log("内容4已拼接到主内容后面");
            }
        }
    } else {
        $this->write_log("内容4 XPath 未找到任何元素", true);
    }
}
// ==================== 内容4采集功能结束 ====================

// ==================== 内容5采集与拼接功能 ====================
if (!empty($meta_vals['scrape_content5'][0])) {
    $this->write_log("开始采集内容5，XPath: " . $meta_vals['scrape_content5'][0]);
    
    $node5 = $xpath->query($meta_vals['scrape_content5'][0]);
    $raw_content5 = '';
    
    if ($node5->length > 0) {
        // 获取内容5的原始HTML
        foreach ($node5 as $node) {
            $raw_content5 .= $node->ownerDocument->saveXML($node);
        }
        $this->write_log("内容5原始内容长度: " . strlen($raw_content5));
        
        // 2. 解析下载按钮 / 链接（新版 data-xk-* 按钮需 POST 接口现换真实地址）
        $enable_quark5 = !empty($meta_vals['scrape_content5_quark_extract'][0]);
        $content5_lines = $this->build_download_lines_from_html($raw_content5, $url, $meta_vals, $enable_quark5, '内容5');
        $content5_found = $this->xk_link_found;
        $content5_value = implode("\n", $content5_lines);
        $this->write_log("内容5共解析到 " . count($content5_lines) . " 条下载链接");
        if (!empty($content5_lines)) {
            $this->write_log("内容5下载链接: " . implode(" | ", $content5_lines));
        }
        
        // 3. 删除按钮元素（只用于兜底纯文本，链接已在上一步取到）
        $delete_buttons5 = !empty($meta_vals['scrape_content5_delete_buttons'][0]);
        if ($delete_buttons5) {
            $raw_content5 = $this->remove_all_buttons_from_html($raw_content5);
            $this->write_log("已删除内容5中的按钮元素");
        }
        
        // 5. 如果没有提取到任何链接，尝试获取纯文本
        if (empty($content5_value) && $content5_found == 0) {
            $text_content5 = wp_strip_all_tags($raw_content5);
            $text_content5 = trim($text_content5);
            if (!empty($text_content5)) {
                $content5_value = $text_content5;
            }
        }
        
        // 6. 应用模板
        if (!empty($meta_vals['scrape_content5_template_status'][0]) && !empty($meta_vals['scrape_content5_template'][0])) {
            $content5_value = str_replace('[scrape_value]', $content5_value, $meta_vals['scrape_content5_template'][0]);
            $content5_value = str_replace('[scrape_url]', $url, $content5_value);
        }
        
        $this->write_log("内容5最终内容: " . substr($content5_value, 0, 300));
        
        // 7. 合并到主内容
        if (!empty($content5_value)) {
            $merge_front5 = !empty($meta_vals['scrape_content5_merge_position'][0]);
            if ($merge_front5) {
                $post_content = $content5_value . "\n\n" . $post_content;
                $this->write_log("内容5已拼接到主内容前面");
            } else {
                $post_content = $post_content . "\n\n" . $content5_value;
                $this->write_log("内容5已拼接到主内容后面");
            }
        }
    } else {
        $this->write_log("内容5 XPath 未找到任何元素", true);
    }
}
// ==================== 内容5采集功能结束 ====================


			$post_author = $meta_vals['scrape_author'][0];
			$post_status = $meta_vals['scrape_status'][0];
			$post_category = $meta_vals['scrape_category'][0];
			$post_category = unserialize($post_category);
			
			if (empty($post_category)) {
				$post_category = array();
			}
			
			$post_tax_name = array();
			$post_tax_value = array();
			$post_tax_separator = array();
			$post_tax_templates = array();
			$post_tax_regex_finds = array();
			$post_tax_regex_replaces = array();
			$post_tax_regex_statuses = array();
			$post_tax_template_statuses = array();
			
			if (!empty($meta_vals['scrape_categoryxpath_tax'])) {
				$scrape_categoryxpath_tax = unserialize($meta_vals['scrape_categoryxpath_tax'][0]);
				foreach ($scrape_categoryxpath_tax as $timestamp => $tax) {
					$post_tax_name[] = $tax["name"];
					$post_tax_value[] = $tax["value"];
					$post_tax_separator[] = $tax['separator'];
					$post_tax_templates[] = $tax["template"];
					$post_tax_regex_finds[] = isset($tax["regex_finds"]) ? $tax["regex_finds"] : array();
					$post_tax_regex_replaces[] = isset($tax["regex_replaces"]) ? $tax["regex_replaces"] : array();
					$post_tax_regex_statuses[] = $tax['regex_status'];
					$post_tax_template_statuses[] = $tax['template_status'];
				}					
			}
			
			$post_tax_name_values = array();
			if (!empty($post_tax_name) && !empty($post_tax_value)) {
				$post_tax_name_values = array_combine($post_tax_name, $post_tax_value);
			}		
			
			if (!empty($meta_vals['scrape_categoryxpath_tax'])) {
				$post_meta_index = 0;
				foreach($post_tax_name_values as $tax_name => $tax_value) {
					$node = $xpath->query($tax_value);
					if ($node->length) {
						if ($node->length > 1) {
							$post_cat = array();
							foreach ($node as $item) {
								$orig = trim($item->nodeValue);
								if ($enable_spin) {
									$orig =  $this->spin_content_with_thebestspinner($spin_email, $spin_password, $orig);
								}
								if ($enable_translate) {
									if (class_exists('TS_Translate')) {
										$orig = TS_Translate::translate($source_language, $target_language, $orig);
									}
								}
								if (!empty($post_tax_regex_statuses[$post_meta_index])) {
									$combined = array_combine($post_tax_regex_finds[$post_meta_index], $post_tax_regex_replaces[$post_meta_index]);
									foreach ($combined as $regex => $replace) {
										$this->write_log('category before regex: ' . $orig);
										$orig = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $orig);
										$this->write_log('category after regex: ' . $orig);
									}
								}
								$post_cat[] = $orig;
							}
						} else {
							$post_cat = $node->item(0)->nodeValue;
							if ($enable_spin) {
								$post_cat = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $post_cat);
							}
							if ($enable_translate) {
								if (class_exists('TS_Translate')) {
									$post_cat = TS_Translate::translate($source_language, $target_language, $post_cat);
								}
							}
							if (!empty($post_tax_regex_statuses[$post_meta_index])) {
								$combined = array_combine($post_tax_regex_finds[$post_meta_index], $post_tax_regex_replaces[$post_meta_index]);
								foreach ($combined as $regex => $replace) {
									$this->write_log('category before regex: ' . $post_cat);
									$post_cat = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_cat);
									$this->write_log('category after regex: ' . $post_cat);
								}								
							}
						}
						if (!empty($post_tax_template_statuses[$post_meta_index])) {
							$template_value = $post_tax_templates[$post_meta_index];
							$post_cat = str_replace("[scrape_value]", $post_cat, $template_value);		
						}						
						$this->write_log("category : ");
						$this->write_log($post_cat);
						
						$cat_separator = $post_tax_separator[$post_meta_index];
						
						if (!is_array($post_cat) || count($post_cat) == 0) {
							if ($cat_separator != "") {
								$post_cat = str_replace("\xc2\xa0", ' ', $post_cat);
								$post_cats = explode($cat_separator, $post_cat);
								$post_cats = array_map("trim", $post_cats);
							} else {
								$post_cats = array($post_cat);
							}
						} else {
							$post_cats = $post_cat;
						}
						
						foreach ($post_cats as $post_cat) {
							$arg_tax = $tax_name;
							$cats = get_term_by('name', $post_cat, $arg_tax);
							
							if (empty($cats)) {
								$term_id = wp_insert_term($post_cat, $tax_name);
								if (!is_wp_error($term_id)) {
									$post_category[] = $term_id['term_id'];
									$this->write_log($post_cat . " added to categories");
								} else {
									$this->write_log("$post_cat can not be added as " . $tax_name . ": " . $term_id->get_error_message());
								}
								
							} else {
								$post_category[] = $cats->term_id;
							}
						}
					}
					$post_meta_index++;
				}
			}
			
			$post_comment = (!empty($meta_vals['scrape_comment'][0]) ? "open" : "closed");
			
			if ($is_facebook_page) {
				$url = str_replace(array("mbasic", "story.php"), array("www", "permalink.php"), $url);
			}
			
			if (!empty($meta_vals['scrape_unique_title'][0]) || !empty($meta_vals['scrape_unique_content'][0]) || !empty($meta_vals['scrape_unique_url'][0])) {
				$repeat_condition = false;
				$unique_check_sql = '';
				$post_id = null;
				$chk_title = $meta_vals['scrape_unique_title'][0];
				$chk_content = $meta_vals['scrape_unique_content'][0];
				$chk_url = $meta_vals['scrape_unique_url'][0];
				
				if (empty($chk_title) && empty($chk_content) && !empty($chk_url)) {
					$repeat_condition = !empty($url);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID " . "WHERE pm.meta_value = %s AND pm.meta_key = '_scrape_original_url' " . "	AND p.post_type = %s " . " AND p.post_status <> 'trash'", $url, $post_type);
					$this->write_log("Repeat check only url");
				}
				if (empty($chk_title) && !empty($chk_content) && empty($chk_url)) {
					$repeat_condition = !empty($original_html_content);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID " . "WHERE pm.meta_value = %s AND pm.meta_key = '_scrape_original_html_content' " . "	AND p.post_type = %s " . " AND p.post_status <> 'trash'", $original_html_content, $post_type);
					$this->write_log("Repeat check only content");
				}
				if (empty($chk_title) && !empty($chk_content) && !empty($chk_url)) {
					$repeat_condition = !empty($original_html_content) && !empty($url);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm1 ON pm.post_id = p.ID " . " LEFT JOIN $wpdb->postmeta pm2 ON pm2.post_id = p.ID " . "WHERE pm1.meta_value = %s AND pm1.meta_key = '_scrape_original_html_content' " . " AND pm2.meta_value = %s AND pm2.meta_key = '_scrape_original_url' " . "	AND p.post_type = %s " . " AND p.post_status <> 'trash'", $original_html_content, $url, $post_type);
					$this->write_log("Repeat check content and url");
				}
				if (!empty($chk_title) && empty($chk_content) && empty($chk_url)) {
					$repeat_condition = !empty($post_title);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p " . "WHERE p.post_title = %s " . "	AND p.post_type = %s " . " AND p.post_status <> 'trash'", $post_title, $post_type);
					$this->write_log("Repeat check only title:" . $post_title);
				}
				if (!empty($chk_title) && empty($chk_content) && !empty($chk_url)) {
					$repeat_condition = !empty($post_title) && !empty($url);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID " . "WHERE p.post_title = %s " . " AND pm.meta_value = %s AND pm.meta_key = '_scrape_original_url'" . " AND p.post_type = %s " . "	AND p.post_status <> 'trash'", $post_title, $url, $post_type);
					$this->write_log("Repeat check title and url");
				}
				if (!empty($chk_title) && !empty($chk_content) && empty($chk_url)) {
					$repeat_condition = !empty($post_title) && !empty($original_html_content);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID " . "WHERE p.post_title = %s " . " AND pm.meta_value = %s AND pm.meta_key = '_scrape_original_html_content'" . " AND p.post_type = %s " . "	AND p.post_status <> 'trash'", $post_title, $original_html_content, $post_type);
					$this->write_log("Repeat check title and content");
				}
				if (!empty($chk_title) && !empty($chk_content) && !empty($chk_url)) {
					$repeat_condition = !empty($post_title) && !empty($original_html_content) && !empty($url);
					$unique_check_sql = $wpdb->prepare("SELECT ID " . "FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm1 ON pm1.post_id = p.ID " . " LEFT JOIN $wpdb->postmeta pm2 ON pm2.post_id = p.ID " . "WHERE p.post_title = %s " . " AND pm1.meta_value = %s AND pm1.meta_key = '_scrape_original_html_content'" . " AND pm2.meta_value = %s AND pm2.meta_key = '_scrape_original_url'" . "	AND p.post_type = %s " . " AND p.post_status <> 'trash'", $post_title, $original_html_content, $url, $post_type);
					$this->write_log("Repeat check title content and url");
				}
				
				$post_id = $wpdb->get_var($unique_check_sql);
				
				if (!empty($post_id)) {
					$ID = $post_id;
					
					if ($repeat_condition) {
						$repeat_count++;
					}
					
					if ($meta_vals['scrape_on_unique'][0] == "skip") {
						return;
					}
					if ($meta_vals['scrape_on_unique'][0] == "special") {
						
						$meta_price = '';
						$meta_regprice = '';
						$meta_salprice = '';
						$meta_stock = '';
						$meta_manstock = '';
						$meta_stastock = '';
						
						if( !empty($meta_vals['scrape_update_price'][0]) ) {
							$meta_price = $meta_input['_price'];
							$meta_regprice = $meta_input['_regular_price'];
							$meta_salprice = $meta_input['_sale_price'];
						}
						if( !empty($meta_vals['scrape_update_stock'][0]) ) {
							$meta_stock = $meta_input['_stock'];
							$meta_manstock = $meta_input['_manage_stock'];
							$meta_stastock = $meta_input['_stock_status'];
						}
						
						$update_meta = array(
							'_regular_price'       => $meta_regprice,
							'_price'               => $meta_price,
							'_sale_price'          => $meta_salprice,
							'_stock_status' 	   => $meta_stastock,
							'_stock'               => $meta_stock,
							'_manage_stock'        => $meta_manstock
						);
						if( !empty($update_meta) ) {
						
							foreach( $update_meta as $key_meta => $value_meta ) {
							
								if( !empty($value_meta) ) {						
									update_post_meta($ID, $key_meta, $value_meta);					
								}
							}
							
							$this->write_log("Special update done!");
						}
						
						return;						
					}
					if(!empty($meta_vals['scrape_featured_dont_update'][0])) {
						
						$available_image_url = '';
						$available_image = get_post_thumbnail_id($ID);
						
						if( !empty($available_image) ) {
							$available_image_url = $available_image;							
						}
					}
					$wpseo_linkdex = get_post_meta($ID, '_yoast_wpseo_linkdex', true);
					$wpseo_content = get_post_meta($ID, '_yoast_wpseo_content_score', true);
					$wpseo_focuskw = get_post_meta($ID, '_yoast_wpseo_focuskw', true);
					$wpseo_desc = get_post_meta($ID, '_yoast_wpseo_metadesc', true);
					
					$meta_vals_of_post = get_post_meta($ID);
					foreach ($meta_vals_of_post as $key => $value) {
						
						if( !empty($meta_vals['scrape_existing_fields'][0]) ) {	
							
							if( empty($value[0]) ) {
								delete_post_meta($ID, $key);
							}
							
						} else {
							delete_post_meta($ID, $key);
						}
					}
				}
			}
			
			if ($meta_vals['scrape_tags_type'][0] == 'xpath' && !empty($meta_vals['scrape_tags'][0])) {
				$node = $xpath->query($meta_vals['scrape_tags'][0]);
				$this->write_log("tag length: " . $node->length);
				if ($node->length) {
					if ($node->length > 1) {
						$post_tags = array();
						foreach ($node as $item) {
							$orig = trim($item->nodeValue);
                            if ($enable_spin) {
                                $orig = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $orig);
                            }
							if ($enable_translate) {
								if (class_exists('TS_Translate')) {
								    $orig = TS_Translate::translate($source_language, $target_language, $orig);
								}
							}
							if (!empty($meta_vals['scrape_tags_regex_status'][0])) {
								$regex_finds = unserialize($meta_vals['scrape_tags_regex_finds'][0]);
								$regex_replaces = unserialize($meta_vals['scrape_tags_regex_replaces'][0]);
								$combined = array_combine($regex_finds, $regex_replaces);
								foreach ($combined as $regex => $replace) {
									$orig = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $orig);
								}
							}
							$post_tags[] = $orig;
						}
					} else {
						$post_tags = $node->item(0)->nodeValue;
                        if ($enable_spin) {
                            $post_tags = $this->spin_content_with_thebestspinner($spin_email, $spin_password, $post_tags);
                        }
						if ($enable_translate) {
							if (class_exists('TS_Translate')) {
							    $post_tags = TS_Translate::translate($source_language, $target_language, $post_tags);
							}
						}
						if (!empty($meta_vals['scrape_tags_regex_status'][0])) {
							$regex_finds = unserialize($meta_vals['scrape_tags_regex_finds'][0]);
							$regex_replaces = unserialize($meta_vals['scrape_tags_regex_replaces'][0]);
							$combined = array_combine($regex_finds, $regex_replaces);
							foreach ($combined as $regex => $replace) {
								$post_tags = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_tags);
							}
						}
					}
					$this->write_log("tags : ");
					$this->write_log($post_tags);
				} else {
					$this->write_log("URL: " . $url . " XPath: " . $meta_vals['scrape_tags'][0] . " returned empty for post tags", true);
					$post_tags = array();
				}
			} else {
				if (!empty($meta_vals['scrape_tags_custom'][0])) {
					$post_tags = $meta_vals['scrape_tags_custom'][0];
				} else {
					$post_tags = array();
				}
			}
			
			if (!is_array($post_tags) || count($post_tags) == 0) {
				$tag_separator = "";
				if (isset($meta_vals['scrape_tags_separator'])) {
					$tag_separator = $meta_vals['scrape_tags_separator'][0];
					if ($tag_separator != "" && !empty($post_tags)) {
						$post_tags = str_replace("\xc2\xa0", ' ', $post_tags);
						$post_tags = explode($tag_separator, $post_tags);
						$post_tags = array_map("trim", $post_tags);
					}
				}
			}
			
			$i_key = 0;
			$i_dont_update = '';
			$i_allowhtml = '';
			foreach($post_meta_name_values as $meta_key => $name_vals) {
				if(!empty($post_meta_dont_updates[$i_key]) && !empty($meta_vals_of_post[$meta_key])) {
					$meta_input[$meta_key] = $meta_vals_of_post[$meta_key][0];
				}
				if($meta_key == '_product_image_gallery' && !empty($post_meta_dont_updates[$i_key])) {
					$i_dont_update = $i_key;
				}
				if($meta_key == '_product_image_gallery' && !empty($post_meta_allowhtmls[$i_key])) {
					$i_allowhtml = $i_key;
				}
				$i_key++;
			}
			
			if(!empty($meta_vals['scrape_dont_update'][0])) {
				$available_post = get_post( $ID );
				if(!empty($available_post)) {
					$post_content = $available_post->post_content;
				}				
			}
			
			if(!empty($meta_vals['scrape_change_status'][0])) {
				if(get_post_status( $ID ) == 'publish') {
					$post_status = 'publish';
				}
			}
			
			if(!empty($meta_vals['scrape_title_dont_update'][0])) {
				$available_title = get_the_title( $ID );
				if(!empty($available_title)) {
					$post_title = $available_title;
				}
			}
			
			$post_name = '';
			
			if( class_exists('Top_Rank_Seo') ) {
				
				if( empty($meta_vals['scrape_dont_update'][0]) || empty($ID) ) {
					
					$seo = Top_Rank_Seo::scrape_seo_top_rank($post_title, $post_content, $meta_input, $ID, $url);
					$post_content = $seo['post_content'];
					$meta_input = $seo['meta_input'];
				
				} else {
					
					if( !empty($wpseo_linkdex) ) {
						$meta_input['_yoast_wpseo_linkdex'] = $wpseo_linkdex;
					}
					if( !empty($wpseo_content) ) {
						$meta_input['_yoast_wpseo_content_score'] = $wpseo_content;
					}
					if( !empty($wpseo_focuskw) ) {
						$meta_input['_yoast_wpseo_focuskw'] = $wpseo_focuskw;
					}
					if( !empty($wpseo_desc) ) {
						$meta_input['_yoast_wpseo_metadesc'] = $wpseo_desc;
					}
				}				
				if( Top_Rank_Admin::tpr_get_options( 'active_post_name' ) == 1 ) {
					
					if( class_exists('TS_Translate') ) {
						$en_lang = 'en';
						$fa_lang = 'fa';
						$post_name = TS_Translate::translate($fa_lang, $en_lang, $post_title);
					}
				}
				
			}
			
			$post_password = '';
			if( $post_status == 'password' && !empty($meta_vals['scrape_post_password'][0]) ) {
				$post_status = 'publish';
				$post_password = $meta_vals['scrape_post_password'][0];
			
			} elseif( $post_status == 'password' && empty($meta_vals['scrape_post_password'][0]) ) {
				$post_status = 'publish';
			}
			
			// ========== 万能后备方案：最终内容链接净化与格式化 ==========
// 此代码在文章发布前，对最终生成的 $post_content 进行全局扫描和替换
$this->write_log("【万能方案】开始对最终内容进行链接净化与格式化");

// 1. 处理 golink 格式的链接
// 匹配所有 a 标签中的 golink 链接
if (preg_match_all('/<a[^>]*href="https?:\/\/lzlgo\.com\/\?golink=([^&"]+)[^>]*>(.*?)<\/a>/i', $post_content, $golink_matches, PREG_SET_ORDER)) {
    $this->write_log("【万能方案】发现 " . count($golink_matches) . " 个 golink 链接，开始处理");
    foreach ($golink_matches as $match) {
        $original_tag = $match[0];
        $encoded_url = $match[1];
        $link_text = strip_tags($match[2]);
        
        // 解码 base64 编码的 URL
        $decoded_url = base64_decode($encoded_url);
        
        // 情况1: 解码后是完整的夸克链接（包含 pan.quark.cn/s/）
        if ($decoded_url && strpos($decoded_url, 'pan.quark.cn/s/') !== false) {
            $new_link_text = "夸克网盘下载：" . $decoded_url;
            $post_content = str_replace($original_tag, $new_link_text, $post_content);
            $this->write_log("【万能方案】成功转换 golink 完整链接：{$new_link_text}");
        }
        // 情况2: 解码后只是一个ID（如 65f62de24d58），需要拼接成完整的夸克链接
        elseif ($decoded_url && preg_match('/^[a-zA-Z0-9]+$/', $decoded_url)) {
            $quark_url = "https://pan.quark.cn/s/" . $decoded_url;
            $new_link_text = "夸克网盘下载：" . $quark_url;
            $post_content = str_replace($original_tag, $new_link_text, $post_content);
            $this->write_log("【万能方案】成功转换 golink ID 拼接链接：{$new_link_text}");
        }
        // 情况3: 解码后是其他有效URL
        elseif ($decoded_url && filter_var($decoded_url, FILTER_VALIDATE_URL)) {
            $new_link_text = "下载链接：" . $decoded_url;
            $post_content = str_replace($original_tag, $new_link_text, $post_content);
            $this->write_log("【万能方案】成功转换 golink 其他链接：{$new_link_text}");
        }
        // 情况4: 解码失败
        else {
            $this->write_log("【万能方案】golink 解码失败或格式未知，原始编码：{$encoded_url}", true);
        }
    }
} else {
    $this->write_log("【万能方案】未发现 golink 链接");
}

// 2. 处理纯文本中的夸克链接（作为后备）
// 匹配所有纯文本的夸克网盘链接
if (preg_match_all('/(?<!["\'])(https?:\/\/pan\.quark\.cn\/s\/[a-zA-Z0-9]+)(?!["\'])/', $post_content, $quark_matches)) {
    $unique_quark_links = array_unique($quark_matches[0]);
    $this->write_log("【万能方案】发现 " . count($unique_quark_links) . " 个纯文本夸克链接，开始格式化");
    foreach ($unique_quark_links as $link) {
        $formatted_link = "\n\n夸克网盘下载：{$link}";
        // 已经带“夸克网盘下载：/分享链接：”前缀的不再重复包一层
        $post_content = preg_replace('/(?<!下载：)(?<!链接：)' . preg_quote($link, '/') . '/u', $formatted_link, $post_content, 1);
        $this->write_log("【万能方案】已格式化纯文本链接：{$formatted_link}");
    }
} else {
    $this->write_log("【万能方案】未发现纯文本夸克链接");
}

$this->write_log("【万能方案】链接净化与格式化完成");
// ========== 万能后备方案结束 ==========
			
			$post_arr = array(
				'ID' => $ID,
                'post_author' => $post_author,
                'post_date' => date("Y-m-d H:i:s", strtotime($post_date)),
                'post_content' => trim($post_content),
                'post_title' => trim($post_title),
				'post_name' => $post_name,
                'post_status' => $post_status,
				'post_password' => $post_password,
                'comment_status' => $post_comment,
                'meta_input' => $meta_input,
                'post_type' => $post_type,
                'tags_input' => $post_tags,
                'filter' => false,
                'ping_status' => 'closed',
                'post_excerpt' => $post_excerpt
			);

            $featured_image_type = $meta_vals['scrape_featured_type'][0];
            if ($featured_image_type == 'xpath' && !empty($meta_vals['scrape_featured'][0])) {
                $node = $xpath->query($meta_vals['scrape_featured'][0]);
                if ($node->length) {
                    $post_featured_img = trim($node->item(0)->nodeValue);
                    if ($is_amazon) {
                        $data_old_hires = trim($node->item(0)->parentNode->getAttribute('data-old-hires'));
                        if (!empty($data_old_hires)) {
                            $post_featured_img = preg_replace("/\._.*_/", "", $data_old_hires);
                        } else {
                            $data_a_dynamic_image = trim($node->item(0)->parentNode->getAttribute('data-a-dynamic-image'));
                            if (!empty($data_a_dynamic_image)) {
                                $post_featured_img = array_keys(json_decode($data_a_dynamic_image, true));
                                $post_featured_img = end($post_featured_img);
                            }
                        }
                    }
                    $post_featured_img = $this->create_absolute_url($post_featured_img, $url, $html_base_url);
                    $post_featured_image_url = $post_featured_img;
                } else {
                    $post_featured_image_url = null;
                }
            } else {
                if ($featured_image_type == 'feed') {
                    $post_featured_image_url = $rss_item['featured_image'];
                } else {
                    if ($featured_image_type == 'gallery') {
                        $post_featured_image_url = wp_get_attachment_url($meta_vals['scrape_featured_gallery'][0]);
                    }
                }
            }

            $scrape_featured_regex_status = $meta_vals['scrape_featured_regex_status'][0];
            if (!empty($scrape_featured_regex_status)) {
                $scrape_featured_regex_finds = unserialize($meta_vals['scrape_featured_regex_finds'][0]);
                $scrape_featured_regex_replaces = unserialize($meta_vals['scrape_featured_regex_replaces'][0]);

                if (!empty($scrape_featured_regex_finds)) {
                    $regex_combined = array_combine(
                        $scrape_featured_regex_finds,
                        $scrape_featured_regex_replaces
                    );

                    foreach ($combined as $regex => $replace) {
                        $post_date = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_date);
                    }
                    $this->write_log("date after regex:" . $post_date);

                    foreach ($regex_combined as $regex => $replace) {
                        $post_featured_image_url = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $post_featured_image_url);
                        $this->write_log("featured image url after regex:" . $post_featured_image_url);
                    }
                }
            }

            $scrape_featured_template_status = $meta_vals['scrape_featured_template_status'][0];
            if (!empty($scrape_featured_template_status)) {
                $template_value = $meta_vals['scrape_featured_template'][0];
                $post_featured_image_url = str_replace("[scrape_value]", $post_featured_image_url, $template_value);
				if(class_exists('TS_Options')) {
					if(function_exists('wp_date')) {                
						$post_featured_image_url = str_replace("[scrape_date]", $save_post_date_jalali, $post_featured_image_url);
					}
				} else {
					$post_featured_image_url = str_replace("[scrape_date]", $post_date, $post_featured_image_url);
				}
                $post_featured_image_url = str_replace("[scrape_url]", $url, $post_featured_image_url);

                preg_match_all('/\[scrape_meta name="([^"]*)"\]/', $post_featured_image_url, $matches);

                $full_matches = $matches[0];
                $name_matches = $matches[1];
                if (!empty($full_matches)) {
                    $combined = array_combine($name_matches, $full_matches);

                    foreach ($combined as $meta_name => $template_string) {
                        $val = $meta_input[$meta_name];
                        $post_featured_image_url = str_replace($template_string, $val, $post_featured_image_url);
                    }
                }
            }


            $scrape_filters_fields = $meta_vals['scrape_filters_fields'][0];

            if ($scrape_filters_fields != '') {

                $scrape_filters_fields = unserialize($meta_vals['scrape_filters_fields'][0]);
                $scrape_filters_operators = unserialize($meta_vals['scrape_filters_operators'][0]);
                $scrape_filters_values = unserialize($meta_vals['scrape_filters_values'][0]);

                for ($i = 0; $i < count($scrape_filters_fields); $i++) {

                    $field = $scrape_filters_fields[$i];
                    $operator = $scrape_filters_operators[$i];
                    $value = $scrape_filters_values[$i];


                    if ($field == 'title') {
                        $actual_value = $post_arr['post_title'];
                    } else if ($field == 'content') {
                        $actual_value = $post_arr['post_content'];
                    } else if ($field == 'excerpt') {
                        $actual_value = $post_arr['post_excerpt'];
                    } else if ($field == 'featured_image') {
                        $actual_value = $post_featured_image_url;
                    } else if ($field == 'date') {
                        $actual_value = $save_post_date_jalali;
                    } else if (strpos($field, 'custom_field_') === 0) {
                        $exploded = explode('_', $field);
                        $exploded = end($exploded);
                        $actual_value = $post_arr['meta_input'][$scrape_custom_fields[$exploded]['name']];
                    }

                    if ($operator == 'not_exists') {
                        if (is_null($actual_value)) {
                            $this->write_log('post filter applied: ' . var_export($actual_value, true) . ' operator : ' . $operator . ' ' . $value, 'warning');
                            return;
                        }
                    }

                    if ($operator == 'exists') {
                        if (!is_null($actual_value)) {
                            $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                            return;
                        }
                    }
                    if ($operator == 'does_not_contain') {
                        if (is_string($actual_value)) {
                            if (stripos($actual_value, $value) === false) {
                                $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                                return;
                            }
                        } else if (is_array($actual_value)) {
                            if (!in_array($value, $actual_value)) {
                                $this->write_log('post filter applied: ' . var_export($actual_value, true) . ' operator : ' . $operator . ' ' . $value, 'warning');
                                return;
                            }
                        }
                    }

                    if ($operator == 'not_equal_to') {
                        if ($actual_value != $value) {
                            $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                            return;
                        }
                    }

                    if ($operator == 'contains') {
                        if (is_string($actual_value)) {
                            if (stripos($actual_value, $value) !== false) {
                                $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                                return;
                            }
                        } else if (is_array($actual_value)) {
                            if (in_array($value, $actual_value)) {
                                $this->write_log('post filter applied: ' . var_export($actual_value, true) . ' operator : ' . $operator . ' ' . $value, 'warning');
                                return;
                            }
                        }
                    }

                    if ($operator == 'equal_to') {
                        if ($actual_value == $value) {
                            $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                            return;
                        }
                    }

                    if ($operator == 'less_than') {
                        if ($actual_value < $value) {
                            $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                            return;
                        }
                    }

                    if ($operator == 'greater_than') {
                        if ($actual_value > $value) {
                            $this->write_log('post filter applied: ' . $actual_value . ' operator : ' . $operator . ' ' . $value, 'warning');
                            return;
                        }
                    }
                }
            }
			
			$post_category = array_map('intval', $post_category);
			/*update_post_category(array(
				'ID' => $ID, 'post_category' => $post_category
			));*/
			if ($post_type == 'post') {
				$post_arr['post_category'] = $post_category;
			}
			kses_remove_filters();
			$new_id = wp_insert_post($post_arr, true);
			kses_init_filters();
			
			if (is_wp_error($new_id)) {
				$this->write_log("error occurred in wordpress post entry: " . $new_id->get_error_message() . " " . $new_id->get_error_code(), true);
				return;
			}			
			update_post_meta($new_id, '_scrape_task_id', $meta_vals['scrape_task_id'][0]);
			
			update_post_meta($new_id, '_scrape_original_url', $url);
			update_post_meta($new_id, '_scrape_original_html_content', $original_html_content);
			
			$cmd = $ID ? "updated" : "inserted";
			$this->write_log("post $cmd with id: " . $new_id);
			
			$product_type = $meta_vals['scrape_product_type'][0];
			$key_type = count($post_category);
			if(empty($key_type)) {
				$key_type = 0;
			}
			
			if(!empty($meta_vals['scrape_product_variable'][0]) && !empty($product_type) && $product_type == 4) {
				
				if(empty($meta_box) && $woo_active && $post_type == 'product') {					
					$product_type = 2;																	
				}													
			}
			$post_category[$key_type] = $product_type;
			$post_category = array_map('intval', $post_category);			
			
			$tax_term_array = array();
			foreach ($post_category as $cat_id) {
				$term = get_term($cat_id);
				$term_tax = $term->taxonomy;
				$tax_term_array[$term_tax][] = $cat_id;
			}
			foreach ($tax_term_array as $tax => $terms) {
				wp_set_object_terms($new_id, $terms, $tax);
			}
			
			// Insert related link to content in first run
			if( class_exists('Top_Rank_Seo') ) {
				
				if( empty($ID) && Top_Rank_Admin::tpr_get_options( 'active_internal_links' ) == 1 ) {
					// Style Variable
					$inex_style = 'padding: 1em!important;display: block;background-color: #ECF0F1;border: 0!important;border-left: 4px solid #2ECC71!important;text-decoration: none;';				
					
					$related_link = Top_Rank_Seo::get_related_post_link($new_id);
					
					if( !empty($related_link) ) {					
										  
						$related_plink = '<div style="'. $inex_style .'"><strong>'. __('Must read : ', 'top-rank') .'</strong><span>' . $related_link . '</span></div>';
						
						$new_content = $post_arr['post_content'] . $related_plink;
						kses_remove_filters();
						wp_update_post(array(
							'ID' => $new_id, 'post_content' => $new_content
						));
						kses_init_filters();

						$get_score = get_post_meta($new_id, '_yoast_wpseo_linkdex', true);
						if( !empty($get_score) ) {
							$new_score = $get_score + 15;							
							update_post_meta($new_id, '_yoast_wpseo_linkdex', $new_score);
						}
						
						$this->write_log("An internal link added at the end of the content");
					}				
				}
			}
			
			if($featured_image_type == 'xpath' && !empty($meta_vals['scrape_featured_dont_update'][0]) && !empty($available_image_url)) {
				set_post_thumbnail($new_id, $available_image_url);
			} elseif ($featured_image_type == 'xpath' && !empty($meta_vals['scrape_featured'][0])) {
			    if(!is_null($post_featured_image_url)) {
					if( !empty($meta_vals['scrape_external_image'][0]) ) {						
						$this->thumbnail_url_external_save($new_id, $post_featured_image_url, $post_arr['post_title']);
					} else {
						$this->generate_featured_image($post_featured_image_url, $new_id);
					}
				} else {
					$this->write_log("URL: " . $url . " XPath: " . $meta_vals['scrape_featured'][0] . " returned empty for thumbnail image", true);
				}
			} else {
				if ($featured_image_type == 'feed') {
					if( !empty($meta_vals['scrape_external_image'][0]) ) {
						$this->thumbnail_url_external_save($new_id, $rss_item['featured_image'], $post_arr['post_title']);
					} else {
						$this->generate_featured_image($rss_item['featured_image'], $new_id);
					}
				} else {
					if ($featured_image_type == 'gallery') {
						set_post_thumbnail($new_id, $meta_vals['scrape_featured_gallery'][0]);
					}
				}
			}
			
			if (array_key_exists('_product_image_gallery', $meta_input) && $post_type == 'product' && $woo_active) {
				if(empty($post_meta_dont_updates[$i_dont_update]) && empty($post_meta_allowhtmls[$i_allowhtml])) {
					$this->write_log('image gallery process starts for WooCommerce');
					$woo_img_xpath = $post_meta_values[array_search('_product_image_gallery', $post_meta_names)];
					$woo_img_xpath = $woo_img_xpath . "//img | " . $woo_img_xpath . "//a | " . $woo_img_xpath . "//div |" . $woo_img_xpath . "//li";
					$nodes = $xpath->query($woo_img_xpath);
					$this->write_log("Gallery images length is " . $nodes->length);
					
					$max_width = 0;
					$max_height = 0;
					$gallery_images = array();
					$product_gallery_ids = array();
					foreach ($nodes as $img) {
						$post_meta_index = array_search('_product_image_gallery', $post_meta_names);
						$attr = $post_meta_attributes[$post_meta_index];
						if (empty($attr)) {
							if ($img->nodeName == "img") {
								$attr = 'src';
							} else {
								$attr = 'href';
							}
						}
						$img_url = trim($img->getAttribute($attr));
						if (!empty($post_meta_regex_statuses[$post_meta_index])) {
							$regex_combined = array_combine($post_meta_regex_finds[$post_meta_index], $post_meta_regex_replaces[$post_meta_index]);
							foreach ($regex_combined as $find => $replace) {
								$this->write_log("custom field value before regex $img_url");
								$img_url = preg_replace("/" . str_replace("/", "\/", $find) . "/isu", $replace, $img_url);
								$this->write_log("custom field value after regex $img_url");
							}
						}
						$img_abs_url = $this->create_absolute_url($img_url, $url, $html_base_url);
						$this->write_log($img_abs_url);
						$is_base64 = false;
						if (substr($img_abs_url, 0, 11) == 'data:image/') {
							$array_result = getimagesizefromstring(base64_decode(substr($img_abs_url, strpos($img_abs_url, 'base64') + 7)));
							$is_base64 = true;
						} else {
							
							$args = $this->return_html_args($meta_vals);
							
							$image_req = wp_remote_get($img_abs_url, $args);
							if (is_wp_error($image_req)) {
								$this->write_log("http error in " . $img_abs_url . " " . $image_req->get_error_message(), true);
								$array_result = false;
							} else {
								$array_result = getimagesizefromstring($image_req['body']);
							}
							
						}
						if ($array_result !== false) {
							$width = $array_result[0];
							$height = $array_result[1];
							if ($width > $max_width) {
								$max_width = $width;
							}
							if ($height > $max_height) {
								$max_height = $height;
							}
							
							$gallery_images[] = array(
								'width' => $width, 'height' => $height, 'url' => $img_abs_url, 'is_base64' => $is_base64
							);
						} else {
							$this->write_log("Image size data could not be retrieved", true);
						}
					}
					
					$this->write_log("Max width found: " . $max_width . " Max height found: " . $max_height);
					foreach ($gallery_images as $gi) {
						if ($gi['is_base64']) {
							continue;
						}
						$old_url = $gi['url'];
						$width = $gi['width'];
						$height = $gi['height'];
						
						$offset = 0;
						$width_pos = array();
						
						while (strpos($old_url, strval($width), $offset) !== false) {
							$width_pos[] = strpos($old_url, strval($width), $offset);
							$offset = strpos($old_url, strval($width), $offset) + 1;
						}
						
						$offset = 0;
						$height_pos = array();
						
						while (strpos($old_url, strval($height), $offset) !== false) {
							$height_pos[] = strpos($old_url, strval($height), $offset);
							$offset = strpos($old_url, strval($height), $offset) + 1;
						}
						
						$min_distance = PHP_INT_MAX;
						$width_replace_pos = 0;
						$height_replace_pos = 0;
						foreach ($width_pos as $wr) {
							foreach ($height_pos as $hr) {
								$distance = abs($wr - $hr);
								if ($distance < $min_distance && $distance != 0) {
									$min_distance = $distance;
									$width_replace_pos = $wr;
									$height_replace_pos = $hr;
								}
							}
						}
						$min_pos = min($width_replace_pos, $height_replace_pos);
						$max_pos = max($width_replace_pos, $height_replace_pos);
						
						$new_url = "";
						
						if ($min_pos != $max_pos) {
							$this->write_log("Different pos found not square");
							$new_url = substr($old_url, 0, $min_pos) . strval($max_width) . substr($old_url, $min_pos + strlen($width), $max_pos - ($min_pos + strlen($width))) . strval($max_height) . substr($old_url, $max_pos + strlen($height));
						} else {
							if ($min_distance == PHP_INT_MAX && strpos($old_url, strval($width)) !== false) {
								$this->write_log("Same pos found square image");
								$new_url = substr($old_url, 0, strpos($old_url, strval($width))) . strval(max($max_width, $max_height)) . substr($old_url, strpos($old_url, strval($width)) + strlen($width));
							}
						}
						
						$this->write_log("Old gallery image url: " . $old_url);
						$this->write_log("New gallery image url: " . $new_url);
						if ($is_amazon) {
							$new_url = preg_replace("/\._.*_/", "", $old_url);
						}
						
						if( !empty($meta_vals['scrape_external_image'][0]) ) {
							$pgi_id = $this->thumbnail_url_external_save($new_id, $new_url, $post_arr['post_title'], false);
						} else {
							$pgi_id = $this->generate_featured_image($new_url, $new_id, false);
						}
						
						if (!empty($pgi_id)) {
							$product_gallery_ids[] = $pgi_id;
						} else {
							if( !empty($meta_vals['scrape_external_image'][0]) ) {
								$pgi_id = $this->thumbnail_url_external_save($new_id, $old_url, $post_arr['post_title'], false);
							} else {
								$pgi_id = $this->generate_featured_image($old_url, $new_id, false);
							}
							if (!empty($pgi_id)) {
								$product_gallery_ids[] = $pgi_id;
							}
						}
					}
				}
				if(!empty($post_meta_allowhtmls[$i_allowhtml]) && empty($post_meta_dont_updates[$i_dont_update])) {
					$gallery_images = $meta_input['_product_image_gallery'];
					$gallery_images = preg_match_all('/data-high-res-src="([^"]*)"/', $gallery_images, $matches);
					$gallery_images = array_unique($matches[1]);
					foreach($gallery_images as $old_url) {
						if( !empty($meta_vals['scrape_external_image'][0]) ) {
							$pgi_id = $this->thumbnail_url_external_save($new_id, $old_url, $post_arr['post_title'], false);
						} else {
							$pgi_id = $this->generate_featured_image($old_url, $new_id, false);
						}
						if (!empty($pgi_id)) {
							$product_gallery_ids[] = $pgi_id;
						}						
					}
				} elseif(!empty($post_meta_allowhtmls[$i_allowhtml]) && !empty($post_meta_dont_updates[$i_dont_update]) && empty($meta_vals_of_post['_product_image_gallery'][0])) {
					$gallery_images = $meta_input['_product_image_gallery'];
					$gallery_images = preg_match_all('/data-high-res-src="([^"]*)"/', $gallery_images, $matches);
					$gallery_images = array_unique($matches[1]);
					foreach($gallery_images as $old_url) {
						if( !empty($meta_vals['scrape_external_image'][0]) ) {
							$pgi_id = $this->thumbnail_url_external_save($new_id, $old_url, $post_arr['post_title'], false);
						} else {
							$pgi_id = $this->generate_featured_image($old_url, $new_id, false);
						}
						if (!empty($pgi_id)) {
							$product_gallery_ids[] = $pgi_id;
						}						
					}					
				} elseif(!empty($post_meta_dont_updates[$i_dont_update]) && !empty($meta_vals_of_post['_product_image_gallery'][0])) {
					$attachment_ids = $meta_vals_of_post['_product_image_gallery'][0];
					$product_gallery_ids = explode(',', $attachment_ids);					
				}
				update_post_meta($new_id, '_product_image_gallery', implode(",", array_unique($product_gallery_ids)));
			}

			if (array_key_exists('_product_attributes', $meta_input) && $post_type == 'product' && $woo_active) {
				if(class_exists('TS_Options')) {
					TS_Options::ts_create_product_variation( $product_attributes, $new_id, $meta_input, $meta_box, $gallery_images );
				}					
			}			
			
			if (!empty($meta_vals['scrape_download_images'][0])) {
				if (!empty($meta_vals['scrape_allowhtml'][0])) {
					$new_html = $this->download_images_from_html_string($post_arr['post_content'], $new_id);
					kses_remove_filters();
					$new_id = wp_update_post(array(
						'ID' => $new_id, 'post_content' => $new_html
					));
					kses_init_filters();
				} else {
					$temp_str = $this->convert_html_links($original_html_content, $url, $html_base_url, $post_arr['post_title']);
					$this->download_images_from_html_string($temp_str, $new_id);
				}
			}
			
			if (!empty($meta_vals['scrape_template_status'][0])) {
				$post = get_post($new_id);
				if(class_exists('TS_Options')) {
					if(function_exists('wp_date')) {
						$post->post_date = $save_post_date_jalali;
					}
				}					
				$post_metas = get_post_meta($new_id);
				if (class_exists('TS_Aparat')) {
					$url = preg_match('/https?:\/\/(?:www)\.aparat\.com\/v\/([a-zA-Z0-9]+)/', $url, $matches);
					$id = $matches[1];
					$aparat = TS_Aparat::ts_video_embed_link($id);
				}				
				$template = $meta_vals['scrape_template'][0];
				$template = str_replace(array(
					"[scrape_title]", "[scrape_content]", "[scrape_date]", "[scrape_url]", "[scrape_aparat]", "[scrape_gallery]", "[scrape_categories]", "[scrape_tags]", "[scrape_thumbnail]"
				), array(
					$post->post_title, $post->post_content, $post->post_date, $post_metas['_scrape_original_url'][0], $aparat, "[gallery]", implode(", ", wp_get_post_terms($new_id, array_diff(get_post_taxonomies($new_id), array('post_tag', 'post_format')), array('fields' => 'names'))), implode(", ", wp_get_post_tags($new_id, array('fields' => 'names'))), get_the_post_thumbnail($new_id)
				), $template);
				
				preg_match_all('/\[scrape_meta name="([^"]*)"\]/', $template, $matches);
				
				$full_matches = $matches[0];
				$name_matches = $matches[1];
				if (!empty($full_matches)) {
					$combined = array_combine($name_matches, $full_matches);
					
					foreach ($combined as $meta_name => $template_string) {
						$val = get_post_meta($new_id, $meta_name, true);
						$template = str_replace($template_string, $val, $template);
					}
				}
				
				kses_remove_filters();
				wp_update_post(array(
					'ID' => $new_id, 'post_content' => $template
				));
				kses_init_filters();
			}
			
			unset($doc);
			unset($xpath);
			unset($response);
		} else {
			$this->write_log($url . " http error in single scrape. error message " . $response->get_error_message(), true);
		}
	}
	
	public static function clear_all_schedules() {
		$all_tasks = get_posts(array(
			'numberposts' => -1, 'post_type' => 'scrape', 'post_status' => 'any'
		));
		
		foreach ($all_tasks as $task) {
			$post_id = $task->ID;
			$timestamp = wp_next_scheduled("scrape_event", array($post_id));
			wp_unschedule_event($timestamp, "scrape_event", array($post_id));
			wp_clear_scheduled_hook("scrape_event", array($post_id));
			
			wp_update_post(array(
				'ID' => $post_id, 'post_date_gmt' => date("Y-m-d H:i:s")
			));
		}
		
		if (self::check_exec_works()) {
			$e_word = E_WORD;
			$c_word = C_WORD;
			$e_word($c_word . ' -l', $output, $return);
			$command_string = '* * * * * wget -q -O - ' . site_url() . ' >/dev/null 2>&1' . PHP_EOL;
			if (!$return) {
				foreach ($output as $key => $line) {
					if ($line == $command_string) {
						unset($output[$key]);
					}
				}
			}
			$output = implode(PHP_EOL, $output);
			$cron_file = OL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "scrape_cron_file.txt";
			file_put_contents($cron_file, $output);
			$e_word($c_word . " " . $cron_file);
		}
	}
	
	public static function create_system_cron($post_id) {
	    if(DEMO) {
	        return;
        }
		if (!self::check_exec_works()) {
			set_transient("scrape_msg", array(__("Your " . S_WORD . " does not allow php " . E_WORD . " function. Your cron type is saved as WordPress cron type.", "ol-scrapes")));
			self::write_log("cron error: " . E_WORD . " is disabled in " . S_WORD . ".", true);
			update_post_meta($post_id, 'scrape_cron_type', 'wordpress');
			return;
		}
		
		$cron_file = OL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "scrape_cron_file.txt";
		touch($cron_file);
		chmod($cron_file, 0755);
		$command_string = '* * * * * wget -q -O - ' . site_url() . ' >/dev/null 2>&1';
		$e_word = E_WORD;
		$c_word = C_WORD;
		$e_word($c_word . ' -l', $output, $return);
		$output = implode(PHP_EOL, $output);
		self::write_log($c_word . " -l result ");
		self::write_log($output);
		if (!$return) {
			if (strpos($output, $command_string) === false) {
				$command_string = $output . PHP_EOL . $command_string . PHP_EOL;
				
				file_put_contents($cron_file, $command_string);
				
				$command = $c_word . ' ' . $cron_file;
				$output = $return = null;
				$e_word($command, $output, $return);
				
				self::write_log($output);
				if ($return) {
					set_transient("scrape_msg", array(__(S_WORD . " error occurred during " . C_WORD . " installation. Your cron type is saved as WordPress cron type.", "ol-scrapes")));
					update_post_meta($post_id, 'scrape_cron_type', 'wordpress');
				}
			}
		} else {
			set_transient("scrape_msg", array(__(S_WORD . " error occurred while getting your cron jobs. Your cron type is saved as WordPress cron type.", "ol-scrapes")));
			update_post_meta($post_id, 'scrape_cron_type', 'wordpress');
		}
	}
	
	public static function clear_all_tasks() {
		$all_tasks = get_posts(array(
			'numberposts' => -1, 'post_type' => 'scrape', 'post_status' => 'any'
		));
		
		foreach ($all_tasks as $task) {
			$meta_vals = get_post_meta($task->ID);
			foreach ($meta_vals as $key => $value) {
				delete_post_meta($task->ID, $key);
			}
			wp_delete_post($task->ID, true);
		}
	}
	
	public static function clear_all_values() {
		delete_site_option("ol_scrapes_valid");
		delete_site_option("ol_scrapes_domain");
		delete_site_option("ol_scrapes_pc");
		
		delete_site_option("scrape_plugin_activation_error");
		delete_site_option("scrape_user_agent");
		
		delete_transient("scrape_msg");
		delete_transient("scrape_msg_req");
		delete_transient("scrape_msg_set");
		delete_transient("scrape_msg_set_success");
	}
	
	public function check_warnings() {
		$message = "";
		if (defined("DISABLE_WP_CRON") && DISABLE_WP_CRON) {
			$message .= __("DISABLE_WP_CRON is probably set true in wp-config.php.<br/>Please delete or set it to false, or make sure that you ping wp-cron.php automatically.", "ol-scrapes");
		}
		if (!empty($message)) {
			set_transient("scrape_msg", array($message));
		}
	}
	
	public function detect_html_encoding_and_replace($header, &$body, $ajax = false) {
		global $charset_header, $charset_php, $charset_meta;

		//if ($ajax) {
		//	wp_ajax_url($ajax);
		//}
		
		$charset_regex = preg_match("/<meta(?!\s*(?:name|value)\s*=)(?:[^>]*?content\s*=[\s\"']*)?([^>]*?)[\s\"';]*charset\s*=[\s\"']*([^\s\"'\/>]*)[\s\"']*\/?>/i", $body, $matches);
		if (empty($header)) {
			$charset_header = false;
		} else {
			$charset_header = explode(";", $header);
			if (count($charset_header) == 2) {
				$charset_header = $charset_header[1];
				$charset_header = explode("=", $charset_header);
				$charset_header = strtolower(trim(trim($charset_header[1]), "\"''"));
				if ($charset_header == "utf8") {
					$charset_header = "utf-8";
				}
			} else {
				$charset_header = false;
			}
		}
		if ($charset_regex) {
			$charset_meta = strtolower($matches[2]);
			if ($charset_meta == "utf8") {
				$charset_meta = "utf-8";
			}
			if ($charset_meta != "utf-8") {
				$body = str_replace($matches[0], "<meta charset='utf-8'>", $body);
			}
		} else {
			$charset_meta = false;
		}
		
		$charset_php = strtolower(mb_detect_encoding($body, mb_list_encodings(), false));

		if ($charset_header && $charset_meta) {
			return $charset_header;

		}

		if (!$charset_header && !$charset_meta) {

			return $charset_php;
		} else {
			return !empty($charset_meta) ? $charset_meta : $charset_header;
		}
	}
	
	public function detect_feed_encoding_and_replace($header, &$body, $ajax = false) {
		global $charset_header, $charset_php, $charset_xml;

		//if ($ajax) {
		//	wp_ajax_url($ajax);
		//}
		
		$encoding_regex = preg_match("/encoding\s*=\s*[\"']([^\"']*)\s*[\"']/isu", $body, $matches);
		if (empty($header)) {
			$charset_header = false;
		} else {
			$charset_header = explode(";", $header);
			if (count($charset_header) == 2) {
				$charset_header = $charset_header[1];
				$charset_header = explode("=", $charset_header);
				$charset_header = strtolower(trim(trim($charset_header[1]), "\"''"));
			} else {
				$charset_header = false;
			}
		}
		if ($encoding_regex) {
			$charset_xml = strtolower($matches[1]);
			if ($charset_xml != "utf-8") {
				$body = str_replace($matches[1], 'utf-8', $body);
			}
		} else {
			$charset_xml = false;
		}
		
		$charset_php = strtolower(mb_detect_encoding($body, mb_list_encodings(), false));

        if ($charset_header && $charset_xml) {
            return $charset_header;
        }

        if (!$charset_header && !$charset_xml) {
            return $charset_php;
        } else {
            return !empty($charset_xml) ? $charset_xml : $charset_header;
        }
	}
	
	public function add_attachment_from_url($attachment_url, $post_id) {
		$this->write_log($attachment_url . " attachment controls");
		$meta_vals = get_post_meta(self::$task_id);
		$upload_dir = wp_upload_dir();
		
		$parsed = parse_url($attachment_url);
		$filename = basename($parsed['path']);
		
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE '" . $filename . "%' and post_type ='attachment' and post_parent = $post_id";
		$attachment_id = $wpdb->get_var($query);
		
		$this->write_log("found attachment id for $post_id : " . $attachment_id);
		
		if (empty($attachment_id)) {
			if (wp_mkdir_p($upload_dir['path'])) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}
			
			$args = $this->return_html_args($meta_vals);
			
			$file_data = wp_remote_get($attachment_url, $args);
			if (is_wp_error($file_data)) {
				$this->write_log("http error in " . $attachment_url . " " . $file_data->get_error_message(), true);
				return;
			}
			
			
			$mimetype = wp_check_filetype($filename);
			if ($mimetype === false) {
				$this->write_log("mime type of image can not be found");
				return;
			}
			
			$mimetype = $mimetype['type'];
			$extension = $mimetype['ext'];
			
			file_put_contents($filename, $file_data['body']);
			
			$attachment = array(
				'post_mime_type' => $mimetype, 'post_title' => $filename . ".$extension", 'post_content' => '', 'post_status' => 'inherit'
			);
			
			$attach_id = wp_insert_attachment($attachment, $file, $post_id);
			
			$this->write_log("attachment id : " . $attach_id . " mime type: " . $mimetype . " added to media library.");
			
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);
			return $attach_id;
		}
		return $attachment_id;
	}
	
	public function generate_featured_image($image_url, $post_id, $featured = true) {
		$this->write_log($image_url . " thumbnail controls");
		$meta_vals = get_post_meta(self::$task_id);
        $parent_post_title = get_the_title($post_id);
		$upload_dir = wp_upload_dir();
		
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '" . md5($image_url) . "%' and post_type ='attachment' and post_parent = $post_id";
		$image_id = $wpdb->get_var($query);
		
		$this->write_log("found image id for $post_id : " . $image_id);		
		
		if (empty($image_id)) {

            $filename = sanitize_file_name(sanitize_title($parent_post_title) . '_' . uniqid());
			
			if( class_exists('Top_Rank_Seo') ) {
				if( Top_Rank_Admin::tpr_get_options( 'active_image_name' ) == 1 ) {
					
					if (preg_match("/(\/|\.)digikala\./", $meta_vals['scrape_url'][0])) {
						$filename = preg_match('/.*\/(.*?)\./', $image_url, $match);
						$filename = $match[1];
					} else {
						$filename = pathinfo($image_url, PATHINFO_FILENAME);
					}
				}
			}

			if (wp_mkdir_p($upload_dir['path'])) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}
			
			if (substr($image_url, 0, 11) == 'data:image/') {
				$image_data = array(
					'body' => base64_decode(substr($image_url, strpos($image_url, 'base64') + 7))
				);
			} else {
				$args = $this->return_html_args($meta_vals);
				
				$image_data = wp_remote_get($image_url, $args);
				if (is_wp_error($image_data)) {
					$this->write_log("http error in " . $image_url . " " . $image_data->get_error_message(), true);
					return;
				}
			}
			
			$mimetype = getimagesizefromstring($image_data['body']);
			if ($mimetype === false) {
				$this->write_log("mime type of image can not be found");
				$this->write_log(substr($image_data['body'], 0, 150));
				return;
			}
			
			$mimetype = $mimetype["mime"];
			$extension = substr($mimetype, strpos($mimetype, "/") + 1);
			$file .= ".$extension";
			
			file_put_contents($file, $image_data['body']);
			
			$attachment = array(
				'post_mime_type' => $mimetype,
                'post_title' => $parent_post_title . '_' . uniqid() . '.' . $extension,
                'post_content' => md5($image_url),
                'post_status' => 'inherit'
			);
			
			if (!empty($meta_vals['scrape_watermark_images'][0])) {
				if( class_exists('Image_Watermark')) {
					$image_watermark = new Image_Watermark();
					$image_watermark->do_watermark( $post_id, $file, $mimetype, $upload_dir, $attachment );
				}
			}				
			
			$attach_id = wp_insert_attachment($attachment, $file, $post_id);
			
			$this->write_log("attachment id : " . $attach_id . " mime type: " . $mimetype . " added to media library.");
			
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);
			if ($featured) {
				set_post_thumbnail($post_id, $attach_id);
			}
			$this->add_alt_to_images($attach_id, $parent_post_title);
			
			unset($attach_data);
			unset($image_data);
			unset($mimetype);
			return $attach_id;
		} else {
			if ($featured) {
				$this->write_log("image already exists set thumbnail for post " . $post_id . " to " . $image_id);
				set_post_thumbnail($post_id, $image_id);
			}
			$this->add_alt_to_images($image_id, $parent_post_title);
		}
		return $image_id;
	}
	
	public function add_alt_to_images($img_id, $title) {

		if( class_exists('Top_Rank_Seo') ) {
			
			if( Top_Rank_Admin::tpr_get_options( 'active_image_alt' ) == 1 ) {
				
				$image_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
					
				if( empty($image_alt) ) {
					update_post_meta($img_id, '_wp_attachment_image_alt', $title);
					$this->write_log("The alt feature added to the image with id $img_id");
				}				
			}
		}	
	}
	
	public function set_image_from_external_url() {
		add_filter( 'wp_get_attachment_url', array($this, 'scp_replace_attachment_url'), 10, 2);
		add_filter('posts_where', array($this, 'main_query_attachments'), 10, 2);
		add_filter('wp_head', array($this,'img_js_resize'));
	}
	
	public function img_js_resize() {
		wp_enqueue_script("ol_img_resize", plugins_url("assets/js/ext-image.js", dirname(__FILE__)), null, OL_VERSION);
	}
	
	public function main_query_attachments($where, \WP_Query $q) {
		global $wpdb;
		$ol_author = 2; //added as constant
		if (is_admin() && $q->is_main_query())
			$where .= ' AND ' . $wpdb->prefix . 'posts.post_author <> ' . $ol_author . ' ';
		else
			$where .= ' AND (' . $wpdb->prefix . 'posts.post_author <> ' . $ol_author . ' OR  (' . $wpdb->prefix . 'posts.post_author = ' . $ol_author . ' AND EXISTS (SELECT 1 FROM ' . $wpdb->prefix . 'postmeta WHERE ' . $wpdb->prefix . 'postmeta.post_id = ' . $wpdb->prefix . 'posts.id AND ' . $wpdb->prefix . 'postmeta.meta_key = "_wp_attachment_metadata")))';
		return $where;
	}	
	
	public function scp_replace_attachment_url($att_url, $att_id) {
		if ( $att_url ) {
			return $this->scp_process_url($att_url, $att_id);
		}
		
		return $att_url;
	}

	public function scp_process_url($att_url, $att_id) {
		if (!$att_id)
			return $att_url;

		// 原代码这里直接用了别的函数里的局部变量 $ol_author，PHP 8 会报 Undefined variable
		$ol_author = defined('OL_AUTHOR') ? (int) OL_AUTHOR : 2;

		$att_post = get_post($att_id);

		if (!$att_post)
			return $att_url;

		// jetpack	
		preg_match("/^http[s]*:\/\/[i][0-9][.]wp.com\//", $att_url, $matches);
		if ($matches)
			return $this->scp_process_external_url($att_url, $att_id);

		// internal
		if ($att_post->post_author != $ol_author)
			return $att_url;

		$url = $att_post->guid;

		$this->scp_fix_legacy($url, $att_id);

		return $this->scp_process_external_url($url, $att_id);
	}

	public function scp_process_external_url($url, $att_id) {
		$post_id = get_post($att_id)->post_parent;

		if (!$post_id)
			return $url;

		$post_thumbnail_id = get_post_thumbnail_id($post_id);
		$post_thumbnail_id = $post_thumbnail_id ? $post_thumbnail_id : get_term_meta($post_id, 'thumbnail_id', true);
		$featured = $post_thumbnail_id == $att_id ? 1 : 0;

		if (!$featured)
			return $url;

		// avoid duplicated call
		if (isset($_POST[$url]))
			return $url;

		$parameters = array();
		$parameters['att_id'] = $att_id;
		$parameters['post_id'] = $post_id;
		$parameters['featured'] = $featured;

		$_POST[$url] = $parameters;
		return $url;
	}	

	public function scp_fix_legacy($url, $att_id) {
		if (strpos($url, ';') === false)
			return;
		$att_url = get_post_meta($att_id, '_wp_attached_file');
		$att_url = is_array($att_url) ? $att_url[0] : $att_url;
		if ($this->scp_starts_with($att_url, ';http') || $this->scp_starts_with($att_url, ';/'))
			update_post_meta($att_id, '_wp_attached_file', $url);
	}

	public function scp_starts_with($text, $substr) {
		return substr($text, 0, strlen($substr)) === $substr;
	}	
	
	public function thumbnail_url_external_save( $pid, $url, $alt, $featured = true ) {
		
		if( $featured ) {
			if ( ! empty( $url ) ) {
				update_post_meta( $pid, '_ol_thumbnail_ext_url', esc_url($url) );
				if ( ! get_post_meta( $pid, '_thumbnail_id', TRUE ) ) {
					update_post_meta( $pid, '_thumbnail_id', 'by_url' );
				}
			} elseif ( get_post_meta( $pid, '_ol_thumbnail_ext_url', TRUE ) ) {
				delete_post_meta( $pid, '_ol_thumbnail_ext_url' );
				if ( get_post_meta( $pid, '_thumbnail_id', TRUE ) === 'by_url' ) {
					delete_post_meta( $pid, '_thumbnail_id' );
				}
			}
		}
		
		return $this->save_url_to_thumbnail( $pid, $alt, $featured, $url );
				
	}	
	
	public function save_url_to_thumbnail( $post_id, $alt, $featured, $url ) {
        global $wpdb;
		$posts = $wpdb->prefix . 'posts';
		
        if( $featured ) {
			$att_id = get_post_thumbnail_id($post_id);
			$url = get_post_meta( $post_id, '_ol_thumbnail_ext_url', TRUE );
		} else {
			global $wpdb;
			$query = $wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type='attachment' AND guid='%s'", $url );
			$att_id = $wpdb->get_var($query);			
		}
		
		// update
		if (! empty( $att_id )) {
			update_post_meta($att_id, '_wp_attached_file', $url);
			update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
			$wpdb->update($posts, $set = array('post_title' => $alt, 'guid' => $url), $where = array('id' => $att_id), null, null);
		}
		// insert
		else {
			$value = $this->get_formatted_value($url, $alt, $post_id);
			$this->insert_attachment_by($value);
			$att_id = $wpdb->insert_id;
			if( $featured ) {
				update_post_meta($post_id, '_thumbnail_id', $att_id);
			}
			update_post_meta($att_id, '_wp_attached_file', $url);
			update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
		}
		
		return $att_id;
	}
	
    public function get_formatted_value($url, $alt, $post_parent) {
        $author = 77777;
		return "(" . $author . ", '" . $url . "', '" . str_replace("'", "", $alt) . "', 'image/jpeg', 'attachment', 'inherit', '" . $post_parent . "', now(), now(), now(), now(), '', '', '', '', '')";
    }

    public function insert_attachment_by($value) {
        global $wpdb;
		$posts = $wpdb->prefix . 'posts';
		$wpdb->get_results("
            INSERT INTO " . $posts . " (post_author, guid, post_title, post_mime_type, post_type, post_status, post_parent, post_date, post_date_gmt, post_modified, post_modified_gmt, post_content, post_excerpt, to_ping, pinged, post_content_filtered) 
            VALUES " . str_replace('\\', '', $value));
    }	
	
	public function create_absolute_url($rel, $base, $html_base) {
		$rel = trim($rel);
		$base = strtolower(trim($base));
		if (substr($rel, 0, 11) == 'data:image/') {
			return $rel;
		}
		
		if (!empty($html_base)) {
			$base = $html_base;
		}
		return str_replace(" ", "%20", WP_Http::make_absolute_url($rel, $base));
	}
	
	public static function write_log($message, $is_error = false) {
		$folder = plugin_dir_path(__FILE__) . "../logs";
		$handle = fopen($folder . DIRECTORY_SEPARATOR . "logs.txt", "a");
		if (!is_string($message)) {
			$message = print_r($message, true);
		}
		if ($is_error) {
			$message = PHP_EOL . " === Scrapes Warning === " . PHP_EOL . $message . PHP_EOL . " === Scrapes Warning === ";
		}
		fwrite($handle, current_time('mysql') . " TASK ID: " . self::$task_id . " - PID: " . getmypid() . " - RAM: " . (round(memory_get_usage() / (1024 * 1024), 2)) . "MB - " . get_current_blog_id() . " " . $message . PHP_EOL);
		if ((filesize($folder . DIRECTORY_SEPARATOR . "logs.txt") / 1024 / 1024) >= 10) {
			fclose($handle);
			unlink($folder . DIRECTORY_SEPARATOR . "logs.txt");
			$handle = fopen($folder . DIRECTORY_SEPARATOR . "logs.txt", "a");
			fwrite($handle, current_time('mysql') . " - " . getmypid() . " - " . self::system_info() . PHP_EOL);
		}
		fclose($handle);
	}
	
	public static function system_info() {
		global $wpdb;
		
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$system_info = "";
		$system_info .= "Website Name: " . get_bloginfo() . PHP_EOL;
		$system_info .= "Wordpress URL: " . site_url() . PHP_EOL;
		$system_info .= "Site URL: " . home_url() . PHP_EOL;
		$system_info .= "Wordpress Version: " . get_bloginfo('version') . PHP_EOL;
		$system_info .= "Multisite: " . (is_multisite() ? "yes" : "no") . PHP_EOL;
		$system_info .= "Theme: " . wp_get_theme() . PHP_EOL;
		$system_info .= "PHP Version: " . phpversion() . PHP_EOL;
		$system_info .= "PHP Extensions: " . json_encode(get_loaded_extensions()) . PHP_EOL;
		$system_info .= "MySQL Version: " . $wpdb->db_version() . PHP_EOL;
		$system_info .= "Server Info: " . $_SERVER['SERVER_SOFTWARE'] . PHP_EOL;
		$system_info .= "WP Memory Limit: " . WP_MEMORY_LIMIT . PHP_EOL;
		$system_info .= "WP Admin Memory Limit: " . WP_MAX_MEMORY_LIMIT . PHP_EOL;
		$system_info .= "PHP Memory Limit: " . ini_get('memory_limit') . PHP_EOL;
		$system_info .= "Wordpress Plugins: " . json_encode(get_plugins()) . PHP_EOL;
		$system_info .= "Wordpress Active Plugins: " . json_encode(get_option('active_plugins')) . PHP_EOL;
		return $system_info;
	}
	
	public static function disable_plugin() {
		if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(OL_PLUGIN_PATH . 'ol_scrapes.php'))) {
			deactivate_plugins(plugin_basename(OL_PLUGIN_PATH . 'ol_scrapes.php'));
			if (isset($_GET['activate'])) {
				unset($_GET['activate']);
			}
		}
	}
	
	public static function show_notice() {
		load_plugin_textdomain('ol-scrapes', false, dirname(plugin_basename(__FILE__)) . '/../languages');
		$msgs = get_transient("scrape_msg");
		if (!empty($msgs)) :
			foreach ($msgs as $msg) :
				?>
                <div class="notice notice-error">
                    <p><strong>Scrapes: </strong><?php echo $msg; ?> <a
                                href="<?php echo add_query_arg('post_type', 'scrape', admin_url('edit.php')); ?>"><?php _e("View All Scrapes", "ol-scrapes"); ?></a>.
                    </p>
                </div>
				<?php
			endforeach;
		endif;
		
		$msgs = get_transient("scrape_msg_req");
		if (!empty($msgs)) :
			foreach ($msgs as $msg) :
				?>
                <div class="notice notice-error">
                    <p><strong>Scrapes: </strong><?php echo $msg; ?></p>
                </div>
				<?php
			endforeach;
		endif;
		
		$msgs = get_transient("scrape_msg_set");
		if (!empty($msgs)) :
			foreach ($msgs as $msg) :
				?>
                <div class="notice notice-error">
                    <p><strong>Scrapes: </strong><?php echo $msg; ?></p>
                </div>
				<?php
			endforeach;
		endif;
		
		$msgs = get_transient("scrape_msg_set_success");
		if (!empty($msgs)) :
			foreach ($msgs as $msg) :
				?>
                <div class="notice notice-success">
                    <p><strong>Scrapes: </strong><?php echo $msg; ?></p>
                </div>
				<?php
			endforeach;
		endif;
		
		delete_transient("scrape_msg");
		delete_transient("scrape_msg_req");
		delete_transient("scrape_msg_set");
		delete_transient("scrape_msg_set_success");
	}
	
	public function custom_column() {
		add_filter('manage_' . 'scrape' . '_posts_columns', array($this, 'add_status_column'));
		add_action('manage_' . 'scrape' . '_posts_custom_column', array($this, 'show_status_column'), 10, 2);
		add_filter('post_row_actions', array($this, 'remove_row_actions'), 10, 2);
		add_filter('manage_' . 'edit-scrape' . '_sortable_columns', array($this, 'add_sortable_column'));
	}
	
	public function add_sortable_column() {
		return array(
			'name' => 'title'
		);
	}
	
	public function custom_start_stop_action() {
		add_action('load-edit.php', array($this, 'scrape_custom_actions'));
	}
	
	public function scrape_custom_actions() {
		$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : null;
		$action = isset($_REQUEST['scrape_action']) ? $_REQUEST['scrape_action'] : null;
		$post_id = isset($_REQUEST['scrape_id']) ? intval($_REQUEST['scrape_id']) : null;
		if (wp_verify_nonce($nonce, 'scrape_custom_action') && isset($post_id)) {
			
			if ($action == 'stop_scrape') {
				$my_post = array();
				$my_post['ID'] = $post_id;
				$my_post['post_date_gmt'] = date("Y-m-d H:i:s");
				wp_update_post($my_post);
				$this->write_log($post_id . " stop button clicked."); 
			} else {
				if ($action == 'start_scrape') {
					update_post_meta($post_id, 'scrape_workstatus', 'waiting');
					update_post_meta($post_id, 'scrape_run_count', 0);
					update_post_meta($post_id, 'scrape_start_time', '');
					update_post_meta($post_id, 'scrape_end_time', '');
					update_post_meta($post_id, 'scrape_last_scrape', '');
					update_post_meta($post_id, 'scrape_task_id', $post_id);
					$this->handle_cron_job($_REQUEST['scrape_id']);
					$this->write_log($post_id . " start button clicked."); 
				} else {
					if ($action == 'duplicate_scrape') {
						$post = get_post($post_id, ARRAY_A);
						$post['ID'] = 0;
						$insert_id = wp_insert_post($post);
						$post_meta = get_post_meta($post_id);
						foreach ($post_meta as $name => $value) {
							update_post_meta($insert_id, $name, get_post_meta($post_id, $name, true));
						}
						update_post_meta($insert_id, 'scrape_workstatus', 'waiting');
						update_post_meta($insert_id, 'scrape_run_count', 0);
						update_post_meta($insert_id, 'scrape_start_time', '');
						update_post_meta($insert_id, 'scrape_end_time', '');
						update_post_meta($insert_id, 'scrape_last_scrape', '');
						update_post_meta($insert_id, 'scrape_task_id', $insert_id);
						$this->write_log($post_id . " duplicate button clicked."); 
					}
				}
			}
			wp_redirect(add_query_arg('post_type', 'scrape', admin_url('/edit.php')));
			exit;
		}
	}
	
	public function remove_row_actions($actions, $post) {
		if ($post->post_type == 'scrape') {
			unset($actions);
			return array(
				'' => ''
			);
		}
		return $actions;
	}
	
	public function add_status_column($columns) {
		unset($columns['title']);
		unset($columns['date']);
		$columns['name'] = __('Name', "ol-scrapes");
		$columns['status'] = __('Status', "ol-scrapes");
		$columns['schedules'] = __('Schedules', "ol-scrapes");
		$columns['actions'] = __('Actions', "ol-scrapes");
		return $columns;
	}
	
	public function show_status_column($column_name, $post_ID) {
		clean_post_cache($post_ID);
		$post_status = get_post_status($post_ID);
		$post_title = get_post_field('post_title', $post_ID);
		$scrape_status = get_post_meta($post_ID, 'scrape_workstatus', true);
		$run_limit = get_post_meta($post_ID, 'scrape_run_limit', true);
		$run_count = get_post_meta($post_ID, 'scrape_run_count', true);
		$run_unlimited = get_post_meta($post_ID, 'scrape_run_unlimited', true);
		$css_class = '';
		
		if ($post_status == 'trash') {
			$status = __("Deactivated", "ol-scrapes");
			$css_class = "deactivated";
		} else {
			if ($run_count == 0 && $scrape_status == 'waiting') {
				$status = __("Preparing", "ol-scrapes");
				$css_class = "preparing";
			} else {
				if ((!empty($run_unlimited) || $run_count < $run_limit) && $scrape_status == 'waiting') {
					$status = __("Waiting next run", "ol-scrapes");
					$css_class = "wait_next";
				} else {
					if (((!empty($run_limit) && $run_count < $run_limit) || (!empty($run_unlimited))) && $scrape_status == 'running') {
						$status = __("Running", "ol-scrapes");
						$css_class = "running";
					} else {
						if (empty($run_unlimited) && $run_count == $run_limit && $scrape_status == 'waiting') {
							$status = __("Complete", "ol-scrapes");
							$css_class = "complete";
						}
					}
				}
			}
		}
		
		if ($column_name == 'status') {
			echo "<span class='ol_status ol_status_$css_class'>" . $status . "</span>";
		}
		
		if ($column_name == 'name') {
			echo "<p><strong><a href='" . get_edit_post_link($post_ID) . "'>" . $post_title . "</a></strong></p>" . "<p><span class='id'>ID: " . $post_ID . "</span></p>";
		}
		
		if ($column_name == 'schedules') {
			$last_run = get_post_meta($post_ID, 'scrape_start_time', true) != "" ? get_post_meta($post_ID, 'scrape_start_time', true) : __("None", "ol-scrapes");
			$last_complete = get_post_meta($post_ID, 'scrape_end_time', true) != "" ? get_post_meta($post_ID, 'scrape_end_time', true) : __("None", "ol-scrapes");
			$last_scrape = get_post_meta($post_ID, 'scrape_last_scrape', true) != "" ? get_post_meta($post_ID, 'scrape_last_scrape', true) : __("None", "ol-scrapes");
			$count_posts = get_post_meta($post_ID, 'scrape_count_post', true) != "" ? get_post_meta($post_ID, 'scrape_count_post', true) : 0;
			$run_count_progress = $run_count;
			if ($run_unlimited == "") {
				$run_count_progress .= " / " . $run_limit;
			}
			
			$offset = get_option('gmt_offset') * 3600;
			$date = date("Y-m-d H:i:s", wp_next_scheduled("scrape_event", array($post_ID)) + $offset);
			if (strpos($date, "1970-01-01") !== false) {
				$date = __("No Schedule", "ol-scrapes");
			}
			echo "<p><label>" . __("Last Run:", "ol-scrapes") . "</label> <span>" . $last_run . "</span></p>" . "<p><label>" . __("Last Complete:", "ol-scrapes") . "</label> <span>" . $last_complete . "</span></p>" . "<p><label>" . __("Last Scrape:", "ol-scrapes") . "</label> <span>" . $last_scrape . "</span></p>" . "<p><label>" . __("Next Run:", "ol-scrapes") . "</label> <span>" . $date . "</span></p>" . "<p><label>" . __("Total Run:", "ol-scrapes") . "</label> <span>" . $run_count_progress . "</span></p>" . "<p><label>" . __("Count Post:", "ol-scrapes") . "</label> <span>" . $count_posts . "</span></p>";
		}
		if ($column_name == "actions") {
			$nonce = wp_create_nonce('scrape_custom_action');
			$untrash = wp_create_nonce('untrash-post_' . $post_ID);
			echo ($post_status != 'trash' ? "<a href='" . get_edit_post_link($post_ID) . "' class='button edit'><i class='icon ion-android-create'></i>" . __("Edit", "ol-scrapes") . "</a>" : "") . ($post_status != 'trash' ? "<a href='" . admin_url("edit.php?post_type=scrape&scrape_id=$post_ID&_wpnonce=$nonce&scrape_action=start_scrape") . "' class='button run ol_status_" . $css_class . "'><i class='icon ion-play'></i>" . __("Run", "ol-scrapes") . "</a>" : "") . ($post_status != 'trash' ? "<a href='" . admin_url("edit.php?post_type=scrape&scrape_id=$post_ID&_wpnonce=$nonce&scrape_action=stop_scrape") . "' class='button stop ol_status_" . $css_class . "'><i class='icon ion-pause'></i>" . __("Pause", "ol-scrapes") . "</a>" : "") . ($post_status != 'trash' ? "<br><a href='" . admin_url("edit.php?post_type=scrape&scrape_id=$post_ID&_wpnonce=$nonce&scrape_action=duplicate_scrape") . "' class='button duplicate'><i class='icon ion-android-add-circle'></i>" . __("Copy", "ol-scrapes") . "</a>" : "") . ($post_status != 'trash' ? "<a href='" . get_delete_post_link($post_ID) . "' class='button trash'><i class='icon ion-trash-b'></i>" . __("Trash", "ol-scrapes") . "</a>" : "<a href='" . admin_url('post.php?post=' . $post_ID . '&action=untrash&_wpnonce=' . $untrash) . "' class='button restore'><i class='icon ion-forward'></i>" . __("Restore", "ol-scrapes") . "</a>");
		}
	}
	
	public function convert_readable_html($html_string) {
		require_once "class-readability.php";
		
		$readability = new Readability($html_string);
		$readability->debug = false;
		$readability->convertLinksToFootnotes = false;
		$result = $readability->init();
		if ($result) {
			$content = $readability->getContent()->innerHTML;
			return $content;
		} else {
			return '';
		}
	}
	
	public function remove_publish() {
		add_action('admin_menu', array($this, 'remove_other_metaboxes'));
		add_filter('get_user_option_screen_layout_' . 'scrape', array($this, 'screen_layout_post'));
	}
	
	public function remove_other_metaboxes() {
		remove_meta_box('submitdiv', 'scrape', 'side');
		remove_meta_box('slugdiv', 'scrape', 'normal');
		remove_meta_box('postcustom', 'scrape', 'normal');
	}
	
	public function screen_layout_post() {
		add_filter('screen_options_show_screen', '__return_false');
		return 1;
	}
	
	public function replace_img_org_to_translate($html_string, $org_html_string) {
		
		preg_match_all('/<\/?img(|\s+[^>]+)>/', $html_string, $old_images);
		preg_match_all('/<\/?img(|\s+[^>]+)>/', $org_html_string, $new_images);
		
		$post_content = str_ireplace(array_values($old_images[0]), array_values($new_images[0]), $html_string);
		
		return $post_content;
	}
	
	public function get_orginal_image_link_from_content($orgi_html) {
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		@$doc->loadHTML('<?xml encoding="utf-8" ?>' . $orgi_html);
		$org_image = $doc->getElementsByTagName('img');
		if ($org_image->length) {
			foreach ($org_image as $itemnew) {
				if ($itemnew->getAttribute('data-lazy-src') != '') {
					$get_image[] = $itemnew->getAttribute('data-lazy-src');
				} elseif ($itemnew->getAttribute('data-src') != '') {
					$get_image[] = $itemnew->getAttribute('data-src');
				} elseif ($itemnew->getAttribute('src') != '') {
					$get_image[] = $itemnew->getAttribute('src');
				}
			}
		}

		return $get_image;
	}
	
	public function fix_image_link_content($html_string, $original_html_string) {
		if (empty($html_string)) {
			return "";
		}
		$new_image = $this->get_orginal_image_link_from_content($original_html_string);
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		@$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html_string);
		$imgs = $doc->getElementsByTagName('img');
		if ($imgs->length) {
			$i = 0;
			foreach ($imgs as $itemold) {
				if ($itemold->getAttribute('src') != '') {
					$old_image[] = $itemold->getAttribute('src');
					$itemold->setAttribute('src', $new_image[$i]);
				}
				$i++;
			}
		}		
		
		return $this->save_html_clean($doc);
	}
	
	public function convert_html_links($html_string, $base_url, $html_base_url, $title = '') {
		if (empty($html_string)) {
			return "";
		}
		$toprank = false;
		if( class_exists('Top_Rank_Seo') ) {
			if( Top_Rank_Admin::tpr_get_options( 'active_image_alt' ) == 1 ) {
				$toprank = true;
			}
		}
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		@$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html_string);
		$imgs = $doc->getElementsByTagName('img');
		if ($imgs->length) {
			foreach ($imgs as $item) {
				if ($item->getAttribute('src') != '') {
					$item->setAttribute('src', $this->create_absolute_url($item->getAttribute('src'), $base_url, $html_base_url));
				}
				if( $toprank && !empty($title) ) {
					if($item->hasAttribute('alt')) {
						if ($item->getAttribute('alt') == '') {
							$item->setAttribute('alt', $title);
						}
					} else {
						$item->setAttribute('alt', $title);
					}
				}
			}
		}		
		
		$a = $doc->getElementsByTagName('a');
		if ($a->length) {
			foreach ($a as $item) {
				if ($item->getAttribute('href') != '') {
					$item->setAttribute('href', $this->create_absolute_url($item->getAttribute('href'), $base_url, $html_base_url));
				}
			}
		}
		
		return $this->save_html_clean($doc);
	}
	
	public function convert_str_to_woo_decimal($money) {
		$decimal_separator = stripslashes(get_option('woocommerce_price_decimal_sep'));
		$thousand_separator = stripslashes(get_option('woocommerce_price_thousand_sep'));
		
		$money = preg_replace("/[^\d\.,]/", '', $money);
		$money = str_replace($thousand_separator, '', $money);
		$money = str_replace($decimal_separator, '.', $money);
		return $money;
	}

    public function spin_content_with_thebestspinner($email, $password, $content) {

        $output = wp_remote_post('http://thebestspinner.com/api.php', array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'action' => 'authenticate',
                    'format' => 'php',
                    'username' => $email,
                    'password' => $password,
                    'rewrite' => 1
                ),
                'cookies' => array()
            )
        );

        $output = unserialize($output);

        $this->write_log('best spinner result ' . $output);
        if ($output['success'] == true) {
            $content = wp_remote_post('http://thebestspinner.com/api.php', array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => array(
                        'session' => $output['session'],
                        'format' => 'php',
                        'text' => $content,
                        'action' => 'rewriteText'
                    ),
                    'cookies' => array()
                )
            );
        }
        return $content;
    }
	
	public function download_images_from_html_string($html_string, $post_id) {
		if (empty($html_string)) {
			return "";
		}
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		@$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html_string);
		$imgs = $doc->getElementsByTagName('img');
		if ($imgs->length) {
			foreach ($imgs as $item) {
				
				$image_url = $item->getAttribute('src');
				global $wpdb;
				$query = "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE '" . md5($image_url) . "%' and post_type ='attachment' and post_parent = $post_id";
				$count = $wpdb->get_var($query);
				
				$this->write_log("download image id for post $post_id is " . $count);
				
				if (empty($count)) {
					$attach_id = $this->generate_featured_image($image_url, $post_id, false);
					$item->setAttribute('src', wp_get_attachment_url($attach_id));
				} else {
					$item->setAttribute('src', wp_get_attachment_url($count));
				}
				$item->removeAttribute('srcset');
				$item->removeAttribute('sizes');
				unset($image_url);
			}
		}
		
		return $this->save_html_clean($doc);
	}
	
	public function save_html_clean($domdocument) {
		$mock = new DOMDocument();
		$body = $domdocument->getElementsByTagName('body')->item(0);
		foreach ($body->childNodes as $child) {
			$mock->appendChild($mock->importNode($child, true));
		}
		return html_entity_decode($mock->saveHTML(), ENT_COMPAT, "UTF-8");
	}
	
	public static function check_exec_works() {
		$e_word = E_WORD;
		if (function_exists($e_word)) {
			@$e_word('pwd', $output, $return);
			return $return == 0;
		} else {
			return false;
		}
	}
	
	public function check_terminate($start_time, $modify_time, $post_id) {
		clean_post_cache($post_id);
		
		if ($start_time != get_post_meta($post_id, "scrape_start_time", true) && get_post_meta($post_id, 'scrape_stillworking', true) == 'terminate') {
			$this->write_log("if not completed in time terminate is selected. finishing this incomplete task.", true);
			return true;
		}
		
		if (get_post_status($post_id) == 'trash' || get_post_status($post_id) === false) {
			$this->write_log("post sent to trash or status read failure. remaining urls will not be scraped.", true);
			return true;
		}
		
		$check_modify_time = get_post_modified_time('U', null, $post_id);
		if ($modify_time != $check_modify_time && $check_modify_time !== false) {
			$this->write_log("post modified. remaining urls will not be scraped.", true);
			return true;
		}
		
		return false;
	}
	
	public function trimmed_templated_value($prefix, &$meta_vals, &$xpath, $post_date, $save_post_date_jalali, $url, $meta_input, $rss_item = null) {
		$value = '';
		if (isset($meta_vals[$prefix]) || isset($meta_vals[$prefix . "_type"])) {
			if (isset($meta_vals[$prefix . "_type"]) && $meta_vals[$prefix . "_type"][0] == 'feed') {
				$value = $rss_item['post_title'];
				if ($meta_vals['scrape_spin_enable'][0]) {
				    $value = $this->spin_content_with_thebestspinner($meta_vals['scrape_spin_email'], $meta_vals['scrape_spin_password'], $value);
                }
				if ($meta_vals['scrape_translate_enable'][0]) {
					if (class_exists('TS_Translate')) {
					    $value = TS_Translate::translate($meta_vals['scrape_translate_source'][0], $meta_vals['scrape_translate_target'][0], $value);
					    $this->write_log("translated $prefix : $value");
					}
				}
			} else {
				if (!empty($meta_vals[$prefix][0])) {
					$node = $xpath->query($meta_vals[$prefix][0]);
					if ($node->length) {
						if (!empty($meta_vals['scrape_excerpt_allowhtml'][0]) && $prefix == 'scrape_excerpt') {
							$node = $node->item(0);
							$value = $node->ownerDocument->saveXML($node);
						} else {
							$value = $node->item(0)->nodeValue;
						}
						$this->write_log($prefix . " : " . $value);
                        if ($meta_vals['scrape_spin_enable'][0]) {
                            $value = $this->spin_content_with_thebestspinner($meta_vals['scrape_spin_email'][0], $meta_vals['scrape_spin_password'][0], $value);
                        }
						if ($meta_vals['scrape_translate_enable'][0]) {
							if (class_exists('TS_Translate')) {
							    $value = TS_Translate::translate($meta_vals['scrape_translate_source'][0], $meta_vals['scrape_translate_target'][0], $value);
							}
						}
						$this->write_log("translated $prefix : $value");
						
					} else {
						$value = '';
						$this->write_log("URL: " . $url . " XPath: " . $meta_vals[$prefix][0] . " returned empty for $prefix", true);
					}
				} else {
					$value = '';
				}
			}
			
			if (!empty($meta_vals[$prefix . '_regex_status'][0])) {
				$regex_finds = unserialize($meta_vals[$prefix . '_regex_finds'][0]);
				$regex_replaces = unserialize($meta_vals[$prefix . '_regex_replaces'][0]);
				if (!empty($regex_finds)) {
					$regex_combined = array_combine($regex_finds, $regex_replaces);
					foreach ($regex_combined as $regex => $replace) {
						$this->write_log("$prefix before regex: " . $value);
						$value = preg_replace("/" . str_replace("/", "\/", $regex) . "/isu", $replace, $value);
						$this->write_log("$prefix after regex: " . $value);
					}
				}
			}
		}
		if (isset($meta_vals[$prefix . '_template_status']) && !empty($meta_vals[$prefix . '_template_status'][0])) {
			$template = $meta_vals[$prefix . '_template'][0];
			$this->write_log($prefix . " : " . $template);
			$value = str_replace("[scrape_value]", $value, $template);
			if(class_exists('TS_Options')) {
				if(function_exists('wp_date')) {			
					$value = str_replace("[scrape_date]", $save_post_date_jalali, $value);
				}
			} else {
				$value = str_replace("[scrape_date]", $post_date, $value);
			}
			$value = str_replace("[scrape_url]", $url, $value);
			
			preg_match_all('/\[scrape_meta name="([^"]*)"\]/', $value, $matches);
			
			$full_matches = $matches[0];
			$name_matches = $matches[1];
			if (!empty($full_matches)) {
				$combined = array_combine($name_matches, $full_matches);
				
				foreach ($combined as $meta_name => $template_string) {
					$val = $meta_input[$meta_name];
					$value = str_replace($template_string, $val, $value);
				}
			}
			$this->write_log("after template replacements: " . $value);
		}
		return trim($value);
	}
	
	public function translate_months($str) {
		$languages = array(
			"fa" => array(
				"فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"
			), "en" => array(
				"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
			), "de" => array(
				"Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"
			), "fr" => array(
				"Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
			), "tr" => array(
				"Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"
			), "nl" => array(
				"Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"
			), "id" => array(
				"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"
			), "pt-br" => array(
				"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
			)
		);
		
		$languages_abbr = $languages;
		
		foreach ($languages_abbr as $locale => $months) {
			$languages_abbr[$locale] = array_map(array($this, 'month_abbr'), $months);
		}
		
		foreach ($languages as $locale => $months) {
			$str = str_ireplace($months, $languages["en"], $str);
		}
		foreach ($languages_abbr as $locale => $months) {
			$str = str_ireplace($months, $languages_abbr["en"], $str);
		}
		
		return $str;
	}
	
	public static function month_abbr($month) {
		return mb_substr($month, 0, 3);
	}
	
	public function settings_page() {
		add_action('admin_init', array($this, 'settings_page_functions'));
		//add_action('admin_init', array($this, 'init_admin_fonts'));
	}
	
	public function settings_page_functions() {
		//wp_load_template(plugin_dir_path(__FILE__) . "../views/scrape-meta-box.php");
		add_action('admin_post_save_scrapes_settings', $this->settings);
		add_action('admin_post_remove_pc', $this->remove_pc);		
	}
	
	public function template_calculator($str) {
		try {
			$this->write_log("calc string " . $str);
			$fn = create_function("", "return ({$str});");
			return $fn !== false ? $fn() : "";
		} catch (ParseError $e) {
			return '';
		}
	}
	
	public function add_translations() {
		add_action('plugins_loaded', array($this, 'load_languages'));
		add_action('plugins_loaded', array($this, 'load_translations'));
	}
	
	public function load_languages() {
		$path = dirname(plugin_basename(__FILE__)) . '/../languages/';
		load_plugin_textdomain('ol-scrapes', false, $path);
	}
	
	public function load_translations() {
		global $translates;
		
		$translates = array(
			__('An error occurred while connecting to server. Please check your connection.', 'ol-scrapes'),
            __('Domain name is not matching with your site. Please check your domain name.', 'ol-scrapes'),
            __('Purchase code is validated.', 'ol-scrapes'),
            __('Purchase code is removed from settings.', 'ol-scrapes'),
			'Post fields are missing. Please fill the required fields.' => __('Post fields are missing. Please fill the required fields.', 'ol-scrapes'),
			'Purchase code is not approved. Please check your purchase code.' => __('Purchase code is not approved. Please check your purchase code.', 'ol-scrapes'),
            'Purchase code is already exists. Please provide another purchase code.' => __('Purchase code is already exists. Please provide another purchase code.', 'ol-scrapes'),
            'Please complete your payment or contact to Octolooks staff.' => __('Please complete your payment or contact to Octolooks staff.', 'ol-scrapes')
		);
	}
	
	private function return_html_args($meta_vals = null) {
		$args = array(
			'sslverify' => false, 'timeout' => is_null($meta_vals) ? 60 : $meta_vals['scrape_timeout'][0], 'user-agent' => get_site_option('scrape_user_agent'), 'redirection' => 10//'httpversion' => '1.1',
			//'headers' => array('Connection' => 'keep-alive')
		);
		if (isset($_GET['cookie_names'])) {
			$args['cookies'] = array_combine(array_values($_GET['cookie_names']), array_values($_GET['cookie_values']));
		}
		if (!empty($meta_vals['scrape_cookie_names'])) {
			$args['cookies'] = array_combine(array_values(unserialize($meta_vals['scrape_cookie_names'][0])), array_values(unserialize($meta_vals['scrape_cookie_values'][0])));
		}
		return $args;
	}
	
	public function ordered_html_tag($response) {
		$response = preg_replace('/<\/\s[a-z]+>|\s[a-z0-9]+>|\s[a-z]+\s>/', '', $response);
		return $response;
	}

	public function scrape_url_shorten( $url, $length = 35 ) {
		$stripped  = str_replace( array( 'https://', 'http://', 'www.' ), '', $url );
		$short_url = untrailingslashit( $stripped );
	 
		if ( strlen( $short_url ) > $length ) {
			$short_url = substr( $short_url, 0, $length - 3 );
		}
		return $short_url;
	}	
	
	public function remove_externals() {
		add_action('admin_head', array($this, 'remove_external_components'), 100);
	}
	
	public function remove_external_components() {
		global $hook_suffix;
		global $wp_meta_boxes;
		if (is_object(get_current_screen()) && get_current_screen()->post_type == "scrape") {
			if (in_array($hook_suffix, array('post.php', 'post-new.php', 'scrape_page_scrapes-support', 'edit.php'))) {
				$wp_meta_boxes['scrape'] = array();
				remove_all_filters('manage_posts_columns');
				remove_all_actions('manage_posts_custom_column');
				remove_all_actions('admin_notices');
				add_action('admin_notices', array('OL_Scrapes', 'show_notice'));
			}
		}
	}
	
	public function set_per_page_value() {
		add_filter('get_user_option_edit_' . 'scrape' . '_per_page', array($this, 'scrape_edit_per_page'), 10, 3);
	}
	
	public function scrape_edit_per_page($result, $option, $user) {
		return 999;
	}

    /**
     * 删除HTML中的所有按钮元素
     * @param string $html 原始HTML
     * @return string 清理后的HTML
     */
    private function remove_all_buttons_from_html($html) {
        // 删除各种按钮标签及其内容
        $patterns = array(
            '/<button[^>]*>.*?<\/button>/is',                           // <button>标签
            '/<a[^>]*class="[^"]*btn[^"]*"[^>]*>.*?<\/a>/is',          // class包含btn的a标签
            '/<a[^>]*class="[^"]*button[^"]*"[^>]*>.*?<\/a>/is',       // class包含button的a标签
            '/<div[^>]*class="[^"]*btn[^"]*"[^>]*>.*?<\/div>/is',      // class包含btn的div
            '/<div[^>]*class="[^"]*button[^"]*"[^>]*>.*?<\/div>/is',   // class包含button的div
            '/<span[^>]*class="[^"]*btn[^"]*"[^>]*>.*?<\/span>/is',    // class包含btn的span
            '/<span[^>]*class="[^"]*button[^"]*"[^>]*>.*?<\/span>/is', // class包含button的span
            '/<input[^>]*type="button"[^>]*>/is',                       // input按钮
            '/<input[^>]*type="submit"[^>]*>/is',                      // submit按钮
            '/<button[^>]*>.*?<\/button>/is',                          // 所有button
        );
        
        foreach ($patterns as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }
        
        // 删除可能残留的空div和span
        $html = preg_replace('/<div[^>]*>\s*<\/div>/is', '', $html);
        $html = preg_replace('/<span[^>]*>\s*<\/span>/is', '', $html);
        
        return $html;
    }
    
    /**
     * 提取夸克网盘真实下载链接（明文格式）
     * @param string $html 包含夸克链接的HTML
     * @param array $meta_vals 采集任务配置
     * @return string 明文格式的下载链接
     */
    private function extract_quark_real_link($html, $meta_vals) {
        // 1. 先删除所有按钮
        $html = $this->remove_all_buttons_from_html($html);
        
        // 2. 提取夸克分享链接
        preg_match_all('/https?:\/\/pan\.quark\.cn\/s\/[a-zA-Z0-9]+/i', $html, $matches);
        
        if (empty($matches[0])) {
            $this->write_log("未检测到夸克网盘链接");
            return $html;
        }
        
        $quark_share_url = $matches[0][0];
        $this->write_log("检测到夸克分享链接: " . $quark_share_url);
        
        // 3. 请求夸克页面，提取真实下载链接
        $args = $this->return_html_args($meta_vals);
        $args['timeout'] = 30;
        
        $response = wp_remote_get($quark_share_url, $args);
        
        if (is_wp_error($response)) {
            $this->write_log("获取夸克页面失败: " . $response->get_error_message(), true);
            // 返回明文分享链接
            return "夸克网盘分享链接：{$quark_share_url}（需打开后保存到自己的网盘下载）";
        }
        
        $page_content = wp_remote_retrieve_body($response);
        $real_download_url = '';
        
        // 方法1: 从 window.__NUXT__ 提取
        if (preg_match('/window\.__NUXT__\s*=\s*({.*?});/s', $page_content, $nux_match)) {
            $nux_data = json_decode($nux_match[1], true);
            if ($nux_data && is_array($nux_data)) {
                $json_str = json_encode($nux_data);
                if (preg_match('/"downloadUrl"\s*:\s*"([^"]+)"/', $json_str, $dl_match)) {
                    $real_download_url = stripcslashes($dl_match[1]);
                    $this->write_log("从__NUXT__提取到下载链接: " . $real_download_url);
                } elseif (preg_match('/"url"\s*:\s*"([^"]+)"[^}]*"type"\s*:\s*"file"/', $json_str, $dl_match)) {
                    $real_download_url = stripcslashes($dl_match[1]);
                    $this->write_log("从__NUXT__提取到文件链接: " . $real_download_url);
                }
            }
        }
        
        // 方法2: 从 script 标签中提取
        if (empty($real_download_url)) {
            preg_match_all('/<script[^>]*>([^<]+)<\/script>/', $page_content, $script_matches);
            foreach ($script_matches[1] as $script) {
                if (preg_match('/"downloadUrl":"([^"]+)"/', $script, $dl_match)) {
                    $real_download_url = stripcslashes($dl_match[1]);
                    $this->write_log("从script提取到下载链接: " . $real_download_url);
                    break;
                }
                if (preg_match('/"download_url":"([^"]+)"/', $script, $dl_match)) {
                    $real_download_url = stripcslashes($dl_match[1]);
                    $this->write_log("从script提取到下载链接: " . $real_download_url);
                    break;
                }
            }
        }
        
        // 方法3: 从 a 标签提取下载链接
        if (empty($real_download_url)) {
            if (preg_match('/<a[^>]*href="([^"]+)"[^>]*>.*?下载.*?<\/a>/i', $page_content, $dl_match)) {
                $real_download_url = $dl_match[1];
                $this->write_log("从a标签提取到下载链接: " . $real_download_url);
            }
        }
        
        // 4. 返回明文格式
        if (!empty($real_download_url) && filter_var($real_download_url, FILTER_VALIDATE_URL)) {
            $this->write_log("成功提取夸克真实下载链接: " . $real_download_url);
            return "夸克网盘下载：{$real_download_url}";
        }
        
        // 5. 无法提取真实链接时，返回分享链接
        $this->write_log("未能提取到夸克真实下载链接，使用分享链接替代", true);
        return "夸克网盘分享链接：{$quark_share_url}（需打开后保存到自己的网盘下载）";
    }
    
        /**
     * 跟随重定向获取夸克分享链接
     */
    private function follow_redirect_to_quark($url, $meta_vals) {
        $args = $this->return_html_args($meta_vals);
        $args['timeout'] = 30;
        $args['redirection'] = 5; // 允许重定向
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            $this->write_log("请求中间页失败: " . $response->get_error_message(), true);
            return '';
        }
        
        // 获取最终URL（重定向后的地址）
        $final_url = $response['http_response']->get_response_object()->url;
        $this->write_log("中间页最终跳转到: " . $final_url);
        
        return $final_url;
    }
    
    /**
     * 从夸克分享页提取真实下载链接
     */
    private function extract_quark_real_download_url($quark_url, $meta_vals) {
        $this->write_log("开始从夸克分享页提取下载链接: " . $quark_url);
        
        $args = $this->return_html_args($meta_vals);
        $args['timeout'] = 30;
        
        $response = wp_remote_get($quark_url, $args);
        
        if (is_wp_error($response)) {
            $this->write_log("获取夸克页面失败: " . $response->get_error_message(), true);
            return '';
        }
        
        $html = wp_remote_retrieve_body($response);
        $real_link = '';
        
        // 从 window.__NUXT__ 提取
        if (preg_match('/window\.__NUXT__\s*=\s*({.*?});/s', $html, $match)) {
            $data = json_decode($match[1], true);
            if ($data) {
                $json = json_encode($data);
                if (preg_match('/"downloadUrl"\s*:\s*"([^"]+)"/', $json, $dl_match)) {
                    $real_link = stripcslashes($dl_match[1]);
                } elseif (preg_match('/"url"\s*:\s*"([^"]+)"[^}]*"type"\s*:\s*"file"/', $json, $dl_match)) {
                    $real_link = stripcslashes($dl_match[1]);
                }
            }
        }
        
        // 从script标签提取
        if (empty($real_link)) {
            preg_match_all('/<script[^>]*>([^<]+)<\/script>/', $html, $scripts);
            foreach ($scripts[1] as $script) {
                if (preg_match('/"downloadUrl":"([^"]+)"/', $script, $dl_match)) {
                    $real_link = stripcslashes($dl_match[1]);
                    break;
                }
            }
        }
        
        if (!empty($real_link)) {
            $this->write_log("成功提取夸克真实下载链接: " . $real_link);
            return $real_link;
        }
        
        $this->write_log("未能提取真实下载链接", true);
        return '';
    }


    /* ================================================================
     * 网盘下载链接解析（适配源站 2026-10 改版）
     *
     * 旧版：下载按钮的 href 直接是 ?golink=base64 跳转，正则抓一下就行。
     * 新版：按钮只剩 data-xk-post / data-xk-down / data-xk-pan，
     *       真实地址必须 POST 到 admin-ajax.php（action=xk_get_download）
     *       现场换取，换回来的是 5 分钟有效的短链，再 302 到网盘分享页。
     *       这里把整条链路在服务端走完，入库的是最终网盘地址，
     *       不会因为短链过期而失效。
     * ================================================================ */

    /**
     * 取 URL 的协议 + 主机（含端口）
     */
    private function url_origin($url) {
        $p = parse_url($url);
        if (empty($p['host'])) {
            return '';
        }
        $scheme = !empty($p['scheme']) ? $p['scheme'] : 'https';
        $origin = $scheme . '://' . $p['host'];
        if (!empty($p['port'])) {
            $origin .= ':' . $p['port'];
        }
        return $origin;
    }

    /**
     * 判断是不是网盘分享地址，是则返回 array(url, pan, code)
     */
    private function classify_pan_url($url) {
        if (empty($url) || !is_string($url)) {
            return null;
        }
        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) {
            return null;
        }

        if (stripos($url, 'pan.quark.cn/s/') !== false) {
            $pan = '夸克网盘';
        } elseif (stripos($url, 'pan.xunlei.com/s/') !== false) {
            $pan = '迅雷云盘';
        } elseif (stripos($url, 'pan.baidu.com/s/') !== false) {
            $pan = '百度网盘';
        } elseif (stripos($url, 'drive.uc.cn/s/') !== false || stripos($url, 'fast.uc.cn/s/') !== false) {
            $pan = 'UC网盘';
        } elseif (stripos($url, 'aliyundrive.com/s/') !== false || stripos($url, 'alipan.com/s/') !== false) {
            $pan = '阿里云盘';
        } elseif (stripos($url, '123pan.com/s/') !== false || stripos($url, '123684.com/s/') !== false) {
            $pan = '123云盘';
        } elseif (stripos($url, 'lanzou') !== false) {
            $pan = '蓝奏云';
        } else {
            return null;
        }

        $code = '';
        $query = parse_url($url, PHP_URL_QUERY);
        if (!empty($query)) {
            parse_str($query, $params);
            foreach (array('pwd', 'password', 'code', 'extract_code', 'passcode') as $key) {
                if (!empty($params[$key])) {
                    $code = $params[$key];
                    break;
                }
            }
        }

        return array('url' => $url, 'pan' => $pan, 'code' => $code);
    }

    /**
     * 从页面里识别下载接口地址与 action，识别不到就用默认值
     */
    private function xk_detect_endpoint($page_url) {
        if (is_array($this->xk_endpoint_cache)) {
            return $this->xk_endpoint_cache;
        }

        $ajax = '';
        $action = 'xk_get_download';
        $html = $this->current_page_html;

        if (!empty($html)) {
            if (preg_match('/var\s+XKDL_AJAX\s*=\s*[\'"]([^\'"]+)[\'"]/i', $html, $m)) {
                $ajax = trim($m[1]);
            }
            if (preg_match('/function\s+xkGoDownload\s*\([^)]*\)\s*\{(.{0,1500}?)\n\s*\}/is', $html, $fn)) {
                if (preg_match('/action\s*:\s*[\'"]([A-Za-z0-9_]+)[\'"]/', $fn[1], $m2)) {
                    $action = $m2[1];
                }
            }
        }

        $origin = $this->url_origin($page_url);

        if (empty($ajax) && !empty($html) && preg_match('/[\'"]((?:https?:)?\/\/[^\'"]*admin-ajax\.php)[\'"]/i', $html, $m3)) {
            $ajax = trim($m3[1]);
        }
        if (empty($ajax)) {
            $ajax = $origin . '/wp-admin/admin-ajax.php';
        }
        if (strpos($ajax, '//') === 0) {
            $ajax = (strpos($page_url, 'https://') === 0 ? 'https:' : 'http:') . $ajax;
        } elseif (strpos($ajax, 'http') !== 0) {
            $ajax = $origin . '/' . ltrim($ajax, '/');
        }

        $this->xk_endpoint_cache = array('url' => $ajax, 'action' => $action);
        $this->write_log("下载接口: {$ajax} (action={$action})");
        return $this->xk_endpoint_cache;
    }

    /**
     * 用 data-xk-post / data-xk-down 换取一次性下载短链
     */
    private function xk_request_download_url($post, $down, $page_url, $meta_vals) {
        $post = (int) $post;
        $down = (int) $down;
        if ($post <= 0) {
            return '';
        }

        $endpoint = $this->xk_detect_endpoint($page_url);
        $args = $this->return_html_args($meta_vals);
        $args['timeout'] = 20;
        $args['headers'] = array(
            'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With' => 'XMLHttpRequest',
            'Referer'          => $page_url,
            'Origin'           => $this->url_origin($page_url),
        );
        $args['body'] = array(
            'action' => $endpoint['action'],
            'p'      => $post,
            'd'      => $down,
        );

        $response = wp_remote_post($endpoint['url'], $args);
        if (is_wp_error($response)) {
            $this->write_log("下载接口请求失败 p={$post} d={$down}: " . $response->get_error_message(), true);
            return '';
        }

        $response_body = wp_remote_retrieve_body($response);
        $json = json_decode($response_body, true);

        if (is_array($json)) {
            if (!empty($json['success']) && !empty($json['data']['url'])) {
                return trim($json['data']['url']);
            }
            $msg = '未知错误';
            if (isset($json['data']['msg'])) {
                $msg = $json['data']['msg'];
            } elseif (isset($json['data']) && is_string($json['data'])) {
                $msg = $json['data'];
            }
            $this->write_log("下载接口未返回链接 p={$post} d={$down}: {$msg}");
            return '';
        }

        if (preg_match('#https?://[^\s"\'<>]+#', $response_body, $m)) {
            return $m[0];
        }

        $this->write_log("下载接口返回异常 p={$post} d={$down}: " . substr($response_body, 0, 200), true);
        return '';
    }

    /**
     * 从 HTML（下载按钮片段或整页）解析出可用的网盘下载链接文本行
     */
    private function build_download_lines_from_html($raw_html, $page_url, $meta_vals, $enable_quark = false, $log_tag = '内容') {
        $lines = array();
        $entries = array();
        $buttons = array();
        $href_links = array();

        $collect = function ($html, $only_post = '') use (&$buttons, &$href_links) {
            if (empty($html)) {
                return;
            }
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('//a') as $a) {
                $post = trim($a->getAttribute('data-xk-post'));
                $down = trim($a->getAttribute('data-xk-down'));
                if ($post !== '') {
                    if ($only_post !== '' && $post !== (string) $only_post) {
                        continue;
                    }
                    $buttons[$post . '-' . $down] = array(
                        'post' => $post,
                        'down' => ($down === '' ? '0' : $down),
                        'pan'  => trim($a->getAttribute('data-xk-pan')),
                    );
                    continue;
                }
                if ($only_post !== '') {
                    continue;
                }
                $href = trim($a->getAttribute('href'));
                if (preg_match('#^https?://#i', $href)) {
                    $href_links[$href] = true;
                }
            }
        };

        $collect($raw_html);

        // 片段里只匹配到部分按钮时，从整页补齐同一篇文章的其余按钮
        $looks_like_box = (stripos($raw_html, 'data-xk-post') !== false
            || stripos($raw_html, 'but-download') !== false
            || stripos($raw_html, 'cdd-free-box') !== false
            || stripos($raw_html, 'custom-download-section') !== false);
        if ($looks_like_box && !empty($this->current_page_html) && preg_match('/archives\/(\d+)/', $page_url, $pm)) {
            $collect($this->current_page_html, $pm[1]);
        }

        $this->write_log("{$log_tag}：新版下载按钮 " . count($buttons) . " 个，普通链接 " . count($href_links) . " 个");

        foreach ($buttons as $btn) {
            $cache_key = $btn['post'] . '-' . $btn['down'];
            if (array_key_exists($cache_key, $this->xk_link_cache)) {
                $entry = $this->xk_link_cache[$cache_key];
            } else {
                $entry = null;
                $short_url = $this->xk_request_download_url($btn['post'], $btn['down'], $page_url, $meta_vals);
                if (!empty($short_url)) {
                    $this->write_log("{$log_tag}：按钮「{$btn['pan']}」短链 {$short_url}");
                    $final_url = $this->get_final_redirect_url($short_url, $meta_vals);
                    $entry = $this->classify_pan_url($final_url);
                    if (empty($entry)) {
                        $this->write_log("{$log_tag}：按钮「{$btn['pan']}」解析不到网盘地址: {$final_url}", true);
                    }
                } else {
                    $this->write_log("{$log_tag}：按钮「{$btn['pan']}」(p={$btn['post']} d={$btn['down']}) 没换到链接");
                }
                $this->xk_link_cache[$cache_key] = $entry;
            }
            if (empty($entry)) {
                continue;
            }
            $entries[$entry['url']] = $entry;
            $this->write_log("{$log_tag}：按钮「{$btn['pan']}」→ {$entry['url']}" . ($entry['code'] !== '' ? "（提取码 {$entry['code']}）" : ''));
        }

        foreach (array_keys($href_links) as $href) {
            if (stripos($href, 'golink=') === false
                && stripos($href, 'pay-download') === false
                && stripos($href, 'xk_dl') === false
                && stripos($href, '/go/') === false
                && $this->classify_pan_url($href) === null) {
                continue;
            }
            $final_url = $this->get_final_redirect_url($href, $meta_vals);
            $entry = $this->classify_pan_url($final_url);
            if (!empty($entry)) {
                $entries[$entry['url']] = $entry;
                $this->write_log("{$log_tag}：a 标签 → {$entry['url']}");
            }
        }

        // 旧版页面：链接直接写在正文里（含 UC 网盘）
        if (preg_match_all('#https?://(?:pan\.quark\.cn/s/|pan\.xunlei\.com/s/|pan\.baidu\.com/s/|(?:drive|fast)\.uc\.cn/s/)[A-Za-z0-9_\-]+(?:\?[^\s"\'<>]*)?#i', $raw_html, $text_matches)) {
            foreach ($text_matches[0] as $text_url) {
                $entry = $this->classify_pan_url($text_url);
                if (!empty($entry)) {
                    $entries[$entry['url']] = $entry;
                }
            }
        }

        $this->xk_link_found = count($entries);

        foreach ($entries as $entry) {
            if (isset($this->xk_emitted[$entry['url']])) {
                continue;
            }
            $this->xk_emitted[$entry['url']] = true;

            $url = $entry['url'];
            $code_text = '';
            if ($entry['code'] !== '' && strpos($url, $entry['code']) === false) {
                $code_text = "（提取码：{$entry['code']}）";
            }
            if ($entry['pan'] === '夸克网盘') {
                if ($enable_quark) {
                    $lines[] = $this->extract_quark_real_link($url, $meta_vals);
                } else {
                    $lines[] = "夸克网盘分享链接：{$url}{$code_text}";
                }
            } elseif ($entry['pan'] === '迅雷云盘') {
                $lines[] = "迅雷云盘链接：{$url}{$code_text}";
            } elseif ($entry['pan'] === '百度网盘') {
                $lines[] = "百度网盘下载：{$url}{$code_text}";
            } else {
                $lines[] = "{$entry['pan']}下载：{$url}{$code_text}";
            }
        }

        return $lines;
    }

    /**
     * 获取最终跳转后的URL（支持多层重定向）
     * @param string $url 初始URL
     * @param array $meta_vals 采集配置
     * @return string 最终URL
     */
    private function get_final_redirect_url($url, $meta_vals) {
        $max_redirects = 5;
        $current_url = $url;
        
        for ($i = 0; $i < $max_redirects; $i++) {
            $this->write_log("重定向跟踪第" . ($i + 1) . "次: " . $current_url);
            
            // 如果已经是网盘分享页，直接返回
            if (!empty($this->classify_pan_url($current_url))) {
                return $current_url;
            }
            
            // 处理 golink 参数（base64解码）
            if (preg_match('/[?&]golink=([^&]+)/', $current_url, $matches)) {
                $decoded = base64_decode($matches[1]);
                if ($decoded && filter_var($decoded, FILTER_VALIDATE_URL)) {
                    $this->write_log("golink解码: " . $decoded);
                    $current_url = $decoded;
                    continue;
                }
            }
            
            // 发送请求获取跳转后的URL
            $args = $this->return_html_args($meta_vals);
            $args['timeout'] = 15;
            $args['method'] = 'HEAD';
            $args['redirection'] = 0; // 不自动跟随，手动获取Location头
            
            $response = wp_remote_request($current_url, $args);
            
            if (is_wp_error($response)) {
                // HEAD请求失败，尝试GET请求
                $args['method'] = 'GET';
                $response = wp_remote_request($current_url, $args);
            }
            
            if (is_wp_error($response)) {
                $this->write_log("请求失败: " . $response->get_error_message(), true);
                return $current_url;
            }
            
            $status_code = wp_remote_retrieve_response_code($response);
            $location = wp_remote_retrieve_header($response, 'location');
            
            // 3xx状态码，有Location头，继续跟踪
            if ($status_code >= 300 && $status_code < 400 && !empty($location)) {
                // 处理相对路径
                if (strpos($location, 'http') !== 0) {
                    $parsed = parse_url($current_url);
                    $base = $parsed['scheme'] . '://' . $parsed['host'];
                    $location = $base . $location;
                }
                $this->write_log("发现重定向: " . $location);
                $current_url = $location;
                continue;
            }
            
            // 不是重定向，返回当前URL
            break;
        }
        
        // 有些短链不返回 302，而是一个带 JS / meta 跳转的 HTML 页面
        if (empty($this->classify_pan_url($current_url))) {
            $scan_args = $this->return_html_args($meta_vals);
            $scan_args['timeout'] = 15;
            $scan_resp = wp_remote_get($current_url, $scan_args);
            if (!is_wp_error($scan_resp)) {
                $scan_body = wp_remote_retrieve_body($scan_resp);
                if (!empty($scan_body) && preg_match('#https?://(?:pan\.quark\.cn/s/|pan\.xunlei\.com/s/|pan\.baidu\.com/s/|(?:drive|fast)\.uc\.cn/s/)[A-Za-z0-9_\-]+(?:\?[^\s"\'<>]*)?#i', $scan_body, $scan_match)) {
                    $this->write_log("从返回页面中解析到网盘地址: " . $scan_match[0]);
                    return $scan_match[0];
                }
            }
        }
        
        // 最后再检查一次是否是夸克链接
        if (preg_match('/pan\.quark\.cn\/s\/[a-zA-Z0-9]+/', $current_url, $match)) {
            return $match[0];
        }
        
        return $current_url;
    }

	public function __construct() {
		$this->settings = function () {return;};
		$this->remove_pc = function () {return;};
	}
}