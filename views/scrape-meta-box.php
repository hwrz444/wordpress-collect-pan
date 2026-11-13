<?php if (!defined('ABSPATH')) exit; ?>
<div class="alert">
	<h3><?php _e('Error!', 'ol-scrapes'); ?></h3>
	<p><i class="icon ion-android-alert"></i><?php _e('This page requires JavaScript to run properly.', 'ol-scrapes'); ?></p>
</div>

<div class="bootstrap" name="form" ng-app="octolooks" ng-controller="options" ng-form novalidate ng-cloak ng-init="
model.post_title = '<?php echo $post_object->post_title != "" ? esc_js($post_object->post_title) : "Scrapes-" . time(); ?>';
model.scrape_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_type', true)); ?>';
model.scrape_url = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_url', true)); ?>';
model.scrape_url_single = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_url_single', true)); ?>';
model.scrape_listitem = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_listitem', true)); ?>';
model.scrape_exact_match = <?php
	if (get_post_meta($post_object->ID, 'scrape_exact_match', true) == "") {
	    echo 'false';
	} else {
	    echo 'true';
	}
	?>;
model.scrape_nextpage_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_nextpage_type', true)); ?>';
model.scrape_nextpage = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_nextpage', true)); ?>';
model.scrape_nextpage_innerhtml = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_nextpage_innerhtml', true)); ?>';
model.scrape_title_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_title_type', true)); ?>';
model.scrape_title = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_title', true)); ?>';
model.scrape_title_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_title_template', true)); ?>';
model.scrape_title_template_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_title_template_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_title_regex_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_title_regex_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_title_dont_update = <?php
	if (get_post_meta($post_object->ID, 'scrape_title_dont_update', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;	
model.scrape_content_regex_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_content_regex_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
	
model.scrape_content2 = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content2', true)); ?>';
model.scrape_content2_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content2_template', true)); ?>';
model.scrape_content2_template_status = <?php echo get_post_meta($post_object->ID, 'scrape_content2_template_status', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content2_quark_extract = <?php echo get_post_meta($post_object->ID, 'scrape_content2_quark_extract', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content2_delete_buttons = <?php echo get_post_meta($post_object->ID, 'scrape_content2_delete_buttons', true) == "" ? 'true' : 'true'; ?>;
model.scrape_content2_merge_position = <?php echo get_post_meta($post_object->ID, 'scrape_content2_merge_position', true) == "" ? 'false' : 'true'; ?>;
	
	model.scrape_content3 = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content3', true)); ?>';
model.scrape_content3_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content3_template', true)); ?>';
model.scrape_content3_template_status = <?php echo get_post_meta($post_object->ID, 'scrape_content3_template_status', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content3_merge_position = <?php echo get_post_meta($post_object->ID, 'scrape_content3_merge_position', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content3_quark_extract = <?php echo get_post_meta($post_object->ID, 'scrape_content3_quark_extract', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content3_delete_buttons = <?php echo get_post_meta($post_object->ID, 'scrape_content3_delete_buttons', true) == "" ? 'true' : 'true'; ?>;

model.scrape_content4 = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content4', true)); ?>';
model.scrape_content4_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content4_template', true)); ?>';
model.scrape_content4_template_status = <?php echo get_post_meta($post_object->ID, 'scrape_content4_template_status', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content4_quark_extract = <?php echo get_post_meta($post_object->ID, 'scrape_content4_quark_extract', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content4_delete_buttons = <?php echo get_post_meta($post_object->ID, 'scrape_content4_delete_buttons', true) == "" ? 'true' : 'true'; ?>;
model.scrape_content4_merge_position = <?php echo get_post_meta($post_object->ID, 'scrape_content4_merge_position', true) == "" ? 'false' : 'true'; ?>;

model.scrape_content5 = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content5', true)); ?>';
model.scrape_content5_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content5_template', true)); ?>';
model.scrape_content5_template_status = <?php echo get_post_meta($post_object->ID, 'scrape_content5_template_status', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content5_quark_extract = <?php echo get_post_meta($post_object->ID, 'scrape_content5_quark_extract', true) == "" ? 'false' : 'true'; ?>;
model.scrape_content5_delete_buttons = <?php echo get_post_meta($post_object->ID, 'scrape_content5_delete_buttons', true) == "" ? 'true' : 'true'; ?>;
model.scrape_content5_merge_position = <?php echo get_post_meta($post_object->ID, 'scrape_content5_merge_position', true) == "" ? 'false' : 'true'; ?>;
	
model.scrape_delete_all_links = <?php 
	if (get_post_meta($post_object->ID, 'scrape_delete_all_links', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
?>;	
model.scrape_excerpt = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_excerpt', true)); ?>';
model.scrape_excerpt_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_excerpt_type', true)); ?>';

model.scrape_excerpt_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_excerpt_template', true)); ?>';
model.scrape_excerpt_template_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_excerpt_template_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_excerpt_regex_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_excerpt_regex_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_excerpt_allowhtml = <?php
	if (get_post_meta($post_object->ID, 'scrape_excerpt_allowhtml', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
	
<?php $scrape_custom_fields = get_post_meta($post_object->ID, 'scrape_custom_fields', true); ?>
<?php if(!empty($scrape_custom_fields)): foreach($scrape_custom_fields as $timestamp => $arr) :?>
model.scrape_custom_fields[<?php echo $timestamp; ?>].template_status = <?php echo $arr['template_status'] == "" ? "false" : "true";?>;
model.scrape_custom_fields[<?php echo $timestamp; ?>].regex_status = <?php echo $arr['regex_status'] == "" ? "false" : "true"; ?>;
model.scrape_custom_fields[<?php echo $timestamp; ?>].allowhtml = <?php echo $arr['allowhtml'] == "" ? "false" : "true"; ?>;
model.scrape_custom_fields[<?php echo $timestamp; ?>].dont_update = <?php echo $arr['dont_update'] == "" ? "false" : "true"; ?>;
model['scrape_custom_fields[<?php echo $timestamp; ?>][value]'] = '<?php echo esc_js($arr['value']); ?>';
<?php endforeach; endif; ?>
model.scrape_content = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content', true)); ?>';
model.scrape_content_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_content_type', true)); ?>';
model.scrape_reader_mode = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_reader_mode', true)); ?>';
model.scrape_dont_update = <?php
	if (get_post_meta($post_object->ID, 'scrape_dont_update', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_change_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_change_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_allowhtml = <?php
	if (is_null(get_post_custom_keys($post_object->ID)) || !in_array('scrape_allowhtml', get_post_custom_keys($post_object->ID))) {
		echo 'true';
	} else {
		if (get_post_meta($post_object->ID, 'scrape_allowhtml', true) == "") {
			echo 'false';
		} else {
			echo 'true';
		}
	}
	?>;
model.scrape_download_images = <?php
	if (get_post_meta($post_object->ID, 'scrape_download_images', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_data_src_images = <?php 
	if (get_post_meta($post_object->ID, 'scrape_data_src_images', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.top_rank = <?php
	if (class_exists('Top_Rank_Seo')) {
		echo 'true';
	} else {
		echo 'false';
	}
	?>;
model.wm_valid = <?php
	if (class_exists('Image_Watermark')) {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;	
model.scrape_watermark_images = <?php
	if (get_post_meta($post_object->ID, 'scrape_watermark_images', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;	
model.scrape_featured = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_featured', true)); ?>';
model.scrape_featured_template = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_featured_template', true)); ?>';
model.scrape_featured_template_status = <?php
if (get_post_meta($post_object->ID, 'scrape_featured_template_status', true) == "") {
	echo 'false';
} else {
	echo 'true';
}
?>;
model.scrape_featured_regex_status = <?php
if (get_post_meta($post_object->ID, 'scrape_featured_regex_status', true) == "") {
	echo 'false';
} else {
	echo 'true';
}
?>;
model.scrape_external_image = <?php
if (get_post_meta($post_object->ID, 'scrape_external_image', true) == "") {
	echo 'false';
} else {
	echo 'true';
}
?>;
model.scrape_featured_dont_update = <?php
if (get_post_meta($post_object->ID, 'scrape_featured_dont_update', true) == "") {
	echo 'false';
} else {
	echo 'true';
}
?>;
model.scrape_featured_gallery = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_featured_gallery', true)); ?>';
model.scrape_featured_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_featured_type', true)); ?>';
model.scrape_post_type = '<?php
	$post_types = array_merge(array('post'), get_post_types(array('_builtin' => false)));
	if (($key = array_search('scrape', $post_types)) !== false) { unset($post_types[$key]); }
	if (get_post_meta($post_object->ID, 'scrape_post_type', true) == "") {
		echo "post";
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_post_type', true));
	}
	?>';
<?php $scrape_categoryxpath_tax = get_post_meta($post_object->ID, 'scrape_categoryxpath_tax', true); ?>
<?php if(!empty($scrape_categoryxpath_tax)): foreach($scrape_categoryxpath_tax as $timetaxonomy => $tax) : ?>
model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].tax_name = '<?php echo esc_js($tax['name']); ?>';
model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].separator = '<?php echo esc_js($tax['separator']); ?>';	
model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].regex_status = <?php echo $tax['regex_status'] == "" ? "false" : "true"; ?>;
model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].template_status = <?php echo $tax['template_status'] == "" ? "false" : "true";?>;
model['scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]'] = '<?php echo esc_js($tax['value']); ?>';
<?php endforeach; endif; ?>	

model.taxonomy_exists = <?php
        $scrape_category = get_post_meta($post_object->ID, 'scrape_category', true);
        $scrape_post_type = esc_js(get_post_meta($post_object->ID, 'scrape_post_type', true));
        if (empty($scrape_post_type)) {
	        $scrape_post_type = 'post';
        }
        $object_taxonomies = get_object_taxonomies($scrape_post_type);
        if ($scrape_post_type == 'post') {
            $cats = get_categories(array('hide_empty' => 0, 'taxonomy' => array_diff($object_taxonomies, array('post_tag'))));
        } else if (!empty($object_taxonomies)) {
            $cats = get_categories(array('hide_empty' => 0, 'taxonomy' => $object_taxonomies, 'type' => $scrape_post_type));
        } else {
            $cats = array();
        }
        if (!empty($object_taxonomies)) {
            echo 'true';
        } else {
            echo 'false';
        }
        ?>;
model.category_exists = <?php if (!empty($cats)) {
	    echo 'true';
	} else {
	    echo 'false';
	}
	?>;
model.scrape_product_type = '<?php 
		$scrape_product_type = get_post_meta($post_object->ID, 'scrape_product_type', true);
		$object_product = get_object_taxonomies('product');
		$product_cats = get_categories(array('hide_empty' => 0, 'taxonomy' => $object_product, 'type' => 'product'));
		foreach($product_cats as $cat_type) {
			if($cat_type->cat_ID == 2 || $cat_type->cat_ID == 5 || $cat_type->cat_ID == 4 || $cat_type->cat_ID == 3) {
				$product_type[$cat_type->cat_ID] = $cat_type->name;
			}
		}
		if(!empty($scrape_category)) {
			foreach($scrape_category as $available_type) {
				if($available_type == 2 || $available_type == 5 || $available_type == 4 || $available_type == 3) {
					$av_type[$available_type] = $available_type;
					$model_type = $av_type[$available_type];
				}
			}
		}
		if(!empty($scrape_product_type)) {
			echo esc_js(get_post_meta($post_object->ID, 'scrape_product_type', true));
		} elseif (!empty($model_type)) {
			echo $model_type;
		} elseif(get_post_meta($post_object->ID, 'scrape_product_type', true) == "") {
			echo "2";
		}
?>';
model.scrape_product_variable = <?php 
	if (get_post_meta($post_object->ID, 'scrape_product_variable', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_tags = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_tags', true)); ?>';
model.scrape_tags_custom = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_tags_custom', true)); ?>';
model.scrape_tags_separator = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_tags_separator', true)); ?>';
model.scrape_tags_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_tags_type', true)); ?>';

model.scrape_tags_regex_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_tags_regex_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;


model.scrape_spin_enable = <?php
if (get_post_meta($post_object->ID, 'scrape_spin_enable', true) == "") {
    echo 'false';
} else {
    echo 'true';
}
?>;
model.scrape_spin_email = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_spin_email', true)); ?>';
model.scrape_spin_password = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_spin_password', true)); ?>';


model.scrape_translate_enable = <?php
if (get_post_meta($post_object->ID, 'scrape_translate_enable', true) == "") {
    echo 'false';
} else {
    echo 'true';
}
?>;
model.scrape_translate_source = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_translate_source', true)); ?>';
model.scrape_translate_target = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_translate_target', true)); ?>';


model.scrape_author = '<?php
	$wp_super_admins = new WP_User_Query(array('orderby' => 'display_name', 'role' => array('Super Admin')));
    $wp_admins = new WP_User_Query(array('orderby' => 'display_name', 'role' => array('Administrator')));
	$wp_editors = new WP_User_Query(array('orderby' => 'display_name', 'role' => array('Editor')));
	$wp_authors = new WP_User_Query(array('orderby' => 'display_name', 'role' => array('Author')));

	$authors = array_merge(
        $wp_super_admins->get_results(),
        $wp_admins->get_results(),
        $wp_editors->get_results(),
        $wp_authors->get_results()
    );
	if (get_post_meta($post_object->ID, 'scrape_author', true) == "") {
		echo esc_js($authors[0]->ID);
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_author', true));
	}
	?>';
model.scrape_comment = <?php
	if (is_null(get_post_custom_keys($post_object->ID)) || !in_array('scrape_comment', get_post_custom_keys($post_object->ID))) {
		echo 'true';
	} else {
		if (get_post_meta($post_object->ID, 'scrape_comment', true) == "") {
			echo 'false';
		} else {
			echo 'true';
		}
	}
	?>;
model.scrape_date = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_date', true)); ?>';
model.scrape_date_regex_status = <?php
if (get_post_meta($post_object->ID, 'scrape_date_regex_status', true) == "") {
    echo 'false';
} else {
    echo 'true';
}
?>;
model.scrape_date_custom = '<?php
	if (get_post_meta($post_object->ID, 'scrape_date_custom', true) == "") {
		echo '1970-01-01 00:00:00';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_date_custom', true));
	}
	?>';
model.scrape_date_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_date_type', true)); ?>';
model.scrape_status = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_status', true)); ?>';
model.scrape_post_password = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_post_password', true)); ?>';
model.scrape_template = '<?php esc_js(get_post_meta($post_object->ID, 'scrape_template', true)); ?>';
model.scrape_template_status = <?php
	if (get_post_meta($post_object->ID, 'scrape_template_status', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_cron_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_cron_type', true)); ?>';
model.scrape_post_limit = '<?php
	if (get_post_meta($post_object->ID, 'scrape_post_limit', true) == "") {
		echo '100';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_post_limit', true));
	}
	?>';
model.scrape_post_unlimited = <?php
	if (is_null(get_post_custom_keys($post_object->ID)) || !in_array('scrape_post_unlimited', get_post_custom_keys($post_object->ID))) {
		echo 'true';
	} else {
		if (get_post_meta($post_object->ID, 'scrape_post_unlimited', true) == "") {
			echo 'false';
		} else {
			echo 'true';
		}
	}
	?>;
model.scrape_run_limit = '<?php
	if (get_post_meta($post_object->ID, 'scrape_run_limit', true) == "") {
		echo '1';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_run_limit', true));
	}
	?>';
model.scrape_run_unlimited = <?php
	if (is_null(get_post_custom_keys($post_object->ID)) || !in_array('scrape_run_unlimited', get_post_custom_keys($post_object->ID))) {
		echo 'true';
	} else {
		if (get_post_meta($post_object->ID, 'scrape_run_unlimited', true) == "") {
			echo 'false';
		} else {
			echo 'true';
		}
	}
	?>;
model.scrape_first_run_times = [
	{ id: 'hour_0', name: '<?php _e('Immediately', 'ol-scrapes'); ?>' },
	{ id: 'hour_1', name: '<?php _e('1 hour later', 'ol-scrapes'); ?>' },
	{ id: 'hour_2', name: '<?php _e('2 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_3', name: '<?php _e('3 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_4', name: '<?php _e('4 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_5', name: '<?php _e('5 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_6', name: '<?php _e('6 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_7', name: '<?php _e('7 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_8', name: '<?php _e('8 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_9', name: '<?php _e('9 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_10', name: '<?php _e('10 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_11', name: '<?php _e('11 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_12', name: '<?php _e('12 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_13', name: '<?php _e('13 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_14', name: '<?php _e('14 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_15', name: '<?php _e('15 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_16', name: '<?php _e('16 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_17', name: '<?php _e('17 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_18', name: '<?php _e('18 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_19', name: '<?php _e('19 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_20', name: '<?php _e('20 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_21', name: '<?php _e('21 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_22', name: '<?php _e('22 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_23', name: '<?php _e('23 hours later', 'ol-scrapes'); ?>' },
	{ id: 'hour_24', name: '<?php _e('24 hours later', 'ol-scrapes'); ?>' }
];
model.scrape_first_run_time = '<?php if (get_post_meta($post_object->ID, 'scrape_first_run_time', true) == "") { echo 'hour_0'; } else { echo esc_js(get_post_meta($post_object->ID, 'scrape_first_run_time', true)); } ?>';
model.scrape_recurrence = '<?php
	$scrape_schedules = wp_get_schedules();
	if (get_post_meta($post_object->ID, 'scrape_recurrence', true) == "") {
		echo 'scrape_1 Day';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_recurrence', true));
	}
	?>';
model.scrape_recurrences = <?php
    $scrape_schedules = wp_get_schedules();
    $result = array();
    foreach ($scrape_schedules as $key => $value) { if (stripos($key, 'scrape_') !== false) { $result[] = array('id' => $key, 'name' => $value['display']); } }
    echo esc_js(json_encode($result));
    ?>;
model.scrape_stillworking = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_stillworking', true)) ?>';
model.scrape_run_type = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_run_type', true)) ?>';
model.scrape_continue_type = <?php
	if (get_post_meta($post_object->ID, 'scrape_continue_type', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_sync_run = <?php
	if (get_post_meta($post_object->ID, 'scrape_sync_run', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;	
model.scrape_unique_title = <?php
	if (is_null(get_post_custom_keys($post_object->ID)) || !in_array('scrape_unique_title', get_post_custom_keys($post_object->ID))) {
		echo 'true';
	} else {
		if (get_post_meta($post_object->ID, 'scrape_unique_title', true) == "") {
			echo 'false';
		} else {
			echo 'true';
		}
	}
	?>;
model.scrape_unique_content = <?php
	if (get_post_meta($post_object->ID, 'scrape_unique_content', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_unique_url = <?php
	if (get_post_meta($post_object->ID, 'scrape_unique_url', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_on_unique = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_on_unique', true)); ?>';
model.scrape_update_price = <?php
	if (get_post_meta($post_object->ID, 'scrape_update_price', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_update_stock = <?php
	if (get_post_meta($post_object->ID, 'scrape_update_stock', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;	
model.scrape_existing_fields = <?php
	if (get_post_meta($post_object->ID, 'scrape_existing_fields', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_finish_repeat = '<?php
	if (get_post_meta($post_object->ID, 'scrape_finish_repeat', true) == "") {
		echo '10';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_finish_repeat', true));
	}
	?>';
model.scrape_finish_repeat_enabled = <?php
	if (get_post_meta($post_object->ID, 'scrape_finish_repeat_enabled', true) == "") {
		echo 'false';
	} else {
		echo 'true';
	}
	?>;
model.scrape_waitpage = '<?php
	if (get_post_meta($post_object->ID, 'scrape_waitpage', true) == "") {
		echo '3';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_waitpage', true));
	}
	?>';
model.scrape_timeout = '<?php
	if (get_post_meta($post_object->ID, 'scrape_timeout', true) == "") {
		echo '60';
	} else {
		echo esc_js(get_post_meta($post_object->ID, 'scrape_timeout', true));
	}
	?>';
model.scrape_onerror = '<?php echo esc_js(get_post_meta($post_object->ID, 'scrape_onerror', true)); ?>';
model.all_custom_fields = <?php echo esc_attr(wp_json_encode($auto_complete)); ?>;
init();
">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<div class="header">
					<div class="fixed">
						<ul class="nav">
							<li class="scr-step scr-step-0"><button type="button" class="btn step icon ion-android-menu"></button></li>
							<li class="scr-step scr-step-1"><button type="button" class="btn step icon ion-link" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-2"><button type="button" class="btn step icon ion-folder" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-3"><button type="button" class="btn step icon ion-android-list" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-4"><button type="button" class="btn step icon ion-loop" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-5"><button type="button" class="btn step icon ion-android-send" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-6"><button type="button" class="btn step icon ion-funnel" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-7"><button type="button" class="btn step icon ion-android-calendar" ng-disabled="!model.scrape_type"></button></li>
							<li class="scr-step scr-step-8"><button type="button" class="btn step icon ion-gear-a" ng-disabled="!model.scrape_type"></button></li>
						</ul>
					</div>
				</div>
				
				<div class="form-horizontal">
					<div class="panel-group">
						<div id="panel_jumb" class="panel jumbotron">
							<div class="cover">
								<div class="c-header">
									<?php if (empty($_GET['post'])) { ?>
										<?php $user = wp_get_current_user(); ?>
										<span class="scr-header-img"><img src="<?php echo esc_url(get_avatar_url($user->ID)); ?>" /></span>								
										<div class="title">										
											<h1><?php echo $user->display_name ; ?></h1>
											<p><?php _e('Give a name and select task type to start.', 'ol-scrapes'); ?></p>
										</div>	
									<?php } else { ?>
										<?php $userid = esc_js(get_post_meta($post_object->ID, 'scrape_author', true)); $user = get_userdata( $userid );?>
										<span class="scr-header-img"><img src="<?php echo esc_url(get_avatar_url($userid)); ?>" /></span>								
										<div class="title">											
											<h1><?php echo $user->display_name; ?></h1>
											<p><?php _e('Modify options and save changes.', 'ol-scrapes'); ?></p>										
										</div>
									<?php } ?>
									<div class="scrapes-logo">
									<img src="<?php echo plugin_dir_url(__FILE__ ) . '../assets/img/scrapes-logo.svg'; ?>" />
									</div>
								</div>
							</div>

							<div class="form-group" ng-class="{'has-error' : form.post_title.$invalid && (form.post_title.$dirty || submitted)}">
								<div class="col-lg-3 col-sm-4">
									<label class="control-label"><?php _e('Name', 'ol-scrapes'); ?> 
										<span class="scr-tooltip">
											<div class="box top">
											<h5 class="title"><?php _e('Name', 'ol-scrapes'); ?></h5>
											<div class="content"><p>
											<?php _e('The field used to give a name and define task (Required).', 'ol-scrapes'); ?>							
											</p></div>
											</div>
											<i title="" class="dashicons-before dashicons-editor-help"></i>
										</span>
									</label>
								</div>
								<div class="col-lg-9 col-sm-8">
									<div class="form-group">
										<div class="col-sm-12">
											<input type="text" name="post_title" placeholder="<?php _e('e.g. Scrapes', 'ol-scrapes'); ?>" class="form-control" ng-model="model.post_title" ng-required="true">
											<p class="help-block" ng-show="form.post_title.$invalid && (form.post_title.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group" ng-class="{'has-error' : form.scrape_type.$invalid && (form.scrape_type.$dirty || submitted)}">
								<div class="col-lg-3 col-sm-4">
									<label class="control-label"><?php _e('Task type', 'ol-scrapes'); ?> 
										<span class="scr-tooltip">
											<div class="box top">
											<h5 class="title"><?php _e('Task type', 'ol-scrapes'); ?></h5>
											<div class="content"><p>
											<?php _e('The field used to set running type of the task (Required).', 'ol-scrapes'); ?>							
											</p></div>
											</div>
											<i title="" class="dashicons-before dashicons-editor-help"></i>
										</span>									
									</label>
								</div>
								<div class="col-lg-9 col-sm-8">
									<div class="form-group">
										<div class="col-sm-12">
											<div class="row">
												<div class="col-xs-4">
													<img src="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/img/type-single.svg" class="img-responsive">

													<label>
														<input type="radio" name="scrape_type" value="single" ng-model="model.scrape_type" ng-required="true">
														<span><?php _e('Single', 'ol-scrapes'); ?></span>
													</label>
												</div>

												<div class="col-xs-4">
													<img src="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/img/type-serial.svg" class="img-responsive">

													<label>
														<input type="radio" name="scrape_type" value="list" ng-model="model.scrape_type" ng-required="true">
														<span><?php _e('Serial', 'ol-scrapes'); ?></span>
													</label>
												</div>

												<div class="col-xs-4">
													<img src="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/img/type-feed.svg" class="img-responsive">

													<label>
														<input type="radio" name="scrape_type" value="feed" ng-model="model.scrape_type" ng-required="true">
														<span><?php _e('Feed', 'ol-scrapes'); ?></span>
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					
						<div id="panel_00" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-0" data-toggle="collapse"><i class="icon ion-link"></i><?php _e('Request', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-0" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Cookies', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Cookies', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set which cookie values to be sent to source url requests.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<?php
											$post_cookie_names = get_post_meta($post_object->ID, 'scrape_cookie_names', true);
											$post_cookie_values = get_post_meta($post_object->ID, 'scrape_cookie_values', true);
											$post_cookie_name_values = array();
											if (!empty($post_cookie_names) && !empty($post_cookie_values)) {
												$post_cookie_name_values = array_combine($post_cookie_names, $post_cookie_values);
											}
											if (!empty($post_cookie_name_values)) : foreach ($post_cookie_name_values as $key => $value) : if(!empty($key)) :
											?>
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Name', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_cookie_names[]" placeholder="<?php _e('e.g. name', 'ol-scrapes'); ?>" value="<?php echo esc_js($key); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_cookie_values[]" placeholder="<?php _e('e.g. value', 'ol-scrapes'); ?>" value="<?php echo esc_js($value); ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'cookie')"><i class="icon ion-plus-circled"></i> <?php _e('Add new cookie', 'ol-scrapes'); ?></button>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_url.$invalid && (form.scrape_url.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Source URL', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Source URL', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set which source you want to scrape (Required).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('URL', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_url" placeholder="<?php _e('e.g. http://octolooks.com', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_url" ng-required="true" ng-pattern="/^(http|https):///">
														<input type="text" name="scrape_url_single" class="hidden" ng-model="model.scrape_url_single">
													</div>
													<p class="help-block" ng-show="form.scrape_url.$invalid && (form.scrape_url.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_listitem.$invalid && (form.scrape_listitem.$dirty || submitted)}" ng-if="model.scrape_type && model.scrape_type == 'list'">
										<label class="col-sm-4 control-label"><?php _e('Post item', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Post item', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set which links redirect to detail pages (Required).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_listitem" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_listitem" ng-required="true" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_serial($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_listitem.$invalid && (form.scrape_listitem.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group"  ng-if="model.scrape_type && model.scrape_type == 'list'">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_exact_match" ng-model="model.scrape_exact_match"> <?php _e('Exact match only', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_nextpage.$invalid && (form.scrape_nextpage.$dirty || submitted)}" ng-if="model.scrape_type && model.scrape_type == 'list'">
										<label class="col-sm-4 control-label"><?php _e('Next page', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Next page', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set which link redirects to next list page.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-show="model.scrape_nextpage_type == 'source'">
												<div class="col-sm-12">
													<p class="help-block success" ng-show="next_page_found && model.scrape_nextpage == next_page_found"><i class="icon ion-checkmark-circled"></i> <?php _e('Next page is found automatically.', 'ol-scrapes'); ?></p>
													<p class="help-block success" ng-show="special_url == 'mfacebook_groups' && model.scrape_nextpage == mfacebook_groups_nextpage_xpath"><i class="icon ion-checkmark-circled"></i> <?php _e('Next page is found automatically.', 'ol-scrapes'); ?></p>
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_nextpage" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_nextpage" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_serial($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
                                                    <input type="text" name="scrape_nextpage_innerhtml" class="hidden" ng-model="model.scrape_nextpage_innerhtml" />
													<p class="help-block" ng-show="form.scrape_nextpage.$invalid && (form.scrape_nextpage.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

                                            <?php
                                            $scrape_next_page_url_parameters_names = get_post_meta($post_object->ID, 'scrape_next_page_url_parameters_names', true);
                                            $scrape_next_page_url_parameters_values = get_post_meta($post_object->ID, 'scrape_next_page_url_parameters_values', true);
                                            $scrape_next_page_url_parameters_increments = get_post_meta($post_object->ID, 'scrape_next_page_url_parameters_increments', true);

                                            if(!empty($scrape_next_page_url_parameters_names)) : for($i = 0; $i < count($scrape_next_page_url_parameters_names); $i++) : if(!empty($scrape_next_page_url_parameters_names[$i])) : ?>
											<div class="form-group" ng-if="model.scrape_nextpage_type == 'parameter'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Name', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_next_page_url_parameters_names[]" placeholder="<?php _e('e.g. name', 'ol-scrapes'); ?>" value="<?php echo esc_html($scrape_next_page_url_parameters_names[$i]); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_next_page_url_parameters_values[]" placeholder="<?php _e('e.g. 1', 'ol-scrapes'); ?>" value="<?php echo esc_html($scrape_next_page_url_parameters_values[$i]); ?>" class="form-control">
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Increment', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_next_page_url_parameters_increments[]" placeholder="<?php _e('e.g. 1', 'ol-scrapes'); ?>" value="<?php echo esc_html($scrape_next_page_url_parameters_increments[$i]); ?>" class="form-control">
													</div>
												</div>
											</div>
                                            <?php endif; endfor; endif; ?>


											<div class="form-group" ng-show="model.scrape_nextpage_type == 'parameter'">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'nextpage_parameter')"><i class="icon ion-plus-circled"></i> <?php _e('Add new parameter', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_nextpage_type" value="source" ng-model="model.scrape_nextpage_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_nextpage_type" value="parameter" ng-model="model.scrape_nextpage_type"> <?php _e('Enter URL parameter', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="panel_01" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-1" data-toggle="collapse"><i class="icon ion-folder"></i><?php _e('Taxonomy', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-1" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : form.scrape_post_type.$invalid && (form.scrape_post_type.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Post type', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Post type', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the post type for posts which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="select">
														<select name="scrape_post_type" class="form-control" ng-model="model.scrape_post_type" ng-change="update_categories(model.scrape_post_type)">
															<?php foreach ($post_types as $post_type) { ?>
															<option value="<?php echo $post_type; ?>"><?php echo get_post_type_object( $post_type )->labels->singular_name; ?></option>
															<?php } ?>
														</select>
													</div>
													<p class="help-block" ng-show="form.scrape_post_type.$invalid && (form.scrape_post_type.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>
									
									<div class="form-group" ng-if="model.scrape_post_type == 'product'" ng-show="model.scrape_type && model.category_exists">
										<label class="col-sm-4 control-label"><?php _e('Product type', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Product type', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the product type', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="select">
														<select name="scrape_product_type" class="form-control" ng-model="model.scrape_product_type">
															<?php foreach($product_type as $key_type => $value_type) : ?>															
																<?php if (!empty($scrape_product_type)) : ?>
																<option value="<?php echo $key_type; ?>" <?php if($key_type == $scrape_product_type) {echo "selected"; } ?>><?php _e($value_type, 'ol-scrapes') ?></option>
																<?php elseif(!empty($av_type[$key_type])) : ?>
																<option value="<?php echo $key_type; ?>" <?php if($key_type == $av_type[$key_type]) {echo "selected"; } ?>><?php _e($value_type, 'ol-scrapes') ?></option>																
																<?php else : ?>
																<option value="<?php echo $key_type; ?>" <?php if($key_type == 2) {echo "selected"; } ?>><?php _e($value_type, 'ol-scrapes') ?></option>
																<?php endif; ?>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
											</div>	
											<div class="form-group" ng-show="model.scrape_product_type == '4'">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_product_variable" ng-model="model.scrape_product_variable"> <?php _e('Aoutomatic Change the variable to simple (If the product did not have a variable attribute)', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>									

									<div class="form-group" ng-class="{'has-error' : form.scrape_categoryxpath_tax.$invalid && (form.scrape_categoryxpath_tax.$dirty || submitted)}" ng-show="model.scrape_type && model.taxonomy_exists">
										<label class="col-sm-4 control-label"><?php _e('Create categories', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Create categories', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the categories of automatically created posts to which newly created categories by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>							
										</label>
										<div class="col-sm-8">
											<?php if(!empty($scrape_categoryxpath_tax)) : foreach($scrape_categoryxpath_tax as $timetaxonomy => $tx) : if(!empty($tx["name"])) :?>
											<div class="form-group" ng-class="{'has-error' : form['scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]'].$invalid && (form['scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]'].$dirty || submitted)}">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Taxonomy', 'ol-scrapes'); ?></div>
														<div class="select">							
															<select name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][name]" id="select-taxonomy-type" class="form-control" ng-model="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].tax_name">
																<option value=""><?php _e('Please select a taxonomy', 'ol-scrapes'); ?></option>
																<?php
																if (get_post_meta($post_object->ID, 'scrape_post_type', true) == "") {
																	$taxonomies = get_object_taxonomies('post', 'objects');
																} else {
																	$taxonomies = get_object_taxonomies(get_post_meta($post_object->ID, 'scrape_post_type', true), 'objects');
																}
																foreach ($taxonomies as $taxonomy) { ?>
																<option value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->labels->name; ?></option>
																<?php } ?>
															</select>
														</div>
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>	
												</div>											
												<div class="form-group" ng-if="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].tax_name">
													<div class="col-sm-12">
														<div class="input-group">
															<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
															<input type="text" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" value="<?php echo esc_js($tx['value']); ?>" class="form-control" ng-model="model['scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]']" ng-pattern="/^///">
															<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
														</div>
														<p class="help-block" ng-show="form['scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]'].$invalid && (form['scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][value]'].$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
													</div>
													<div class="col-sm-12">
														<div class="input-group">
															<div class="input-group-addon"><?php _e('Separator', 'ol-scrapes'); ?></div>
															<input type="text" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][separator]" placeholder="<?php _e('e.g. ,', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].separator">
														</div>
													</div>
													<div class="col-sm-12" ng-show="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].template_status">
														<div class="input-group">
															<div class="input-group-addon"><?php _e('Template', 'ol-scrapes'); ?></div>
															<input type="text" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][template]" placeholder="<?php _e('e.g [scrape_value]', 'ol-scrapes'); ?>" value="<?php echo esc_js($tx['template']); ?>" class="form-control">
														</div>
														<div class="input-tags">
															<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'><?php _e('value', 'ol-scrapes'); ?></button>
														</div>
													</div>													
												</div>												

												<?php
												$scrape_category_regex_finds = isset($tx['regex_finds']) ? $tx['regex_finds'] : array();
												$scrape_category_regex_replaces = isset($tx['regex_replaces']) ? $tx['regex_replaces'] : array();
												$combined_regex = array();
												if(!empty($scrape_category_regex_finds))
													$combined_regex = array_combine($scrape_category_regex_finds, $scrape_category_regex_replaces);
												if(!empty($scrape_category_regex_finds)) : foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>
												<div class="form-group" ng-show="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].tax_name && model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].regex_status">
													<div class="col-sm-12">
														<div class="input-group">
															<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
															<input type="text" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][regex_finds][]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex); ?>" class="form-control">
															<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
														</div>
													</div>
													<div class="col-sm-12">
														<div class="input-group">
															<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
															<input type="text" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][regex_replaces][]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
														</div>
													</div>
												</div>
												<?php endif; endforeach; endif; ?>

												<div class="form-group" ng-show="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].tax_name && model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].regex_status">
													<div class="col-sm-12">
														<button type="button" class="btn btn-link" ng-click="add_field($event, 'category_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
													</div>
												</div>

												<div class="form-group" ng-show="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].tax_name">
													<div class="col-sm-12">
														<div class="checkbox"><label><input type="checkbox" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][template_status]" ng-model="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].template_status"> <?php _e('Enable template', 'ol-scrapes'); ?></label></div>
														<div class="checkbox"><label><input type="checkbox" name="scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>][regex_status]" ng-model="model.scrape_categoryxpath_tax[<?php echo $timetaxonomy; ?>].regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>
											<div class="form-group">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'taxonomy_field')"><i class="icon ion-plus-circled"></i> <?php _e('Add new taxonomy', 'ol-scrapes'); ?></button>
												</div>
											</div>											
										</div>
									</div>

									<div class="form-group" ng-show="model.scrape_type && model.category_exists">
										<label class="col-sm-4 control-label"><?php _e('Categories', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Categories', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the categories of posts which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="overflow">
														<?php foreach ($cats as $c) { ?>
															<div class="checkbox"><label><input type="checkbox" name="scrape_category[]" value="<?php echo $c->cat_ID; ?>"<?php if (!empty($scrape_category) && in_array($c->cat_ID, $scrape_category)) { echo ' checked'; } ?>> <?php echo $c->name; ?> <small>(<?php echo get_taxonomy($c->taxonomy)->labels->name; ?>)</small></label></div>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="panel_02" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-2" data-toggle="collapse"><i class="icon ion-android-list"></i><?php _e('Post', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-2" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : form.scrape_title.$invalid && (form.scrape_title.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Title', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Title', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set post titles which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="(model.scrape_type == 'single' || model.scrape_type == 'list' || (model.scrape_type == 'feed' && model.scrape_title_type == 'xpath')) || model.scrape_title_template_status">
												<div class="col-sm-12" ng-if="model.scrape_type == 'single' || model.scrape_type == 'list' || (model.scrape_type == 'feed' && model.scrape_title_type == 'xpath')">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_title" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_title" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_title.$invalid && (form.scrape_title.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>

												<div class="col-sm-12" ng-if="model.scrape_title_template_status">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Template', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_title_template" placeholder="<?php _e('e.g. [scrape_value]', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_title_template">
													</div>
													<div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'><?php _e('value', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]'><?php _e('date', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]'><?php _e('custom field', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'><?php _e('source url', 'ol-scrapes'); ?></button>
													</div>
												</div>
											</div>

											<?php
											$scrape_title_regex_finds = get_post_meta($post_object->ID, 'scrape_title_regex_finds', true);
											$scrape_title_regex_replaces = get_post_meta($post_object->ID, 'scrape_title_regex_replaces', true);
											$combined_regex = array();
											if(!empty($scrape_title_regex_finds))
												$combined_regex = array_combine($scrape_title_regex_finds, $scrape_title_regex_replaces);
											if(!empty($scrape_title_regex_finds)) : foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>
											<div class="form-group" ng-show="model.scrape_title_regex_status">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_title_regex_finds[]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_title_regex_replaces[]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group" ng-show="model.scrape_title_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'title_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_title_template_status" ng-model="model.scrape_title_template_status"> <?php _e('Enable template', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_title_regex_status" ng-model="model.scrape_title_regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_title_dont_update" ng-model="model.scrape_title_dont_update" ng-unchecked="model.scrape_unique_url == false" ng-disabled="model.scrape_unique_url == false"> <?php _e('Do Not update(To use this option, the unique post must be set to url only)', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group" ng-if="model.scrape_type && model.scrape_type == 'feed'">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_title_type" value="feed" ng-model="model.scrape_title_type"> <?php _e('Detect from feed', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_title_type" value="xpath" ng-model="model.scrape_title_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_content.$invalid && (form.scrape_content.$dirty || submitted)}" ng-show="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Content', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Content', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set post content which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_type && model.scrape_content_type == 'xpath'">
												<div class="col-sm-12">
													<p class="help-block success" ng-show="special_url == 'mfacebook_groups' && model.scrape_content == mfacebook_groups_content_xpath"><i class="icon ion-checkmark-circled"></i> <?php _e('Content is found automatically.', 'ol-scrapes'); ?></p>
													<p class="help-block success" ng-show="special_url == 'mfacebook_page' && model.scrape_content == mfacebook_page_content_xpath"><i class="icon ion-checkmark-circled"></i> <?php _e('Content is found automatically.', 'ol-scrapes'); ?></p>
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_content" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_content" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_content.$invalid && (form.scrape_content.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group" ng-show="model.scrape_type && model.scrape_template_status">
												<div class="col-sm-12">
													<?php wp_editor(get_post_meta($post_object->ID, 'scrape_template', true), 'scrapetemplate', array('textarea_name' => 'scrape_template', 'editor_height' => 200)); ?>
													<div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_content]'><?php _e('content', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_title]'><?php _e('title', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_categories]'><?php _e('categories', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_tags]'><?php _e('tags', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]'><?php _e('date', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_thumbnail]'><?php _e('featured image', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_gallery]'><?php _e('gallery', 'ol-scrapes'); ?></button>
														<?php if (class_exists('TS_Aparat')) : ?>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_aparat]'><?php _e('aparat', 'ol-scrapes'); ?></button>
														<?php endif; ?>
														<button type="button" class="btn btn-primary btn-xs" data-value='[embed][scrape_url][/embed]'><?php _e('embed', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]'><?php _e('custom field', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'><?php _e('source url', 'ol-scrapes'); ?></button>
													</div>
												</div>
											</div>

											<?php
											$scrape_content_regex_finds = get_post_meta($post_object->ID, 'scrape_content_regex_finds', true);
											$scrape_content_regex_replaces = get_post_meta($post_object->ID, 'scrape_content_regex_replaces', true);
											$combined_regex = array();
											if(!empty($scrape_content_regex_finds)) {
												$combined_regex = array_combine($scrape_content_regex_finds, $scrape_content_regex_replaces);
											}

											if(!empty($scrape_content_regex_finds)): foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>
											<div class="form-group" ng-show="model.scrape_type && model.scrape_content_regex_status">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_content_regex_finds[]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex) ;?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_content_regex_replaces[]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group" ng-show="model.scrape_type && model.scrape_content_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'content_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_template_status" ng-model="model.scrape_template_status"> <?php _e('Enable template', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_content_regex_status" ng-model="model.scrape_content_regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_delete_all_links" ng-model="model.scrape_delete_all_links"> <?php _e('Delete all links in the content', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio" ng-if="model.scrape_type && model.scrape_type == 'feed'"><label><input type="radio" name="scrape_content_type" value="feed" ng-model="model.scrape_content_type"> <?php _e('Detect from feed', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_content_type" value="auto" ng-model="model.scrape_content_type"> <?php _e('Detect automatically', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_content_type" value="xpath" ng-model="model.scrape_content_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_dont_update" ng-model="model.scrape_dont_update"> <?php _e('Do Not update', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_allowhtml" ng-model="model.scrape_allowhtml"> <?php _e('Allow HTML tags', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_download_images" ng-model="model.scrape_download_images"> <?php _e('Download images to media library', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_data_src_images" ng-model="model.scrape_data_src_images"> <?php _e('Enable this option if the source site images have data-src or data-lazy-src feature', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_watermark_images" ng-model="model.scrape_watermark_images" ng-unchecked="model.wm_valid" ng-disabled="model.wm_valid"> <?php _e('Add watermark to images ' , 'ol-scrapes'); ?><?php if(!class_exists('Image_Watermark')) echo _e('(Requires Image Watermark plugin)' , 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>
<!-- ==================== 内容2 字段（额外内容拼接） ==================== -->
<div class="form-group" ng-show="model.scrape_type">
    <label class="col-sm-4 control-label">
        内容2 (额外内容，将与主内容拼接)
        <span class="scr-tooltip">
            <div class="box top">
                <h5 class="title">内容2</h5>
                <div class="content"><p>采集第二个内容区域，将与主内容拼接发布。适用于下载链接、资源区域等。</p></div>
            </div>
            <i title="" class="dashicons-before dashicons-editor-help"></i>
        </span>
    </label>
    <div class="col-sm-8">
        <div class="form-group">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">XPath 值</div>
                    <input type="text" name="scrape_content2" placeholder="例如: //div[@class='download-links'] 或 //div[contains(text(),'夸克')]/.." class="form-control" ng-model="model.scrape_content2">
                    <span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
                </div>
                <p class="help-block">采集下载区域、夸克网盘链接等额外内容的XPath</p>
            </div>
        </div>

        <div class="form-group" ng-if="model.scrape_content2_template_status">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">模板</div>
                    <input type="text" name="scrape_content2_template" placeholder="例如: <div class='download-section'>[scrape_value]</div>" class="form-control" ng-model="model.scrape_content2_template">
                </div>
                <div class="input-tags">
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'>value</button>
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'>source url</button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <div class="checkbox"><label><input type="checkbox" name="scrape_content2_quark_extract" ng-model="model.scrape_content2_quark_extract"> 🔗 夸克网盘深度提取（自动进入夸克页面提取真实下载链接，并以明文展示）</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content2_delete_buttons" ng-model="model.scrape_content2_delete_buttons" checked> 🗑️ 自动删除所有按钮元素（只保留纯文本链接）</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content2_template_status" ng-model="model.scrape_content2_template_status"> 启用模板</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content2_merge_position" ng-model="model.scrape_content2_merge_position"> 内容2放在主内容前面（默认在后面）</label></div>
            </div>
        </div>
    </div>
</div>
<!-- ==================== 内容2字段结束 ==================== -->

<!-- ==================== 内容3 字段 ==================== -->
<div class="form-group" ng-show="model.scrape_type">
    <label class="col-sm-4 control-label">
        内容3 (第三个内容区域)
        <span class="scr-tooltip">
            <div class="box top">
                <h5 class="title">内容3</h5>
                <div class="content"><p>采集第三个内容区域，将与主内容和内容2一起拼接发布。</p></div>
            </div>
            <i title="" class="dashicons-before dashicons-editor-help"></i>
        </span>
    </label>
    <div class="col-sm-8">
        <div class="form-group">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">XPath 值</div>
                    <input type="text" name="scrape_content3" placeholder="例如: //div[@class='download-links2']" class="form-control" ng-model="model.scrape_content3">
                    <span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
                </div>
                <p class="help-block">采集第三个下载区域或额外内容的XPath</p>
            </div>
        </div>

        <div class="form-group" ng-if="model.scrape_content3_template_status">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">模板</div>
                    <input type="text" name="scrape_content3_template" placeholder="例如: <div class='download-section3'>[scrape_value]</div>" class="form-control" ng-model="model.scrape_content3_template">
                </div>
                <div class="input-tags">
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'>value</button>
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'>source url</button>
                </div>
            </div>
        </div>

        <div class="form-group">
    <div class="col-sm-12">
        <div class="checkbox"><label><input type="checkbox" name="scrape_content3_quark_extract" ng-model="model.scrape_content3_quark_extract"> 🔗 夸克网盘深度提取（自动进入夸克页面提取真实下载链接，并以明文展示）</label></div>
        <div class="checkbox"><label><input type="checkbox" name="scrape_content3_delete_buttons" ng-model="model.scrape_content3_delete_buttons" checked> 🗑️ 自动删除所有按钮元素（只保留纯文本链接）</label></div>
        <div class="checkbox"><label><input type="checkbox" name="scrape_content3_template_status" ng-model="model.scrape_content3_template_status"> 启用模板</label></div>
        <div class="checkbox"><label><input type="checkbox" name="scrape_content3_merge_position" ng-model="model.scrape_content3_merge_position"> 内容3放在所有内容最前面（默认在最后面）</label></div>
    </div>
</div>
    </div>
</div>
<!-- ==================== 内容3字段结束 ==================== -->

<!-- ==================== 内容4 字段 ==================== -->
<div class="form-group" ng-show="model.scrape_type">
    <label class="col-sm-4 control-label">
        内容4 (第四个内容区域)
        <span class="scr-tooltip">
            <div class="box top">
                <h5 class="title">内容4</h5>
                <div class="content"><p>采集第四个内容区域，将与主内容一起拼接发布。</p></div>
            </div>
            <i title="" class="dashicons-before dashicons-editor-help"></i>
        </span>
    </label>
    <div class="col-sm-8">
        <div class="form-group">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">XPath 值</div>
                    <input type="text" name="scrape_content4" placeholder="例如: //div[@class='download-area4']" class="form-control" ng-model="model.scrape_content4">
                    <span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
                </div>
                <p class="help-block">采集第四个下载区域或额外内容的XPath</p>
            </div>
        </div>

        <div class="form-group" ng-if="model.scrape_content4_template_status">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">模板</div>
                    <input type="text" name="scrape_content4_template" placeholder="例如: <div class='download-section4'>[scrape_value]</div>" class="form-control" ng-model="model.scrape_content4_template">
                </div>
                <div class="input-tags">
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'>value</button>
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'>source url</button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <div class="checkbox"><label><input type="checkbox" name="scrape_content4_quark_extract" ng-model="model.scrape_content4_quark_extract"> 🔗 夸克网盘深度提取</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content4_delete_buttons" ng-model="model.scrape_content4_delete_buttons" checked> 🗑️ 自动删除所有按钮元素</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content4_template_status" ng-model="model.scrape_content4_template_status"> 启用模板</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content4_merge_position" ng-model="model.scrape_content4_merge_position"> 内容4放在所有内容最前面</label></div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== 内容5 字段 ==================== -->
<div class="form-group" ng-show="model.scrape_type">
    <label class="col-sm-4 control-label">
        内容5 (第五个内容区域)
        <span class="scr-tooltip">
            <div class="box top">
                <h5 class="title">内容5</h5>
                <div class="content"><p>采集第五个内容区域，将与主内容一起拼接发布。</p></div>
            </div>
            <i title="" class="dashicons-before dashicons-editor-help"></i>
        </span>
    </label>
    <div class="col-sm-8">
        <div class="form-group">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">XPath 值</div>
                    <input type="text" name="scrape_content5" placeholder="例如: //div[@class='download-area5']" class="form-control" ng-model="model.scrape_content5">
                    <span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
                </div>
                <p class="help-block">采集第五个下载区域或额外内容的XPath</p>
            </div>
        </div>

        <div class="form-group" ng-if="model.scrape_content5_template_status">
            <div class="col-sm-12">
                <div class="input-group">
                    <div class="input-group-addon">模板</div>
                    <input type="text" name="scrape_content5_template" placeholder="例如: <div class='download-section5'>[scrape_value]</div>" class="form-control" ng-model="model.scrape_content5_template">
                </div>
                <div class="input-tags">
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'>value</button>
                    <button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'>source url</button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <div class="checkbox"><label><input type="checkbox" name="scrape_content5_quark_extract" ng-model="model.scrape_content5_quark_extract"> 🔗 夸克网盘深度提取</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content5_delete_buttons" ng-model="model.scrape_content5_delete_buttons" checked> 🗑️ 自动删除所有按钮元素</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content5_template_status" ng-model="model.scrape_content5_template_status"> 启用模板</label></div>
                <div class="checkbox"><label><input type="checkbox" name="scrape_content5_merge_position" ng-model="model.scrape_content5_merge_position"> 内容5放在所有内容最前面</label></div>
            </div>
        </div>
    </div>
</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_excerpt.$invalid && (form.scrape_excerpt.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Excerpt', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Excerpt', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set post excerpt which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_excerpt_type == 'xpath'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_excerpt" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_excerpt" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_excerpt.$invalid && (form.scrape_excerpt.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>

												<div class="col-sm-12" ng-if="model.scrape_excerpt_template_status">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Template', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_excerpt_template" placeholder="<?php _e('e.g. [scrape_value]', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_excerpt_template">
													</div>
													<div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'><?php _e('value', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]'><?php _e('date', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]'><?php _e('custom field', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'><?php _e('source url', 'ol-scrapes'); ?></button>
													</div>
												</div>
											</div>
											<?php
											$scrape_excerpt_regex_finds = get_post_meta($post_object->ID, 'scrape_excerpt_regex_finds', true);
											$scrape_excerpt_regex_replaces = get_post_meta($post_object->ID, 'scrape_excerpt_regex_replaces', true);
											$combined_regex = array();
											if(!empty($scrape_excerpt_regex_finds)) {
												$combined_regex = array_combine($scrape_excerpt_regex_finds, $scrape_excerpt_regex_replaces);
											}

											if(!empty($scrape_excerpt_regex_finds)): foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>
											<div class="form-group" ng-show="model.scrape_excerpt_type == 'xpath' && model.scrape_excerpt_regex_status">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_excerpt_regex_finds[]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_excerpt_regex_replaces[]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group" ng-show="model.scrape_excerpt_type == 'xpath' && model.scrape_excerpt_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'excerpt_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group" ng-show="model.scrape_excerpt_type == 'xpath'">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_excerpt_template_status" ng-model="model.scrape_excerpt_template_status"> <?php _e('Enable template', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_excerpt_regex_status" ng-model="model.scrape_excerpt_regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_excerpt_allowhtml" ng-model="model.scrape_excerpt_allowhtml"> <?php _e('Allow HTML tags', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_excerpt_type" value="auto" ng-model="model.scrape_excerpt_type"> <?php _e('Generate from content', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_excerpt_type" value="xpath" ng-model="model.scrape_excerpt_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : (form.scrape_tags_custom.$invalid && (form.scrape_tags_custom.$dirty || submitted)) || ((form.scrape_tags.$invalid && (form.scrape_tags.$dirty || submitted)) || (form.scrape_tags_separator.$invalid && (form.scrape_tags_separator.$dirty || submitted)))}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Tags', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Tags', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the tags of posts which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_tags_type == 'xpath'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_tags" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_tags" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_tags.$invalid && (form.scrape_tags.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Separator', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_tags_separator" placeholder="<?php _e('e.g. ,', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_tags_separator">
													</div>
													<p class="help-block" ng-show="form.scrape_tags_separator.$invalid && (form.scrape_tags_separator.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group" ng-if="model.scrape_tags_type == 'custom'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_tags_custom" placeholder="<?php _e('e.g. octolooks, scrapes', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_tags_custom">
													</div>
													<p class="help-block" ng-show="form.scrape_tags_custom.$invalid && (form.scrape_tags_custom.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<?php
											$scrape_tags_regex_finds = get_post_meta($post_object->ID, 'scrape_tags_regex_finds', true);
											$scrape_tags_regex_replaces = get_post_meta($post_object->ID, 'scrape_tags_regex_replaces', true);
											$combined_regex = array();
											if(!empty($scrape_tags_regex_finds)) {
												$combined_regex = array_combine($scrape_tags_regex_finds, $scrape_tags_regex_replaces);
											}
											if(!empty($scrape_tags_regex_finds)): foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>
											<div class="form-group" ng-show="model.scrape_tags_type == 'xpath' && model.scrape_tags_regex_status">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_tags_regex_finds[]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_tags_regex_replaces[]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group" ng-show="model.scrape_tags_type == 'xpath' && model.scrape_tags_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'tags_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group" ng-show="model.scrape_tags_type == 'xpath'">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_tags_regex_status" ng-model="model.scrape_tags_regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_tags_type" value="xpath" ng-model="model.scrape_tags_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_tags_type" value="custom" ng-model="model.scrape_tags_type"> <?php _e('Enter custom', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : (form.scrape_featured_gallery.$invalid && (form.scrape_featured_gallery.$dirty || submitted)) || (form.scrape_featured.$invalid && (form.scrape_featured.$dirty || submitted))}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Featured image', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Featured image', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set featured image for posts which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-show="model.scrape_featured_type == 'xpath'">
												<div class="col-sm-12">
													<p class="help-block success" ng-show="featured_image_found && model.scrape_featured == featured_image_found"><i class="icon ion-checkmark-circled"></i> <?php _e('Featured image is found automatically.', 'ol-scrapes'); ?></p>
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_featured" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_featured" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_featured.$invalid && (form.scrape_featured.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>

												<div class="col-sm-12" ng-if="model.scrape_featured_template_status">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Template', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_featured_template" placeholder="<?php _e('e.g. [scrape_value]', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_featured_template">
													</div>
													<div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'><?php _e('value', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]'><?php _e('date', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]'><?php _e('custom field', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'><?php _e('source url', 'ol-scrapes'); ?></button>
													</div>
												</div>
											</div>

                                            <?php
                                            $scrape_featured_regex_finds = get_post_meta($post_object->ID, 'scrape_featured_regex_finds', true);
                                            $scrape_featured_regex_replaces = get_post_meta($post_object->ID, 'scrape_featured_regex_replaces', true);
                                            $combined_regex = array();
                                            if(!empty($scrape_featured_regex_finds))
                                                $combined_regex = array_combine($scrape_featured_regex_finds, $scrape_featured_regex_replaces);
                                            if(!empty($scrape_featured_regex_finds)) : foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>

                                            <div class="form-group" ng-show="model.scrape_featured_regex_status && model.scrape_featured_type == 'xpath'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_featured_regex_finds[]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo $regex; ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_featured_regex_replaces[]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo $replace; ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group" ng-show="model.scrape_featured_regex_status && model.scrape_featured_type == 'xpath'">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'featured_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group" ng-show="model.scrape_featured_type == 'xpath'">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_featured_template_status" ng-model="model.scrape_featured_template_status"> <?php _e('Enable template', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_featured_regex_status" ng-model="model.scrape_featured_regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_external_image" ng-model="model.scrape_external_image"> <?php _e('Dont upload images and gallery to media library', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_featured_dont_update" ng-model="model.scrape_featured_dont_update"> <?php _e('Do Not update(your posts must have an featured image)', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group" ng-if="model.scrape_featured_type == 'gallery'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_featured_gallery" placeholder="<?php _e('e.g. 100', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_featured_gallery" ng-pattern="/^[0-9]*$/">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_media_library($event)"><i class="icon ion-image"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_featured_gallery.$invalid && (form.scrape_featured_gallery.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio" ng-if="model.scrape_type && model.scrape_type == 'feed'"><label><input type="radio" name="scrape_featured_type" value="feed" ng-model="model.scrape_featured_type"> <?php _e('Detect from feed', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_featured_type" value="xpath" ng-model="model.scrape_featured_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_featured_type" value="gallery" ng-model="model.scrape_featured_type"> <?php _e('Select from media library', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-show="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Custom fields', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Custom fields', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the custom meta fields of posts which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<?php if(!empty($scrape_custom_fields)) : foreach($scrape_custom_fields as $timestamp => $cf) : if(!empty($cf["name"])) :?>
											<div class="form-group" ng-class="{'has-error' : form['scrape_custom_fields[<?php echo $timestamp; ?>][value]'].$invalid && (form['scrape_custom_fields[<?php echo $timestamp; ?>][value]'].$dirty || submitted)}">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Name', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_custom_fields[<?php echo $timestamp; ?>][name]" placeholder="<?php _e('e.g. name', 'ol-scrapes'); ?>" value="<?php echo esc_js($cf['name']); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_custom_fields[<?php echo $timestamp; ?>][value]" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" value="<?php echo esc_js($cf['value']); ?>" class="form-control" ng-model="model['scrape_custom_fields[<?php echo $timestamp; ?>][value]']" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form['scrape_custom_fields[<?php echo $timestamp; ?>][value]'].$invalid && (form['scrape_custom_fields[<?php echo $timestamp; ?>][value]'].$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Attribute', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_custom_fields[<?php echo $timestamp; ?>][attribute]" placeholder="<?php _e('e.g. href', 'ol-scrapes'); ?>" value="<?php echo esc_js($cf['attribute']); ?>" class="form-control">
													</div>
												</div>
												<div class="col-sm-12" ng-show="model.scrape_custom_fields[<?php echo $timestamp; ?>].template_status">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Template', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_custom_fields[<?php echo $timestamp; ?>][template]" placeholder="<?php _e('e.g [scrape_value]', 'ol-scrapes'); ?>" value="<?php echo esc_js($cf['template']); ?>" class="form-control">
													</div>
													<div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'><?php _e('value', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='calc([scrape_value] + 0)'><?php _e('calculate', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]'><?php _e('date', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]'><?php _e('custom field', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'><?php _e('source url', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[affiliate]'><?php _e('affiliate', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='{{amazon_product_url()}}' ng-if="special_url == 'amazon'"><i class="fa fa-amazon"></i> <?php _e('product url', 'ol-scrapes'); ?></button>
														<button type="button" class="btn btn-primary btn-xs" data-value='{{amazon_cart_url()}}' ng-if="special_url == 'amazon'"><i class="fa fa-amazon"></i> <?php _e('cart url', 'ol-scrapes'); ?></button>
													</div>
												</div>

												<div class="separator">
													<div class="col-sm-12">
														<?php
														$custom_fields_regex_finds = isset($cf['regex_finds']) ? $cf['regex_finds'] : array();
														$custom_fields_regex_replaces = isset($cf['regex_replaces']) ? $cf['regex_replaces'] : array();
														$regex_combined = array();
														if(!empty($custom_fields_regex_finds)) {
															$regex_combined = array_combine($cf['regex_finds'], $cf['regex_replaces']);
														}
														foreach($regex_combined as $regex => $replace) : if(!empty($regex)):
														?>
														<div class="form-group" ng-show="model.scrape_custom_fields[<?php echo $timestamp; ?>].regex_status">
															<div class="col-sm-12">
																<div class="input-group">
																	<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
																	<input type="text" name="scrape_custom_fields[<?php echo $timestamp; ?>][regex_finds][]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex); ?>" class="form-control">
																	<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
																</div>
															</div>

															<div class="col-sm-12">
																<div class="input-group">
																	<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
																	<input type="text" name="scrape_custom_fields[<?php echo $timestamp; ?>][regex_replaces][]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
																</div>
															</div>
														</div>
														<?php endif; endforeach; ?>

														<div class="form-group" ng-show="model.scrape_custom_fields[<?php echo $timestamp; ?>].regex_status">
															<div class="col-sm-12">
																<button type="button" class="btn btn-link" ng-click="add_field($event, 'custom_field_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
															</div>
														</div>

														<div class="form-group">
															<div class="col-sm-12">
																<div class="checkbox"><label><input type="checkbox" name="scrape_custom_fields[<?php echo $timestamp; ?>][template_status]" ng-model="model.scrape_custom_fields[<?php echo $timestamp; ?>].template_status"> <?php _e('Enable template', 'ol-scrapes'); ?></label></div>
																<div class="checkbox"><label><input type="checkbox" name="scrape_custom_fields[<?php echo $timestamp; ?>][regex_status]" ng-model="model.scrape_custom_fields[<?php echo $timestamp; ?>].regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
																<div class="checkbox"><label><input type="checkbox" name="scrape_custom_fields[<?php echo $timestamp; ?>][allowhtml]" ng-model="model.scrape_custom_fields[<?php echo $timestamp; ?>].allowhtml"> <?php _e('Allow HTML tags', 'ol-scrapes'); ?></label></div>
																<div class="checkbox"><label><input type="checkbox" name="scrape_custom_fields[<?php echo $timestamp; ?>][dont_update]" ng-model="model.scrape_custom_fields[<?php echo $timestamp; ?>].dont_update"> <?php _e('Do Not update', 'ol-scrapes'); ?></label></div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>

											<div class="form-group">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'custom_field')"><i class="icon ion-plus-circled"></i> <?php _e('Add new custom field', 'ol-scrapes'); ?></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="panel_03" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-3" data-toggle="collapse"><i class="icon ion-loop"></i><?php _e('Customize', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-3" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : (form.scrape_translate_source.$invalid && (form.scrape_translate_source.$dirty || submitted)) || (form.scrape_translate_target.$invalid && (form.scrape_translate_target.$dirty || submitted))}">
										<label class="col-sm-4 control-label"><?php _e('Translate fields', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Translate fields', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the translation language for all fields of automatically created posts by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_translate_enable">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Source', 'ol-scrapes'); ?></div>
														<div class="select">
															<select name="scrape_translate_source" class="form-control" ng-model="model.scrape_translate_source" ng-required="true">
																<option value=""><?php _e('Please select a language', 'ol-scrapes'); ?></option>
	                                                            <?php foreach($google_languages as $lang => $code): ?>
	                                                            <option value="<?php echo $code?>"><?php echo $lang; ?></option>
	                                                            <?php endforeach; ?>
															</select>
														</div>
													</div>
													<p class="help-block" ng-show="form.scrape_translate_source.$invalid && (form.scrape_translate_source.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Target', 'ol-scrapes'); ?></div>
														<div class="select">
															<select name="scrape_translate_target" class="form-control" ng-model="model.scrape_translate_target" ng-required="true">
																<option value=""><?php _e('Please select a language', 'ol-scrapes'); ?></option>
	                                                            <?php foreach($google_languages as $lang => $code): ?>
	                                                            <option value="<?php echo $code?>"><?php echo $lang; ?></option>
	                                                            <?php endforeach; ?>
															</select>
														</div>
													</div>
													<p class="help-block" ng-show="form.scrape_translate_target.$invalid && (form.scrape_translate_target.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
                                                    <?php if(DEMO) : ?><p class="help-block success"> Translate feature is not available on demo</p><?php endif; ?>
												    <?php if (class_exists('TS_Translate')) {
													$option = (get_option('ts_page_settings_is_active',true));
													if($option['ts_active_translate'] != 'off'){ ?>													
													<div class="checkbox"><label><input type="checkbox" name="scrape_translate_enable" ng-model="model.scrape_translate_enable" <?php if(DEMO) echo " disabled"; ?>> <?php _e('Enable', 'ol-scrapes'); ?></label></div>
												    <?php } else { ?>
													<p class="scrapes-notice"><?php _e( 'Please enable the translation feature in the Technical Scapes settings.', 'ol-scrapes' ); ?></p>
													<?php } 
													} else { ?>
												    <p class="scrapes-error"><?php _e( 'You must install and enable the Technical Scrapes plugin to enable translation.', 'ol-scrapes' ); ?><a href="https://www.zhaket.com/web/technical-scrapes-addon" target="_blank" style="text-decoration: none;"><?php _e(' buy technical scrapes plugin', 'ol-scrapes'); ?></a></p>
												    <?php } ?>												
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : (form.scrape_spin_email.$invalid && (form.scrape_spin_email.$dirty || submitted)) || (form.scrape_spin_password.$invalid && (form.scrape_spin_password.$dirty || submitted))}">
										<label class="col-sm-4 control-label"><?php _e('Spin fields', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Spin fields', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the spinner api parameters for all fields of automatically created posts by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_spin_enable">
												<div class="col-sm-12">
													<div class="alert alert-warning">
														<p><?php _e('Spinner feature requires ', 'ol-scrapes'); ?><strong><?php _e('The Best Spinner', 'ol-scrapes'); ?></strong><?php _e(' account which you can create new from ', 'ol-scrapes'); ?><a href="http://octolooks.jonathanleger.zaxaa.com/s/4152949213724" target="_blank">http://thebestspinner.com</a><?php _e('. You can also use other 3rd party ', 'ol-scrapes'); ?><strong><?php _e('spinner plugins', 'ol-scrapes'); ?></strong><?php _e(' for WordPress.', 'ol-scrapes'); ?></p>
													</div>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('E-mail', 'ol-scrapes'); ?></div>
														<div class="input">
															<input name="scrape_spin_email" class="form-control" ng-model="model.scrape_spin_email" placeholder="<?php _e('e.g. contact@octolooks.com', 'ol-scrapes'); ?>" ng-required="true">
														</div>
													</div>
													<p class="help-block" ng-show="form.scrape_spin_email.$invalid && (form.scrape_spin_email.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Password', 'ol-scrapes'); ?></div>
														<div class="input">
															<input name="scrape_spin_password" class="form-control" ng-model="model.scrape_spin_password" placeholder="<?php _e('e.g. password', 'ol-scrapes'); ?>" ng-required="true">
														</div>
													</div>
													<p class="help-block" ng-show="form.scrape_spin_password.$invalid && (form.scrape_spin_password.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_spin_enable" ng-model="model.scrape_spin_enable"> <?php _e('Enable', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="panel_04" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-4" data-toggle="collapse"><i class="icon ion-android-send"></i><?php _e('Publish', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-4" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : form.scrape_author.$invalid && (form.scrape_author.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Author', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Author', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the publishing author for automatically created posts by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="select">
														<select name="scrape_author" class="form-control" ng-model="model.scrape_author">
															<?php foreach ($authors as $author) { ?>
															<option value="<?php echo $author->ID; ?>"><?php echo $author->data->user_nicename; ?></option>
															<?php } ?>
														</select>
													</div>
													<p class="help-block" ng-show="form.scrape_author.$invalid && (form.scrape_author.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_status.$invalid && (form.scrape_status.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Status', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Status', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set which post status for automatically created posts by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_status" value="publish" ng-model="model.scrape_status"> <?php _e('Published', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_status" value="draft" ng-model="model.scrape_status"> <?php _e('Draft', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_status" value="pending" ng-model="model.scrape_status"> <?php _e('Pending review', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_status" value="private" ng-model="model.scrape_status"> <?php _e('Private', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_status" value="password" ng-model="model.scrape_status"> <?php _e('Password protected', 'ol-scrapes'); ?></label></div>
													<div class="input-group" ng-if="model.scrape_status == 'password'">
														<div class="input-group-addon"><?php _e('Password', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_post_password" placeholder="<?php _e('Enter your password', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_post_password">
													</div>													
													<div class="checkbox"><label><input type="checkbox" name="scrape_change_status" ng-model="model.scrape_change_status"> <?php _e('Automatic change to publish at update time', 'ol-scrapes'); ?></label></div>
													<p class="help-block" ng-show="form.scrape_status.$invalid && (form.scrape_status.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : (form.scrape_date_custom.$invalid && (form.scrape_date_custom.$dirty || submitted)) || (form.scrape_date.$invalid && (form.scrape_date.$dirty || submitted))}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Date', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Date', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the publish date of posts which will be created automatically by the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_date_type == 'xpath'">
												<div class="col-sm-12">
													<p class="help-block success" ng-show="special_url == 'mfacebook_groups' && model.scrape_date == mfacebook_groups_date_xpath"><i class="icon ion-checkmark-circled"></i> <?php _e('Date is found automatically.', 'ol-scrapes'); ?></p>
													<p class="help-block success" ng-show="special_url == 'mfacebook_page' && model.scrape_date == mfacebook_page_date_xpath"><i class="icon ion-checkmark-circled"></i> <?php _e('Date is found automatically.', 'ol-scrapes'); ?></p>
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_date" placeholder="<?php _e('e.g. //div[@id=\'octolooks\']', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_date" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="show_iframe_single($event)"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_date.$invalid && (form.scrape_date.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

                                            <?php
                                            $scrape_date_regex_finds = get_post_meta($post_object->ID, 'scrape_date_regex_finds', true);
                                            $scrape_date_regex_replaces = get_post_meta($post_object->ID, 'scrape_date_regex_replaces', true);
                                            $combined_regex = array();
                                            if(!empty($scrape_date_regex_finds)) {
                                                $combined_regex = array_combine($scrape_date_regex_finds, $scrape_date_regex_replaces);
                                            }
                                            if(!empty($scrape_date_regex_finds)): foreach($combined_regex as $regex => $replace): if(!empty($regex)): ?>
											<div class="form-group" ng-show="model.scrape_date_type == 'xpath' && model.scrape_date_regex_status">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Find', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_date_regex_finds[]" placeholder="<?php _e('e.g. find', 'ol-scrapes'); ?>" value="<?php echo esc_html($regex); ?>" class="form-control">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Replace', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_date_regex_replaces[]" placeholder="<?php _e('e.g. replace', 'ol-scrapes'); ?>" value="<?php echo esc_html($replace); ?>" class="form-control">
													</div>
												</div>
											</div>
											<?php endif; endforeach; endif; ?>
											
											<div class="form-group" ng-show="model.scrape_date_type == 'xpath' && model.scrape_date_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'date_regex')"><i class="icon ion-plus-circled"></i> <?php _e('Add new find and replace rule', 'ol-scrapes'); ?></button>
												</div>
											</div>

											<div class="form-group" ng-show="model.scrape_date_type == 'xpath'">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_date_regex_status" ng-model="model.scrape_date_regex_status"> <?php _e('Enable find and replace rules', 'ol-scrapes'); ?></label></div>
												</div>
											</div>

											<div class="form-group" ng-if="model.scrape_date_type == 'custom'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_date_custom" placeholder="<?php _e('e.g. YYYY-MM-DD HH:mm:ss', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_date_custom" ng-pattern="/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/">
													</div>
													<p class="help-block" ng-show="form.scrape_date_custom.$invalid && (form.scrape_date_custom.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_date_type" value="runtime" ng-model="model.scrape_date_type"> <?php _e('Process time', 'ol-scrapes'); ?></label></div>
													<div class="radio" ng-if="model.scrape_type && model.scrape_type == 'feed'"><label><input type="radio" name="scrape_date_type" value="feed" ng-model="model.scrape_date_type"> <?php _e('Detect from feed', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_date_type" value="xpath" ng-model="model.scrape_date_type"> <?php _e('Select from source', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_date_type" value="custom" ng-model="model.scrape_date_type"> <?php _e('Enter custom', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_comment.$invalid && (form.scrape_comment.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Discussion', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Discussion', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set whether user comments are allowed for automatically created posts by the plugin in your WordPress site or not.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_comment" ng-model="model.scrape_comment"> <?php _e('Allow comments', 'ol-scrapes'); ?></label></div>
													<p class="help-block" ng-show="form.scrape_comment.$invalid && (form.scrape_comment.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="panel_05" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-5" data-toggle="collapse"><i class="icon ion-funnel"></i><?php _e('Filter', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-5" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : form.scrape_unique_title.$invalid && (form.scrape_unique_title.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Unique post check', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Unique post check', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to use whether posts should be unique or not.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_unique_title" ng-model="model.scrape_unique_title"> <?php _e('From title', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_unique_content" ng-model="model.scrape_unique_content"> <?php _e('From content', 'ol-scrapes'); ?></label></div>
													<div class="checkbox"><label><input type="checkbox" name="scrape_unique_url" ng-model="model.scrape_unique_url"> <?php _e('From source url', 'ol-scrapes'); ?></label></div>
													<p class="help-block" ng-show="form.scrape_unique_title.$invalid && (form.scrape_unique_title.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_on_unique.$invalid && (form.scrape_on_unique.$dirty || submitted)}" ng-if="model.scrape_type && (model.scrape_unique_title || model.scrape_unique_content || model.scrape_unique_url)">
										<label class="col-sm-4 control-label"><?php _e('On existing post', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('On existing post', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set the action when a post in source url already exists in the WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio" ng-if="model.scrape_type && (model.scrape_type == 'list' || model.scrape_type == 'feed')"><label><input type="radio" name="scrape_on_unique" value="skip" ng-model="model.scrape_on_unique"> <?php _e('Skip to next process', 'ol-scrapes'); ?></label></div>
													<div class="radio" ng-if="model.scrape_type && model.scrape_type == 'single'"><label><input type="radio" name="scrape_on_unique" value="skip" ng-model="model.scrape_on_unique"> <?php _e('Complete process', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_on_unique" value="update" ng-model="model.scrape_on_unique"> <?php _e('Update post', 'ol-scrapes'); ?></label></div>
													<div class="radio" ng-if="model.scrape_post_type == 'product'"><label><input type="radio" name="scrape_on_unique" value="special" ng-model="model.scrape_on_unique"> <?php _e('Special update without product re-processing', 'ol-scrapes'); ?></label></div>
													<div class="checkbox" ng-if="model.scrape_on_unique == 'special'">
													<label class="field-spacing"><input type="checkbox" name="scrape_update_price" ng-model="model.scrape_update_price"> <?php _e('Price', 'ol-scrapes'); ?></label>
													<label class="field-spacing"><input type="checkbox" name="scrape_update_stock" ng-model="model.scrape_update_stock"> <?php _e('Stock', 'ol-scrapes'); ?></label>
													</div>
													<p class="help-block" ng-show="form.scrape_on_unique.$invalid && (form.scrape_on_unique.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>
									
									<div class="form-group" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('existing custom fields', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('existing custom fields', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('By activating this option, you can avoid deleting the fields that you define manually in the posts. (Such as product attributes).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_existing_fields" ng-model="model.scrape_existing_fields"> <?php _e('Do not delete the fields in the post', 'ol-scrapes'); ?></label></div>													
												</div>
											</div>
										</div>
									</div>								

									<div class="form-group" ng-class="{'has-error' : form.scrape_finish_repeat.$invalid && (form.scrape_finish_repeat.$dirty || submitted)}" ng-if="model.scrape_type && (model.scrape_type == 'list' || model.scrape_type == 'feed') && (model.scrape_unique_title || model.scrape_unique_content || model.scrape_unique_url)">
										<label class="col-sm-4 control-label"><?php _e('Complete run on loop', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Complete run on loop', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set how many on existing post occurrence is needed to stop the task until next run time in order to save (Required)', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_finish_repeat_enabled">
												<div class="col-sm-12">
													<div class="input-group">
														<input type="text" name="scrape_finish_repeat" placeholder="<?php _e('e.g. 100', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_finish_repeat" ng-required="true" ng-pattern="/^[1-9][0-9]*$/">
														<div class="input-group-addon"><?php _e('posts', 'ol-scrapes'); ?></div>
													</div>
													<p class="help-block" ng-show="form.scrape_finish_repeat.$invalid && (form.scrape_finish_repeat.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_finish_repeat_enabled" ng-model="model.scrape_finish_repeat_enabled"> <?php _e('Enable', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-4 control-label"><?php _e('Filters', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Filters', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The option to set filters which are applied to posts before they are created.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
                                            <?php
                                            $scrape_filters_fields = get_post_meta($post_object->ID, 'scrape_filters_fields', true);
                                            $scrape_filters_operators = get_post_meta($post_object->ID, 'scrape_filters_operators', true);
                                            $scrape_filters_values = get_post_meta($post_object->ID, 'scrape_filters_values', true);

                                            if(!empty($scrape_filters_fields)) : for($i = 0; $i < count($scrape_filters_fields); $i++) : if(!empty($scrape_filters_operators[$i])) : ?>
                                            <div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Operator', 'ol-scrapes'); ?></div>
														<div class="select">
															<select name="scrape_filters_operators[]" class="form-control">
																<option value="contains" <?php if($scrape_filters_operators[$i] == "contains") {echo "selected"; } ?>><?php _e('Contains', 'ol-scrapes'); ?></option>
																<option value="does_not_contain" <?php if($scrape_filters_operators[$i] == "does_not_contain") {echo "selected"; } ?>><?php _e('Does not contain', 'ol-scrapes'); ?></option>
																<option value="exists" <?php if($scrape_filters_operators[$i] == "exists") {echo "selected"; } ?>><?php _e('Exists', 'ol-scrapes'); ?></option>
																<option value="not_exists" <?php if($scrape_filters_operators[$i] == "not_exists") {echo "selected"; } ?>><?php _e('Not exists', 'ol-scrapes'); ?></option>
																<option value="equal_to" <?php if($scrape_filters_operators[$i] == "equal_to") {echo "selected"; } ?>><?php _e('Equal to', 'ol-scrapes'); ?></option>
																<option value="not_equal_to" <?php if($scrape_filters_operators[$i] == "not_equal_to") {echo "selected"; } ?>><?php _e('Not equal to', 'ol-scrapes'); ?></option>
																<option value="greater_than" <?php if($scrape_filters_operators[$i] == "greater_than") {echo "selected"; } ?>><?php _e('Greater than', 'ol-scrapes'); ?></option>
																<option value="less_than" <?php if($scrape_filters_operators[$i] == "less_than") {echo "selected"; } ?>><?php _e('Less than', 'ol-scrapes'); ?></option>
															</select>
														</div>
														<span class="input-group-btn"><button type="button" class="btn btn-danger btn-block" ng-click="remove_field($event)"><i class="icon ion-trash-a"></i></button></span>
													</div>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Field', 'ol-scrapes'); ?></div>
														<div class="select">
															<select name="scrape_filters_fields[]" class="form-control">
																<option value="title" <?php if($scrape_filters_fields[$i] == "title") {echo "selected"; } ?>><?php _e('Title', 'ol-scrapes'); ?></option>
																<option value="content" <?php if($scrape_filters_fields[$i] == "content") {echo "selected"; } ?>><?php _e('Content', 'ol-scrapes'); ?></option>
																<option value="excerpt" <?php if($scrape_filters_fields[$i] == "excerpt") {echo "selected"; } ?>><?php _e('Excerpt', 'ol-scrapes'); ?></option>
																<option value="featured_image" <?php if($scrape_filters_fields[$i] == "featured_image") {echo "selected"; } ?>><?php _e('Featured image', 'ol-scrapes'); ?></option>
																<option value="date" <?php if($scrape_filters_fields[$i] == "date") {echo "selected"; } ?>><?php _e('Date', 'ol-scrapes'); ?></option>
																<?php if (strpos($scrape_filters_fields[$i], 'custom_field_') !== false) : ?>
																<option value="<?php echo $scrape_filters_fields[$i]; ?>" selected><?php _e('Custom field', 'ol-scrapes'); ?> - <?php echo $scrape_custom_fields[substr($scrape_filters_fields[$i], 13)]['name'] ?></option>
																<?php endif; ?>
															</select>
														</div>
													</div>
												</div>

												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon"><?php _e('Value', 'ol-scrapes'); ?></div>
														<input type="text" name="scrape_filters_values[]" value="<?php echo $scrape_filters_values[$i]; ?>" placeholder="<?php _e('e.g. value', 'ol-scrapes'); ?>" class="form-control">
													</div>
												</div>
											</div>
                                            <?php endif; endfor; endif; ?>

											<div class="form-group">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_field($event, 'filter')"><i class="icon ion-plus-circled"></i> <?php _e('Add new filter', 'ol-scrapes'); ?></button>
												</div>
											</div>
										</div>
									</div>


								</div>
							</div>
						</div>

						<div id="panel_06" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-6" data-toggle="collapse"><i class="icon ion-android-calendar"></i><?php _e('Schedule', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-6" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : form.scrape_cron_type.$invalid && (form.scrape_cron_type.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Cron type', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Cron type', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set the method of calling the task when it is due time in the plugin in your WordPress site.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_cron_type" value="wordpress" ng-model="model.scrape_cron_type"> <?php _e('WordPress', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_cron_type" value="<?php echo S_WORD; ?>" ng-model="model.scrape_cron_type"> <?php _e(ucfirst(S_WORD), 'ol-scrapes'); ?></label></div>
													<p class="help-block" ng-show="form.scrape_cron_type.$invalid && (form.scrape_cron_type.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_post_limit.$invalid && (form.scrape_post_limit.$dirty || submitted)}" ng-if="model.scrape_type && (model.scrape_type == 'list' || model.scrape_type == 'feed')">
										<label class="col-sm-4 control-label"><?php _e('Total posts', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Total posts', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field used to set how many posts will be created in the task in each run (Required).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_post_unlimited == false">
												<div class="col-sm-12">
													<div class="input-group">
														<input type="text" name="scrape_post_limit" placeholder="<?php _e('e.g. 100', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_post_limit" ng-required="true" ng-pattern="/^[1-9][0-9]*$/">
														<div class="input-group-addon"><?php _e('posts', 'ol-scrapes'); ?></div>
													</div>
													<p class="help-block" ng-show="form.scrape_post_limit.$invalid && (form.scrape_post_limit.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_post_unlimited" ng-model="model.scrape_post_unlimited"> <?php _e('Unlimited', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_run_limit.$invalid && (form.scrape_run_limit.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Total runs', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Total runs', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set how many times the task will run. When the task reaches the total run value it stops running (Required).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group" ng-if="model.scrape_run_unlimited == false">
												<div class="col-sm-12">
													<div class="input-group">
														<input type="text" name="scrape_run_limit" placeholder="<?php _e('e.g. 100', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_run_limit" ng-required="true" ng-pattern="/^[1-9][0-9]*$/">
														<div class="input-group-addon"><?php _e('times', 'ol-scrapes'); ?></div>
													</div>
													<p class="help-block" ng-show="form.scrape_run_limit.$invalid && (form.scrape_run_limit.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_run_unlimited" ng-model="model.scrape_run_unlimited"> <?php _e('Unlimited', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-4 control-label"><?php _e('First run', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('First run', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The option to select first run time of task.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>										
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="slider" data-value="scrape_first_run_time" data-values="scrape_first_run_times"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_recurrance.$invalid && (form.scrape_recurrance.$dirty || submitted)}" ng-show="model.scrape_type && (model.scrape_run_unlimited || model.scrape_run_limit > 1)">
										<label class="col-sm-4 control-label"><?php _e('Run frequency', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Run frequency', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set the time interval of each task run.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="slider" data-value="scrape_recurrence" data-values="scrape_recurrences"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_stillworking.$invalid && (form.scrape_stillworking.$dirty || submitted)}" ng-if="model.scrape_type && (model.scrape_type == 'list' || model.scrape_type == 'feed')">
										<label class="col-sm-4 control-label"><?php _e('On uncompleted run', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('On uncompleted run', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to use what action will be taken when a process is not finished and according to run frequency field another task should start.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_stillworking" value="terminate" ng-model="model.scrape_stillworking"> <?php _e('Terminate previous run', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_stillworking" value="wait" ng-model="model.scrape_stillworking"> <?php _e('Wait until previous run is completed', 'ol-scrapes'); ?></label></div>
													<p class="help-block" ng-show="form.scrape_stillworking.$invalid && (form.scrape_stillworking.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-if="model.scrape_type && model.scrape_type == 'list'">
										<label class="col-sm-4 control-label"><?php _e('Run type', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Run type', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set where the task should start from for every run.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_run_type" value="start" ng-model="model.scrape_run_type"> <?php _e('Start from first list page', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_run_type" value="continue" ng-model="model.scrape_run_type"> <?php _e('Continue from last scraped list page', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="form-group" ng-if="model.scrape_type && model.scrape_type == 'list' && model.scrape_run_type == 'continue'">
										<label class="col-sm-4 control-label"><?php _e('How to continue', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('How to continue', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('By activating this option, you can specify that if the total posts is limited, it Continue from last scraped list page in any case.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_continue_type" ng-model="model.scrape_continue_type"> <?php _e('If the total posts is limited, continue from the last of the page list', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Synchronic run', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Synchronic run', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('By activating this option, you can prevent the from scrapes synchronic running.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label><input type="checkbox" name="scrape_sync_run" ng-model="model.scrape_sync_run"> <?php _e('Prevent the from scrapes synchronic running', 'ol-scrapes'); ?></label></div>
												</div>
											</div>
										</div>
									</div>									
								</div>
							</div>
						</div>

						<div id="panel_07" class="panel">
							<div class="panel-heading">
								<h4><a href="#collapse-7" data-toggle="collapse"><i class="icon ion-gear-a"></i><?php _e('Others', 'ol-scrapes'); ?></a></h4>
							</div>

							<div id="collapse-7" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" ng-class="{'has-error' : form.scrape_timeout.$invalid && (form.scrape_timeout.$dirty || submitted)}" ng-if="model.scrape_type">
										<label class="col-sm-4 control-label"><?php _e('Timeout for process', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Timeout for process', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set maximum time the task will wait for a reply for http requests (Required).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<input type="text" name="scrape_timeout" placeholder="<?php _e('e.g. 100', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_timeout" ng-required="true" ng-pattern="/^[1-9][0-9]*$/">
														<div class="input-group-addon"><?php _e('seconds', 'ol-scrapes'); ?></div>
													</div>
													<p class="help-block" ng-show="form.scrape_timeout.$invalid && (form.scrape_timeout.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_waitpage.$invalid && (form.scrape_waitpage.$dirty || submitted)}" ng-if="model.scrape_type && (model.scrape_type == 'list' || model.scrape_type == 'feed')">
										<label class="col-sm-4 control-label"><?php _e('Wait next processes', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('Wait next processes', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set how much time to wait between processes (Required).', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<input type="text" name="scrape_waitpage" placeholder="<?php _e('e.g. 100', 'ol-scrapes'); ?>" class="form-control" ng-model="model.scrape_waitpage" ng-required="true" ng-pattern="/^[1-9][0-9]*$/">
														<div class="input-group-addon"><?php _e('seconds', 'ol-scrapes'); ?></div>
													</div>
													<p class="help-block" ng-show="form.scrape_waitpage.$invalid && (form.scrape_waitpage.$dirty || submitted)"><?php _e('Please enter a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group" ng-class="{'has-error' : form.scrape_onerror.$invalid && (form.scrape_onerror.$dirty || submitted)}" ng-if="model.scrape_type && (model.scrape_type == 'list' || model.scrape_type == 'feed')">
										<label class="col-sm-4 control-label"><?php _e('On error', 'ol-scrapes'); ?> 
											<span class="scr-tooltip">
												<div class="box top">
												<h5 class="title"><?php _e('On error', 'ol-scrapes'); ?></h5>
												<div class="content"><p>
												<?php _e('The field to set what action will be taken if task encounters an error during scrape process.', 'ol-scrapes'); ?>							
												</p></div>
												</div>
												<i title="" class="dashicons-before dashicons-editor-help"></i>
											</span>
										</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label><input type="radio" name="scrape_onerror" value="next" ng-model="model.scrape_onerror"> <?php _e('Skip to next process', 'ol-scrapes'); ?></label></div>
													<div class="radio"><label><input type="radio" name="scrape_onerror" value="stop" ng-model="model.scrape_onerror"> <?php _e('Complete run', 'ol-scrapes'); ?></label></div>
													<p class="help-block" ng-show="form.scrape_onerror.$invalid && (form.scrape_onerror.$dirty || submitted)"><?php _e('Please select a valid value.', 'ol-scrapes'); ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<button type="button" class="next btn forward" ng-disabled="!model.scrape_type"><?php _e('NEXT', 'ol-scrapes'); ?></button>
					<button type="button" class="prev btn back"><?php _e('PREV', 'ol-scrapes'); ?></button>					
				</div>

				<div class="sidebar">
					<div class="fixed topbtn">
						<button type="button" class="btn btn-primary next" ng-disabled="!model.scrape_type"><i class="icon ion-ios-undo"></i></button>
						<button type="button" class="btn btn-primary prev"><i class="icon ion-forward"></i></button>
						<button type="button" class="btn btn-primary trial"><a href="<?php echo admin_url("edit.php?post_type=scrape&page=scrapes-support"); ?>" target="_blank"><?php _e('Tutorial', 'ol-scrapes'); ?></a></button>
						<button type="button" class="btn btn-primary trial" ng-disabled="!model.top_rank">
							<?php if(class_exists('Top_Rank_Seo')) : ?>
							<a href="<?php echo admin_url("edit.php?post_type=scrape&page=scrapes-tpr&tpr_tab=general"); ?>" target="_blank"><?php _e('SEO', 'ol-scrapes'); ?></a>
							<?php else : ?>
							<span class="scr-tooltip">
								<div class="box top seo">
									<div class="content"><p>
									<?php _e('To use this feature, you must purchase the Top Rank plugin from the Zhaket.', 'ol-scrapes'); ?>							
									</p></div>							
								</div>
								<?php _e('SEO', 'ol-scrapes'); ?>
							</span>
							<?php endif; ?>
						</button>
					</div>
				</div>				
				<div class="sidebar" ng-if="model.scrape_type">
					<div class="fixed">
						<?php if (empty($_GET['post'])) { ?>
						<button type="submit" name="publish" value="Publish" id="publish" class="btn btn-primary" ng-class="{'disabled' : form.$invalid}" ng-click="submit($event)"><i class="icon ion-arrow-right-c"></i><span><?php _e('Save', 'ol-scrapes'); ?></span></button>
						<?php } else { ?>
						<button type="submit" name="save" value="Update" id="publish" class="btn btn-primary" ng-class="{'disabled' : form.$invalid}" ng-click="submit($event)"><i class="icon ion-arrow-right-c"></i><span><?php _e('Save', 'ol-scrapes'); ?></span></button>
						<?php } ?>
						<div class="hidden"><?php post_submit_meta_box($post_object); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="loading" class="modal">
		<div class="modal-dialog">
			<div class="rotate">
				<i class="icon ion-gear-a"></i>
			</div>
		</div>
	</div>

	<div id="error" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<i class="icon ion-android-close" data-dismiss="modal"></i>
				</div>

				<div class="modal-body">
					<i class="icon ion-alert-circled"></i>
					<p>{{error}}</p>
				</div>
			</div>
		</div>
	</div>

	<div id="iframe" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="checkbox">
						<label><input type="checkbox" name="iframe_styles" ng-model="iframe_styles" ng-change="toggle_iframe_styles()"> <?php _e('Disable styles', 'ol-scrapes'); ?></label>
					</div>
					<i class="icon ion-android-close" data-dismiss="modal"></i>
				</div>

				<div class="modal-body">
					<iframe id="iframe_serial" frameborder="0"></iframe>
					<iframe id="iframe_single" frameborder="0"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>