<?php
namespace Drupal\csv_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\asset\Entity\Asset;
use Drupal\log\Entity\Log;
/**
 * Provides route responses for the Example module.
 */
class CsvImportController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function upload() {
    return [
      '#children' => '
        project summary:
        <form action="/csv_import/upload_inputs" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        market activities:
        <form action="/csv_import/upload_market_activities" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        field enrollment:
        <form action="/csv_import/upload_field_enrollment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        partner activities:
        <form action="/csv_import/upload_partner_activities" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
        producer enrollment:
        <form action="/csv_import/upload_producer_enrollment" enctype="multipart/form-data" method="post">
          <input type="file" id="file" name="file">
          <input type="submit">
        </form>
    ',
    ];
  }

  public function process_market_activities() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $market_activities_submission = [];
      $market_activities_submission['type'] = 'market_activities';
      $market_activities_submission['name'] = $csv_line[0];
      $market_activities_submission['m_activities_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $csv_line[1]]));
      $market_activities_submission['m_activities_marketing_channel_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_type', 'name' => $csv_line[2]]));
      $market_activities_submission['m_activities_marketing_channel_type_other'] = $csv_line[3];
      $market_activities_submission['m_activities_number_of_buyers'] = $csv_line[4];
      $market_activities_submission['m_activities_buyer_names'] = $csv_line[5];
      $market_activities_submission['m_activities_marketing_channel_geography'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_geography', 'name' => $csv_line[6]]));
      $market_activities_submission['m_activities_value_sold'] = $csv_line[7];
      $market_activities_submission['m_activities_volume_sold'] = $csv_line[8];
      $market_activities_submission['m_activities_volume_sold_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'volume_sold_unit', 'name' => $csv_line[9]]));
      $market_activities_submission['m_activities_volume_unit_other'] = $csv_line[10];
      $market_activities_submission['m_activities_price_premium'] = $csv_line[11];
      $market_activities_submission['m_activities_price_premium_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'price_premium_unit', 'name' => $csv_line[12]]));
      $market_activities_submission['m_activities_price_premium_unit_other'] = $csv_line[13];
      $market_activities_submission['m_activities_price_premium_to_producer'] = $csv_line[14];
      $market_activities_submission['m_activities_product_differentiation_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'product_differentiation_method', 'name' => $csv_line[15]]));
      $market_activities_submission['m_activities_product_differentiation_method_other'] = $csv_line[16];
      $market_activities_submission['m_activities_marketing_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_method', 'name' => $csv_line[17]]));
      $market_activities_submission['m_activities_marketing_method_other'] = $csv_line[18];
      $market_activities_submission['m_activities_marketing_channel_identification'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_identification', 'name' => $csv_line[19]]));
      $market_activities_submission['m_activities_marketing_channel_id_methods_other'] = $csv_line[20];
      $market_activities_submission['m_activities_traceability_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'traceability_method', 'name' => $csv_line[21]]));
      $market_activities_submission['m_activities_traceability_method_other'] = $csv_line[22];
      
      $ps_to_save = Log::create($market_activities_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " market activities.",
    ];
    
  }

  public function process_field_enrollment() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $field_enrollment_submission = [];
      $field_enrollment_submission['type'] = 'field_enrollment';
      $field_enrollment_submission['name'] = $csv_line[0];
      $field_enrollment_submission['f_enrollment_tract_id'] = $csv_line[1];
      $field_enrollment_submission['f_enrollment_field_id'] = $csv_line[2];
      $field_enrollment_submission['f_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $csv_line[3]]));
      $field_enrollment_submission['f_enrollment_prior_field_id'] = $csv_line[4];
      $field_enrollment_submission['f_enrollment_start_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $field_enrollment_submission['f_enrollment_total_field_area'] = $csv_line[6];
      $field_enrollment_submission['f_enrollment_commodity_category'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $csv_line[7]]));
      $field_enrollment_submission['f_enrollment_baseline_yield'] = $csv_line[8];
      $field_enrollment_submission['f_enrollment_baseline_yield_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'baseline_yield_unit', 'name' => $csv_line[9]]));
      $field_enrollment_submission['f_enrollment_baseline_yield_unit_other'] = $csv_line[10];
      $field_enrollment_submission['f_enrollment_baseline_yield_location'] = $csv_line[11];
      $field_enrollment_submission['f_enrollment_baseline_yield_location_other'] = $csv_line[12];
      $field_enrollment_submission['f_enrollment_field_land_use'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_land_use', 'name' => $csv_line[13]]));
      $field_enrollment_submission['f_enrollment_field_irrigated'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_irrigated', 'name' => $csv_line[14]]));
      $field_enrollment_submission['f_enrollment_field_tillage'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_tillage', 'name' => $csv_line[15]]));
      $field_enrollment_submission['f_enrollment_practice_prior_utilization_percent'] = $csv_line[16];
      $field_enrollment_submission['f_enrollment_field_any_csaf_practice'] = $csv_line[17];
      $field_enrollment_submission['f_enrollment_field_practice_prior_utilization'] = $csv_line[18];
      $field_enrollment_submission['f_enrollment_practice_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[19]]));
      $field_enrollment_submission['f_enrollment_practice_standard_1'] = $csv_line[20];
      $field_enrollment_submission['f_enrollment_practice_standard_other_1'] = $csv_line[21];
      $field_enrollment_submission['f_enrollment_practice_year_1'] = $csv_line[22];
      $field_enrollment_submission['f_enrollment_practice_extent_1'] = $csv_line[23];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_1'] = $csv_line[24];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_1'] = $csv_line[25];
      $field_enrollment_submission['f_enrollment_practice_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[26]]));
      $field_enrollment_submission['f_enrollment_practice_standard_2'] = $csv_line[27];
      $field_enrollment_submission['f_enrollment_practice_standard_other_2'] = $csv_line[28];
      $field_enrollment_submission['f_enrollment_practice_year_2'] = $csv_line[29];
      $field_enrollment_submission['f_enrollment_practice_extent_2'] = $csv_line[30];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_2'] = $csv_line[31];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_2'] = $csv_line[32];
      $field_enrollment_submission['f_enrollment_practice_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[33]]));
      $field_enrollment_submission['f_enrollment_practice_standard_3'] = $csv_line[34];
      $field_enrollment_submission['f_enrollment_practice_standard_other_3'] = $csv_line[35];
      $field_enrollment_submission['f_enrollment_practice_year_3'] = $csv_line[36];
      $field_enrollment_submission['f_enrollment_practice_extent_3'] = $csv_line[37];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_3'] = $csv_line[38];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_3'] = $csv_line[39];
      $field_enrollment_submission['f_enrollment_practice_type_4'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[40]]));
      $field_enrollment_submission['f_enrollment_practice_standard_4'] = $csv_line[41];
      $field_enrollment_submission['f_enrollment_practice_standard_other_4'] = $csv_line[42];
      $field_enrollment_submission['f_enrollment_practice_year_4'] = $csv_line[43];
      $field_enrollment_submission['f_enrollment_practice_extent_4'] = $csv_line[44];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_4'] = $csv_line[45];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_4'] = $csv_line[46];
      $field_enrollment_submission['f_enrollment_practice_type_5'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[47]]));
      $field_enrollment_submission['f_enrollment_practice_standard_5'] = $csv_line[48];
      $field_enrollment_submission['f_enrollment_practice_standard_other_5'] = $csv_line[49];
      $field_enrollment_submission['f_enrollment_practice_year_5'] = $csv_line[50];
      $field_enrollment_submission['f_enrollment_practice_extent_5'] = $csv_line[51];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_5'] = $csv_line[52];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_5'] = $csv_line[53];
      $field_enrollment_submission['f_enrollment_practice_type_6'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[54]]));
      $field_enrollment_submission['f_enrollment_practice_standard_6'] = $csv_line[55];
      $field_enrollment_submission['f_enrollment_practice_standard_other_6'] = $csv_line[56];
      $field_enrollment_submission['f_enrollment_practice_year_6'] = $csv_line[57];
      $field_enrollment_submission['f_enrollment_practice_extent_6'] = $csv_line[58];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_6'] = $csv_line[59];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_6'] = $csv_line[60];
      $field_enrollment_submission['f_enrollment_practice_type_7'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $csv_line[61]]));
      $field_enrollment_submission['f_enrollment_practice_standard_7'] = $csv_line[62];
      $field_enrollment_submission['f_enrollment_practice_standard_other_7'] = $csv_line[63];
      $field_enrollment_submission['f_enrollment_practice_year_7'] = $csv_line[64];
      $field_enrollment_submission['f_enrollment_practice_extent_7'] = $csv_line[65];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_7'] = $csv_line[66];
      $field_enrollment_submission['f_enrollment_practice_extent_unit_other_7'] = $csv_line[67];
      


      
      
      $ps_to_save = Asset::create($field_enrollment_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " field enrollment.",
    ];
    
  }

  public function process_partner_activities() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $partner_activities_submission = [];
      $partner_activities_submission['type'] = 'partner_activities';
      $partner_activities_submission['name'] = $csv_line[0];
      $partner_activities_submission['partner_activity_partner_ein'] = $csv_line[1];
      $partner_activities_submission['partner_activity_partner_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'partner_type', 'name' => $csv_line[2]]));
      $partner_activities_submission['partner_activity_partner_poc'] = $csv_line[3];
      $partner_activities_submission['partner_activity_partner_poc_email'] = $csv_line[4];
      $partner_activities_submission['partner_activity_partnership_start'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $partner_activities_submission['partner_activity_partnership_end'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[6])->getTimestamp();
      $partner_activities_submission['partner_activity_partnership_initation'] = filter_var($csv_line[7], FILTER_VALIDATE_BOOLEAN);
      $partner_activities_submission['partner_activity_partner_total_requested'] = $csv_line[8];
      $partner_activities_submission['partner_activity_total_match_contribution'] = $csv_line[9];
      $partner_activities_submission['partner_activity_total_match_incentives'] = $csv_line[10];
      $partner_activities_submission['partner_activity_match_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $csv_line[11]]));
      $partner_activities_submission['partner_activity_match_amount_1'] = $csv_line[12];
      $partner_activities_submission['partner_activity_match_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $csv_line[13]]));
      $partner_activities_submission['partner_activity_match_amount_2'] = $csv_line[14];
      $partner_activities_submission['partner_activity_match_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $csv_line[15]]));
      $partner_activities_submission['partner_activity_match_amount_3'] = $csv_line[16];
      $partner_activities_submission['partner_activity_match_type_other'] = $csv_line[17];
      $partner_activities_submission['partner_activity_training_provided'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'training_provided', 'name' => $csv_line[18]]));
      $partner_activities_submission['partner_activity_training_other'] = $csv_line[19];
      $partner_activities_submission['partner_activity_activity1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $csv_line[20]]));
      $partner_activities_submission['partner_activity_activity1_cost'] = $csv_line[21];
      $partner_activities_submission['partner_activity_activity2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $csv_line[22]]));
      $partner_activities_submission['partner_activity_activity2_cost'] = $csv_line[23];
      $partner_activities_submission['partner_activity_activity3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $csv_line[24]]));
      $partner_activities_submission['partner_activity_activity3_cost'] = $csv_line[25];
      $partner_activities_submission['partner_activity_activity_other'] = $csv_line[26];
      $partner_activities_submission['partner_activity_products_supplied'] = $csv_line[27];
      $partner_activities_submission['partner_activity_product_source'] = $csv_line[28];
      
      
      $ps_to_save = Asset::create($partner_activities_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " partner activities.",
    ];
    
  }

  public function process_producer_enrollment() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $producer_enrollment_submission = [];
      $producer_enrollment_submission['type'] = 'producer_enrollment';
      $producer_enrollment_submission['name'] = $csv_line[0];
      $producer_enrollment_submission['project_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'project_summary', 'name' => $csv_line[1]]));
      $producer_enrollment_submission['p_enrollment_farm_id'] = $csv_line[2];
      $producer_enrollment_submission['p_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $csv_line[3]]));
      $producer_enrollment_submission['p_enrollment_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' => $csv_line[4]]));
      $producer_enrollment_submission['p_enrollment_start_date'] = \DateTime::createFromFormat("D, m/d/Y - G:i", $csv_line[5])->getTimestamp();
      $producer_enrollment_submission['p_enrollment_underserved_status'] = $csv_line[6];
      $producer_enrollment_submission['p_enrollment_total_area'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'total_area', 'name' => $csv_line[7]]));
      $producer_enrollment_submission['p_enrollment_total_crop_area'] = $csv_line[8];
      $producer_enrollment_submission['p_enrollment_total_livestock_area'] = $csv_line[9];
      $producer_enrollment_submission['p_enrollment_total_forest_area'] = $csv_line[10];
      $producer_enrollment_submission['p_enrollment_livestock_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $csv_line[11]]));
      $producer_enrollment_submission['p_enrollment_livestock_type_1_count'] = $csv_line[12];
      $producer_enrollment_submission['p_enrollment_livestock_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $csv_line[13]]));
      $producer_enrollment_submission['p_enrollment_livestock_type_2_count'] = $csv_line[14];
      $producer_enrollment_submission['p_enrollment_livestock_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $csv_line[15]]));
      $producer_enrollment_submission['p_enrollment_livestock_type_3_count'] = $csv_line[16];
      $producer_enrollment_submission['p_enrollment_livestock_type_other'] = $csv_line[17];
      $producer_enrollment_submission['p_enrollment_organic_farm'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_farm', 'name' => $csv_line[18]]));
      $producer_enrollment_submission['p_enrollment_organic_fields'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_fields', 'name' => $csv_line[19]]));
      $producer_enrollment_submission['p_enrollment_producer_motivation'] = $csv_line[20];
      $producer_enrollment_submission['p_enrollment_producer_outreach'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'producer_outreach', 'name' => $csv_line[21]]));
      $producer_enrollment_submission['p_enrollment_producer_outreach_other'] = $csv_line[22];
      $producer_enrollment_submission['p_enrollment_csaf_experience'] =array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_experience', 'name' => $csv_line[23]]));
      $producer_enrollment_submission['p_enrollment_csaf_federal_funds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_federal_funds', 'name' => $csv_line[24]]));
      $producer_enrollment_submission['p_enrollment_csaf_state_local_funds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_state_or_local_funds', 'name' => $csv_line[25]]));
      $producer_enrollment_submission['p_enrollment_csaf_nonprofit_funds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_nonprofit_funds', 'name' => $csv_line[26]]));
      $producer_enrollment_submission['p_enrollment_csaf_market_incentives'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_market_incentives', 'name' => $csv_line[27]]));
      
      $ps_to_save = Asset::create($producer_enrollment_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " producer enrollment.",
    ];
    
  }

  public function process_project_summary() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));
    array_shift($csv);
    $out = 0;

    foreach($csv as $csv_line) {
      $project_summary_submission = [];
      $project_summary_submission['type'] = 'project_summary';
      $project_summary_submission['p_summary_ghg_benefits'] = $csv_line[3]; //strtotime($csv_line[1]);
      $project_summary_submission['p_summary_cumulative_carbon_stack'] = $csv_line[4];
      $project_summary_submission['p_summary_cumulative_co2_benefit'] = $csv_line[5];
      $project_summary_submission['p_summary_cumulative_ch4_benefit'] = $csv_line[6];
      $project_summary_submission['p_summary_cumulative_n2o_benefit'] = $csv_line[7];
      $project_summary_submission['p_summary_offsets_produced'] = $csv_line[8];
      $project_summary_submission['p_summary_offsets_sale'] = $csv_line[9];
      $project_summary_submission['p_summary_offsets_price'] = $csv_line[10];
      $project_summary_submission['p_summary_insets_produced'] = $csv_line[11];
      $project_summary_submission['p_summary_cost_on_farm'] = $csv_line[12];
      $project_summary_submission['p_summary_mmrv_cost'] = $csv_line[13];
      
      $ps_to_save = Asset::create($project_summary_submission);

      $ps_to_save->save();

      $out = $out + 1;
    }

    return [
      "#children" => "added " . $out . " project summary.",
    ];
    
  }

  public function process_combo() {
    // grab the contents of the file and same some info
    $file = \Drupal::request()->files->get("file");
    $file_name = $file->getClientOriginalName();
    $item_count = 0;
    $file_loc = $file->getRealPath();
    
    $csv = array_map('str_getcsv', file($file_loc));
    array_shift($csv);

    $out_str = "test";

    return [
      "#children" => $out_str,
    ];
    
  }
  
  public function process_operations_with_other_costs() {
    $file = \Drupal::request()->files->get("file");
    $fName = $file->getClientOriginalName();
    $fLoc = $file->getRealPath();
    $csv = array_map('str_getcsv', file($fLoc));

    $oc_index =  array_search("other_costs",$csv[0]);

    $csv_oc = $csv[1][$oc_index];

    $result = str_replace( '"', '', $csv_oc);

    $exps = explode("|",$result);

    $csid = [];

    foreach( $exps as $exp){
      
      $cval = explode(",",$exp);
      $cost = $cval[0];
      $cost_type = $cval[1];
      
      //create cost sequence
      $cost_sequence = [];
      $cost_sequence['type'] = 'cost_sequence';
      $cost_sequence['field_cost_type'] = ['target_id' => $cost_type];
      $cost_sequence['field_cost'] = $cost;
      $cost_sequenceN = Asset::create($cost_sequence);
      $cost_sequenceN->save();
      $nid = $cost_sequenceN->id();
      $csid[] = $nid;

    }

    $shmu = \Drupal::entityTypeManager()->getStorage('asset')->load($csv[1][0]);
    $project = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('project')->target_id);
    $field_input = \Drupal::entityTypeManager()->getStorage('asset')->load($csv[1][2]);
    $operation_submission = [];
    $operation_submission['type'] = 'operation';
    $operation_submission['shmu'] = $shmu;
    $operation_submission['field_operation_date'] = strtotime($csv[1][1]);
    $operation_submission['field_operation'] = $csv[1][3];
    $operation_submission['field_ownership_status'] = $csv[1][4];
    $operation_submission['field_tractor_self_propelled_machine'] = $csv[1][5];
    $operation_submission['field_row_number'] = $csv[1][6];
    $operation_submission['field_width'] = $csv[1][7];
    $operation_submission['field_horsepower'] = $csv[1][8];
    $operation_submission['project'] = $project;
    $operation_submission['field_operation_cost_sequences'] = $csid;
    $operation_to_save = Asset::create($operation_submission);
    $operation_to_save->save();

    return [
      "#children" => nl2br(print_r("saved", true)),
    ];
    
  }

}