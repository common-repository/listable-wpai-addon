<?php

/*
  Plugin Name: Listable WPAI AddOn
  Plugin URI: https://www.ajouda.com/listable-wpai-addon/#en
  Description: Import geolocation listings and related images into the Listable theme with WP All Import (free or pro version)
  Version: 1.0.1
  Author: AJOUDA.Com
  Author URI: https://wwww.ajouda.com
 */


include "rapid-addon.php";

$aj_listable_addon = new RapidAddon('Listable Add-On', 'aj_listable_addon');

$aj_listable_addon->disable_default_images();

$aj_listable_addon->import_images('aj_listable_addon_listing_gallery', 'Listing Gallery');

/**
 *
 * @param type $post_id
 * @param type $attachment_id
 * @param type $image_filepath
 * @param type $import_options
 */
function aj_listable_addon_listing_gallery($post_id, $attachment_id, $image_filepath, $import_options) {

  // build gallery_images
  $new_url = wp_get_attachment_url($attachment_id);

  $urls = get_post_meta($post_id, '_main_image', true);

  $new_urls = array();

  foreach ($urls as $key => $url) {

    $new_urls[] = $url;
  }

  $new_urls[] = $new_url;

  update_post_meta($post_id, '_main_image', $new_urls);

  //build gallery
  $new_id = $attachment_id;

  $main_image = get_post_meta($post_id, 'main_image', true);
  $ids = empty($main_image) ? "" : explode(",", $main_image);

  $new_ids = array();

  foreach ($ids as $key => $id) {

    $new_ids[] = $id;
  }

  $new_ids[] = $new_id;

  update_post_meta($post_id, 'main_image', implode(",", $new_ids));
}

$aj_listable_addon->add_field('_company_tagline', 'Company Tagline', 'text');

$aj_listable_addon->add_field('_company_website', 'Company Website', 'text');

$aj_listable_addon->add_field('_job_location', 'Location', 'text', null, 'Leave this blank if location is not important');

$aj_listable_addon->add_field('_company_twitter', 'Company Twitter', 'text');

$aj_listable_addon->add_field('_job_hours', 'Hours of Operation', 'text', null, 'Use the format is right for you. (Ex: Mon - Fri: 09:00 - 23:00)');

$aj_listable_addon->add_field('_company_phone', 'Company Phone', 'text');

$aj_listable_addon->add_field('_job_expires', 'Listing Expiry Date', 'text', null, 'Import date in any strtotime compatible format.');

$aj_listable_addon->add_field('_filled', 'Filled', 'radio', array(
    '0' => 'No',
    '1' => 'Yes'
        ), 'Filled listings will no longer accept applications.'
);

$aj_listable_addon->add_field('_featured', 'Featured Listing', 'radio', array(
    '0' => 'No',
    '1' => 'Yes'
        ), 'Featured listings will be sticky during searches, and can be styled differently.'
);


$aj_listable_addon->add_options(
        null, 'Geolocation Options', array(
    $aj_listable_addon->add_field('geolocated', 'Geolocated', 'radio', array('0' => 'No', '1' => 'Yes'), 'Indicate'),
    $aj_listable_addon->add_field('geolocation_lat', 'Latitude', 'text'),
    $aj_listable_addon->add_field('geolocation_long', 'Longitude', 'text'),
    $aj_listable_addon->add_field('geolocation_formatted_address', 'Formatted address', 'text', null, 'E.g.: 3 Abbey Rd, London NW8 9AY, United-Kingdom'),
    $aj_listable_addon->add_field('geolocation_street_number', 'Street number', 'text', null, 'E.g: 3'),
    $aj_listable_addon->add_field('geolocation_street', 'Street name', 'text', null, 'E.g.: Abbey Road'),
    $aj_listable_addon->add_field('geolocation_city', 'City', 'text', null, 'E.g.: London'),
    $aj_listable_addon->add_field('geolocation_state_short', 'State short name', 'text', null, 'E.g.: Greater London'),
    $aj_listable_addon->add_field('geolocation_state_long', 'State long name', 'text', null, 'E.g.: Greater London'),
    $aj_listable_addon->add_field('geolocation_postcode', 'Postcode', 'text', null, 'E.g.: NW8 9AY'),
    $aj_listable_addon->add_field('geolocation_country_short', 'Country short name', 'text', null, 'E.g.: GB'),
    $aj_listable_addon->add_field('geolocation_country_long', 'Country Long name', 'text', null, 'E.g.: United-Kingdom')
        )
);


$aj_listable_addon->set_import_function('aj_listable_addon_import');

$aj_listable_addon->admin_notice(
        'The Listable WPAI Addon requires WP All Import <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://pixelgrade.com/demos/listable/">Listable</a> theme.', array(
    'themes' => array('Listable')
));

$aj_listable_addon->run(array(
    'themes' => array('Listable'),
    'post_types' => array('job_listing')
));

/**
 *
 * @global RapidAddon $aj_listable_addon
 * @param type $post_id
 * @param type $data
 * @param type $import_options
 */
function aj_listable_addon_import($post_id, $data, $import_options) {

  global $aj_listable_addon;

  // clear image fields to override import settings
  $fields = array(
      '_job_location',
      '_company_name',
      '_company_tagline',
      '_company_description',
      '_company_website',
      '_company_phone',
      '_company_twitter',
      '_filled',
      '_featured',
      'geolocated',
      'geolocation_lat',
      'geolocation_long',
      'geolocation_formatted_address',
      'geolocation_street_number',
      'geolocation_street',
      'geolocation_city',
      'geolocation_state_short',
      'geolocation_state_long',
      'geolocation_postcode',
      'geolocation_country_short',
      'geolocation_country_long'
  );

  // update everything in fields arrays
  foreach ($fields as $field) {

    if ($aj_listable_addon->can_update_meta($field, $import_options)) {

      update_post_meta($post_id, $field, $data[$field]);
    }
  }

  // clear image fields to override import settings
  $fields = array(
      '_main_image',
      'main_image'
  );

  if ($aj_listable_addon->can_update_image($import_options)) {

    foreach ($fields as $field) {

      delete_post_meta($post_id, $field);
    }
  }

  // update listing expiration date
  $field = '_job_expires';

  $date = $data[$field];

  $date = strtotime($date);

  if ($aj_listable_addon->can_update_meta($field, $import_options) && !empty($date)) {

    $date = date('Y-m-d', $date);

    update_post_meta($post_id, $field, $date);
  }
}
