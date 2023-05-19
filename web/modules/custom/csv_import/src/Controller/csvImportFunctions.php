<?php

use Drupal\asset\Entity\Asset;
use Drupal\log\Entity\Log;

function import_project_summary($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'ps'. $dateConst . $cur_count;

    $project_summary_submission = [];
    $project_summary_submission['type'] = 'project_summary';
    $project_summary_submission['name'] = $entry_name;
    $project_summary_submission['p_summary_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $in_data_array[0]]));
    $project_summary_submission['p_summary_ghg_calculation_methods'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_calculation_methods', 'name' => $in_data_array[3]]));
    $project_summary_submission['p_summary_ghg_cumulative_calculation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_cumulative_calculation', 'name' => $in_data_array[4]]));
    $project_summary_submission['p_summary_ghg_benefits'] = $in_data_array[5];
    $project_summary_submission['p_summary_cumulative_carbon_stack'] = $in_data_array[6];
    $project_summary_submission['p_summary_cumulative_co2_benefit'] = $in_data_array[7];
    $project_summary_submission['p_summary_cumulative_ch4_benefit'] = $in_data_array[8];
    $project_summary_submission['p_summary_cumulative_n2o_benefit'] = $in_data_array[9];
    $project_summary_submission['p_summary_offsets_produced'] = $in_data_array[10];
    $project_summary_submission['p_summary_offsets_sale'] = $in_data_array[11];
    $project_summary_submission['p_summary_offsets_price'] = $in_data_array[12];
    $project_summary_submission['p_summary_insets_produced'] = $in_data_array[13];
    $project_summary_submission['p_summary_cost_on_farm'] = $in_data_array[14];
    $project_summary_submission['p_summary_mmrv_cost'] = $in_data_array[15];
    $project_summary_submission['p_summary_ghg_monitoring_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_monitoring_method', 'name' => $in_data_array[16]]));
    $project_summary_submission['p_summary_ghg_reporting_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_reporting_method', 'name' => $in_data_array[22]]));
    $project_summary_submission['p_summary_ghg_verification_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_verification_method', 'name' => $in_data_array[28]]));

    $ps_to_save = Asset::create($project_summary_submission);
            
    $ps_to_save->save();
}

function import_partner_activities($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'pa'. $dateConst . $cur_count;

    $partner_activities_submission = [];
    $partner_activities_submission['type'] = 'partner_activities';
    $partner_activities_submission['name'] = $entry_name;
    $partner_activities_submission['partner_activity_partner_ein'] = $in_data_array[0];
    $partner_activities_submission['partner_activity_partner_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'partner_type', 'name' => $in_data_array[2]]));
    $partner_activities_submission['partner_activity_partner_poc'] = $in_data_array[3];
    $partner_activities_submission['partner_activity_partner_poc_email'] = $in_data_array[4];

    $ndate = convertExcelDate($in_data_array[5]);
    $partner_activities_submission['partner_activity_partnership_start'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    
    $ndate = convertExcelDate($in_data_array[6]);
    $partner_activities_submission['partner_activity_partnership_end'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();

    $partner_activities_submission['partner_activity_partnership_initation'] = filter_var($in_data_array[7], FILTER_VALIDATE_BOOLEAN);
    $partner_activities_submission['partner_activity_partner_total_requested'] = $in_data_array[8];
    $partner_activities_submission['partner_activity_total_match_contribution'] = $in_data_array[9];
    $partner_activities_submission['partner_activity_total_match_incentives'] = $in_data_array[10];
    $partner_activities_submission['partner_activity_match_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $in_data_array[11]]));
    $partner_activities_submission['partner_activity_match_amount_1'] = $in_data_array[12];
    $partner_activities_submission['partner_activity_match_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $in_data_array[13]]));
    $partner_activities_submission['partner_activity_match_amount_2'] = $in_data_array[14];
    $partner_activities_submission['partner_activity_match_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $in_data_array[15]]));
    $partner_activities_submission['partner_activity_match_amount_3'] = $in_data_array[16];
    $partner_activities_submission['partner_activity_match_type_other'] = $in_data_array[17];

    $training_provided = '';
    for($i=18; $i<21; $i++){
        if(!empty($in_data_array[$i])){
            if($training_provided == ''){
                $training_provided = $in_data_array[$i];
            }else{
                $training_provided = $training_provided . ' | ' . $in_data_array[$i];
            }
        }
    }
    $training_provided_array = array_map('trim', explode('|', $training_provided));
      $training_provided_results = [];
      foreach ($training_provided_array as $value) {
        $training_provided_results = array_merge($training_provided_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'training_provided', 'name' => $value]));
      }
    $partner_activities_submission['partner_activity_training_provided'] = $training_provided_results;

    $partner_activities_submission['partner_activity_training_other'] = $in_data_array[21];
    $partner_activities_submission['partner_activity_activity1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $in_data_array[22]]));
    $partner_activities_submission['partner_activity_activity1_cost'] = $in_data_array[23];
    $partner_activities_submission['partner_activity_activity2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $in_data_array[24]]));
    $partner_activities_submission['partner_activity_activity2_cost'] = $in_data_array[25];
    $partner_activities_submission['partner_activity_activity3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $in_data_array[26]]));
    $partner_activities_submission['partner_activity_activity3_cost'] = $in_data_array[27];
    $partner_activities_submission['partner_activity_activity_other'] = $in_data_array[28];
    $partner_activities_submission['partner_activity_products_supplied'] = $in_data_array[29];
    $partner_activities_submission['partner_activity_product_source'] = $in_data_array[30];
    
    $ps_to_save = Asset::create($partner_activities_submission);

    $ps_to_save->save();
}

function import_market_activities($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'ma'. $dateConst . $cur_count;

    $market_activities_submission = [];
    $market_activities_submission['type'] = 'market_activities';
    $market_activities_submission['name'] = $entry_name;
    $market_activities_submission['m_activities_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $in_data_array[0]]));
    $market_activities_submission['m_activities_marketing_channel_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_type', 'name' => $in_data_array[1]]));
    $market_activities_submission['m_activities_marketing_channel_type_other'] = $in_data_array[2];
    $market_activities_submission['m_activities_number_of_buyers'] = $in_data_array[3];

    $buyer_names = '';
    for($i=4; $i<5; $i++){
        if(!empty($in_data_array[$i])){
            if($buyer_names == ''){
                $buyer_names = $in_data_array[$i];
            }else{
                $buyer_names = $buyer_names . ' | ' . $in_data_array[$i];
            }
        }
    }
    $market_activities_submission['m_activities_buyer_names'] = array_map('trim', explode('|', $buyer_names));

    $market_activities_submission['m_activities_marketing_channel_geography'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_geography', 'name' => $in_data_array[5]]));
    $market_activities_submission['m_activities_value_sold'] = $in_data_array[6];
    $market_activities_submission['m_activities_volume_sold'] = $in_data_array[7];
    $market_activities_submission['m_activities_volume_sold_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'volume_sold_unit', 'name' => $in_data_array[8]]));
    $market_activities_submission['m_activities_volume_unit_other'] = $in_data_array[9];
    $market_activities_submission['m_activities_price_premium'] = $in_data_array[10];
    $market_activities_submission['m_activities_price_premium_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'price_premium_unit', 'name' => $in_data_array[11]]));
    $market_activities_submission['m_activities_price_premium_unit_other'] = $in_data_array[12];
    $market_activities_submission['m_activities_price_premium_to_producer'] = $in_data_array[13];

    $product_differentiation = '';
    for($i=14; $i<17; $i++){
        if(!empty($in_data_array[$i])){
            if($product_differentiation == ''){
                $product_differentiation = $in_data_array[$i];
            }else{
                $product_differentiation = $product_differentiation . ' | ' . $in_data_array[$i];
            }
        }
    }
    $product_differentiation_method_array = array_map('trim', explode('|', $product_differentiation));
    $product_differentiation_method_results = [];
    foreach ($product_differentiation_method_array as $value) {
      $product_differentiation_method_results = array_merge($product_differentiation_method_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'product_differentiation_method', 'name' => $value]));
    }
    $market_activities_submission['m_activities_product_differentiation_method'] = $product_differentiation_method_results;

    $market_activities_submission['m_activities_product_differentiation_method_other'] = $in_data_array[17];

    $marketing_method = '';
    for($i=18; $i<21; $i++){
        if(!empty($in_data_array[$i])){
            if($marketing_method == ''){
                $marketing_method = $in_data_array[$i];
            }else{
                $marketing_method = $marketing_method . ' | ' . $in_data_array[$i];
            }
        }
    }
    $marketing_method_array = array_map('trim', explode('|', $marketing_method));
    $marketing_method_results = [];
    foreach ($marketing_method_array as $value) {
      $marketing_method_results = array_merge($marketing_method_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_method', 'name' => $value]));
    }
    $market_activities_submission['m_activities_marketing_method'] = $marketing_method_results;

    $market_activities_submission['m_activities_marketing_method_other'] = $in_data_array[21];

    $marketing_channel_identification = '';
    for($i=22; $i<25; $i++){
        if(!empty($in_data_array[$i])){
            if($marketing_channel_identification == ''){
                $marketing_channel_identification = $in_data_array[$i];
            }else{
                $marketing_channel_identification = $marketing_channel_identification . ' | ' . $in_data_array[$i];
            }
        }
    }
    $marketing_channel_identification_array = array_map('trim', explode('|', $marketing_channel_identification));
      $marketing_channel_identification_results = [];
      foreach ($marketing_channel_identification_array as $value) {
        $marketing_channel_identification_results = array_merge($marketing_channel_identification_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_identification', 'name' => $value]));
      }
    $market_activities_submission['m_activities_marketing_channel_identification'] = $marketing_channel_identification_results;

    $market_activities_submission['m_activities_marketing_channel_id_methods_other'] = $in_data_array[25];

    $traceability_method = '';
    for($i=26; $i<29; $i++){
        if(!empty($in_data_array[$i])){
            if($traceability_method == ''){
                $traceability_method = $in_data_array[$i];
            }else{
                $traceability_method = $traceability_method . ' | ' . $in_data_array[$i];
            }
        }
    }
    $traceability_method_array = array_map('trim', explode('|', $traceability_method));
      $traceability_method_results = [];
      foreach ($traceability_method_array as $value) {
        $traceability_method_results = array_merge($traceability_method_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'traceability_method', 'name' => $value]));
      }
    $market_activities_submission['m_activities_traceability_method'] = $traceability_method_results;

    $market_activities_submission['m_activities_traceability_method_other'] = $in_data_array[29];
    $ps_to_save = Log::create($market_activities_submission);

    $ps_to_save->save();
}

function import_producer_enrollment($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'pe'. $dateConst . $cur_count;

    $producer_enrollment_submission = [];
    $producer_enrollment_submission['type'] = 'producer_enrollment';
    $producer_enrollment_submission['name'] = $entry_name;
    $producer_enrollment_submission['project_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'project_summary', 'name' => $entry_name]));
    $producer_enrollment_submission['p_enrollment_farm_id'] = $in_data_array[0];
    $producer_enrollment_submission['p_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' =>  $in_data_array[1]]));
    $producer_enrollment_submission['p_enrollment_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' =>  $in_data_array[2]]));

    $ndate = convertExcelDate($in_data_array[4]);
    $producer_enrollment_submission['p_enrollment_start_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $producer_enrollment_submission['p_enrollment_underserved_status'] = $in_data_array[6];
    $producer_enrollment_submission['p_enrollment_total_area'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'total_area', 'name' => $in_data_array[7]]));
    $producer_enrollment_submission['p_enrollment_total_crop_area'] = $in_data_array[8];
    $producer_enrollment_submission['p_enrollment_total_livestock_area'] = $in_data_array[9];
    $producer_enrollment_submission['p_enrollment_total_forest_area'] = $in_data_array[10];
    $producer_enrollment_submission['p_enrollment_livestock_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $in_data_array[11]]));
    $producer_enrollment_submission['p_enrollment_livestock_type_1_count'] = $in_data_array[12];
    $producer_enrollment_submission['p_enrollment_livestock_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $in_data_array[13]]));
    $producer_enrollment_submission['p_enrollment_livestock_type_2_count'] = $in_data_array[14];
    $producer_enrollment_submission['p_enrollment_livestock_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $in_data_array[15]]));
    $producer_enrollment_submission['p_enrollment_livestock_type_3_count'] = $in_data_array[16];
    $producer_enrollment_submission['p_enrollment_livestock_type_other'] = $in_data_array[17];
    $producer_enrollment_submission['p_enrollment_organic_farm'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_farm', 'name' => $in_data_array[18]]));
    $producer_enrollment_submission['p_enrollment_organic_fields'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_fields', 'name' => $in_data_array[19]]));
    $producer_enrollment_submission['p_enrollment_producer_motivation'] = $in_data_array[20];

    $producer_outreach_v = '';
    for($i=21; $i<24; $i++){
        if(!empty($in_data_array[$i])){
            if($producer_outreach_v == ''){
                $producer_outreach_v = $in_data_array[$i];
            }else{
                $producer_outreach_v = $producer_outreach_v . ' | ' . $in_data_array[$i];
            }
        }
    }

    $producer_outreach_array = array_map('trim', explode('|', $producer_outreach_v));

    $producer_outreach_results = [];
    foreach ($producer_outreach_array as $value) {
      $producer_outreach_results = array_merge($producer_outreach_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'producer_outreach', 'name' => $value]));
    }
    $producer_enrollment_submission['p_enrollment_producer_outreach'] = $producer_outreach_results;
    $producer_enrollment_submission['p_enrollment_producer_outreach_other'] = $in_data_array[24];
    $producer_enrollment_submission['p_enrollment_csaf_experience'] =array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_experience', 'name' => $in_data_array[25]]));
    $producer_enrollment_submission['p_enrollment_csaf_federal_funds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_federal_funds', 'name' => $in_data_array[26]]));
    $producer_enrollment_submission['p_enrollment_csaf_state_local_funds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_state_or_local_funds', 'name' => $in_data_array[27]]));
    $producer_enrollment_submission['p_enrollment_csaf_nonprofit_funds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_nonprofit_funds', 'name' => $in_data_array[28]]));
    $producer_enrollment_submission['p_enrollment_csaf_market_incentives'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_market_incentives', 'name' => $in_data_array[29]]));
    
    $ps_to_save = Asset::create($producer_enrollment_submission);

    $ps_to_save->save();

}

function import_field_enrollment($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'fe'. $dateConst . $cur_count;

    $field_enrollment_submission = [];
    $field_enrollment_submission['type'] = 'field_enrollment';
    $field_enrollment_submission['name'] = $entry_name;
    $field_enrollment_submission['f_enrollment_tract_id'] = $in_data_array[1];
    $field_enrollment_submission['f_enrollment_field_id'] = $in_data_array[2];
    $field_enrollment_submission['f_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $in_data_array[3]]));
    $field_enrollment_submission['f_enrollment_prior_field_id'] = $in_data_array[4];;

    $ndate = convertExcelDate($in_data_array[7]);
    $field_enrollment_submission['f_enrollment_start_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $field_enrollment_submission['f_enrollment_total_field_area'] = $in_data_array[8];;
    $field_enrollment_submission['f_enrollment_commodity_category'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $in_data_array[10]]));
    $field_enrollment_submission['f_enrollment_baseline_yield'] = $in_data_array[11];
    $field_enrollment_submission['f_enrollment_baseline_yield_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'baseline_yield_unit', 'name' => $in_data_array[12]]));
    $field_enrollment_submission['f_enrollment_baseline_yield_unit_other'] = $in_data_array[13];
    $field_enrollment_submission['f_enrollment_baseline_yield_location'] = $in_data_array[14];
    $field_enrollment_submission['f_enrollment_baseline_yield_location_other'] = $in_data_array[15];
    $field_enrollment_submission['f_enrollment_field_land_use'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_land_use', 'name' => $in_data_array[16]]));
    $field_enrollment_submission['f_enrollment_field_irrigated'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_irrigated', 'name' => $in_data_array[17]]));
    $field_enrollment_submission['f_enrollment_field_tillage'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_tillage', 'name' => $in_data_array[18]]));
    $field_enrollment_submission['f_enrollment_practice_prior_utilization_percent'] = $in_data_array[19];
    $field_enrollment_submission['f_enrollment_field_any_csaf_practice'] = $in_data_array[20];
    $field_enrollment_submission['f_enrollment_field_practice_prior_utilization'] = $in_data_array[21];
    $field_enrollment_submission['f_enrollment_practice_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[22]]));
    $field_enrollment_submission['f_enrollment_practice_standard_1'] = $in_data_array[23];
    $field_enrollment_submission['f_enrollment_practice_standard_other_1'] = $in_data_array[24];
    $field_enrollment_submission['f_enrollment_practice_year_1'] = $in_data_array[25];
    $field_enrollment_submission['f_enrollment_practice_extent_1'] = $in_data_array[26];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_1'] = $in_data_array[27];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_1'] = $in_data_array[28];
    $field_enrollment_submission['f_enrollment_practice_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[29]]));
    $field_enrollment_submission['f_enrollment_practice_standard_2'] = $in_data_array[30];
    $field_enrollment_submission['f_enrollment_practice_standard_other_2'] = $in_data_array[31];
    $field_enrollment_submission['f_enrollment_practice_year_2'] = $in_data_array[32];
    $field_enrollment_submission['f_enrollment_practice_extent_2'] = $in_data_array[33];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_2'] = $in_data_array[34];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_2'] = $in_data_array[35];
    $field_enrollment_submission['f_enrollment_practice_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[36]]));
    $field_enrollment_submission['f_enrollment_practice_standard_3'] = $in_data_array[37];
    $field_enrollment_submission['f_enrollment_practice_standard_other_3'] = $in_data_array[38];
    $field_enrollment_submission['f_enrollment_practice_year_3'] = $in_data_array[39];
    $field_enrollment_submission['f_enrollment_practice_extent_3'] = $in_data_array[40];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_3'] = $in_data_array[41];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_3'] = $in_data_array[42];
    $field_enrollment_submission['f_enrollment_practice_type_4'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[43]]));
    $field_enrollment_submission['f_enrollment_practice_standard_4'] = $in_data_array[44];
    $field_enrollment_submission['f_enrollment_practice_standard_other_4'] = $in_data_array[45];
    $field_enrollment_submission['f_enrollment_practice_year_4'] = $in_data_array[46];
    $field_enrollment_submission['f_enrollment_practice_extent_4'] = $in_data_array[47];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_4'] = $in_data_array[48];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_4'] = $in_data_array[49];
    $field_enrollment_submission['f_enrollment_practice_type_5'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[50]]));
    $field_enrollment_submission['f_enrollment_practice_standard_5'] = $in_data_array[51];
    $field_enrollment_submission['f_enrollment_practice_standard_other_5'] = $in_data_array[52];
    $field_enrollment_submission['f_enrollment_practice_year_5'] = $in_data_array[53];
    $field_enrollment_submission['f_enrollment_practice_extent_5'] = $in_data_array[54];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_5'] = $in_data_array[55];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_5'] = $in_data_array[56];
    $field_enrollment_submission['f_enrollment_practice_type_6'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[57]]));
    $field_enrollment_submission['f_enrollment_practice_standard_6'] = $in_data_array[58];
    $field_enrollment_submission['f_enrollment_practice_standard_other_6'] = $in_data_array[59];
    $field_enrollment_submission['f_enrollment_practice_year_6'] = $in_data_array[60];
    $field_enrollment_submission['f_enrollment_practice_extent_6'] = $in_data_array[61];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_6'] = $in_data_array[62];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_6'] = $in_data_array[63];
    $field_enrollment_submission['f_enrollment_practice_type_7'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[64]]));
    $field_enrollment_submission['f_enrollment_practice_standard_7'] = $in_data_array[65];
    $field_enrollment_submission['f_enrollment_practice_standard_other_7'] = $in_data_array[66];
    $field_enrollment_submission['f_enrollment_practice_year_7'] = $in_data_array[67];
    $field_enrollment_submission['f_enrollment_practice_extent_7'] = $in_data_array[68];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_7'] = $in_data_array[69];
    $field_enrollment_submission['f_enrollment_practice_extent_unit_other_7'] = $in_data_array[70];
    
    $ps_to_save = Asset::create($field_enrollment_submission);

    $ps_to_save->save();

}

function import_farm_summary($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'fa'. $dateConst . $cur_count;

    $farm_summary_submission = [];
    $farm_summary_submission['type'] = 'farm_summary';
    $farm_summary_submission['name'] = $entry_name;
    $farm_summary_submission['farm_summary_fiscal_year'] = '';
    $farm_summary_submission['farm_summary_fiscal_quarter'] = '';
    $farm_summary_submission['farm_summary_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $in_data_array[1]]));
    $farm_summary_submission['farm_summary_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' => $in_data_array[2]]));
    
    $producer_ta_received_v = '';
    for($i=3; $i<6; $i++){
        if(!empty($in_data_array[$i])){
            if($producer_ta_received_v == ''){
                $producer_ta_received_v = $in_data_array[$i];
            }else{
                $producer_ta_received_v = $producer_ta_received_v . ' | ' . $in_data_array[$i];
            }
        }
    }
    
    $producer_ta_received_array = array_map('trim', explode('|', $producer_ta_received_v));
    $producer_ta_received_results = [];
    foreach ($producer_ta_received_array as $value) {
      $producer_ta_received_results = array_merge($producer_ta_received_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'producer_ta_received', 'name' => $value]));
    }
    $farm_summary_submission['farm_summary_producer_ta_received'] = $producer_ta_received_results;
    $farm_summary_submission['farm_summary_producer_ta_received_other'] = $in_data_array[6];
    $farm_summary_submission['farm_summary_producer_incentive_amount'] = $in_data_array[7];

    $incentive_reason_v = '';
    for($i=8; $i<12; $i++){
        if(!empty($in_data_array[$i])){
            if($incentive_reason_v == ''){
                $incentive_reason_v = $in_data_array[$i];
            }else{
                $incentive_reason_v = $incentive_reason_v . ' | ' . $in_data_array[$i];
            }
        }
    }

    $incentive_reason_array = array_map('trim', explode('|', $incentive_reason_v));
    $incentive_reason_results = [];
    foreach ($incentive_reason_array as $value) {
      $incentive_reason_results = array_merge($incentive_reason_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'incentive_reason', 'name' => $value]));
    }
    $farm_summary_submission['farm_summary_incentive_reason'] = $incentive_reason_results;
    $farm_summary_submission['farm_summary_incentive_reason_other'] = $in_data_array[12];
 
    $incentive_structure_v = '';
    for($i=13; $i<17; $i++){
        if(!empty($in_data_array[$i])){
            if($incentive_structure_v == ''){
                $incentive_structure_v = $in_data_array[$i];
            }else{
                $incentive_structure_v = $incentive_structure_v . ' | ' . $in_data_array[$i];
            }
        }
    }
 
    $incentive_structure_array = array_map('trim', explode('|', $incentive_structure_v));
    $incentive_structure_results = [];
    foreach ($incentive_structure_array as $value) {
      $incentive_structure_results = array_merge($incentive_structure_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'incentive_structure', 'name' => $value]));
    }
    $farm_summary_submission['farm_summary_incentive_structure'] = $incentive_structure_results;
    $farm_summary_submission['farm_summary_incentive_structure_other'] = $in_data_array[17];

    $incentive_type_v = '';
    for($i=18; $i<22; $i++){
        if(!empty($in_data_array[$i])){
            if($incentive_type_v == ''){
                $incentive_type_v = $in_data_array[$i];
            }else{
                $incentive_type_v = $incentive_type_v . ' | ' . $in_data_array[$i];
            }
        }
    }

    $incentive_type_array = array_map('trim', explode('|', $incentive_type_v));
    $incentive_type_results = [];
    foreach ($incentive_type_array as $value) {
      $incentive_type_results = array_merge($incentive_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'incentive_type', 'name' => $value]));
    }
    $farm_summary_submission['farm_summary_incentive_type'] = $incentive_type_results;
    $farm_summary_submission['farm_summary_incentive_type_other'] = $in_data_array[22];
    $farm_summary_submission['farm_summary_payment_on_enrollment'] = $in_data_array[23];
    $farm_summary_submission['farm_summary_payment_on_implementation'] = $in_data_array[24];
    $farm_summary_submission['farm_summary_payment_on_harvest'] = $in_data_array[25];
    $farm_summary_submission['farm_summary_payment_on_mmrv'] = $in_data_array[26];
    $farm_summary_submission['farm_summary_payment_on_sale'] = $in_data_array[27];
    
    $ps_to_save = Log::create($farm_summary_submission);

    $ps_to_save->save();

}

function import_field_summary($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'fs'. $dateConst . $cur_count;

    $field_summary_submission = [];
    $field_summary_submission['type'] = 'field_summary';
    $field_summary_submission['name'] = $entry_name;
    $field_summary_submission['status'] = '';
    $field_summary_submission['flag'] = '';
    $field_summary_submission['notes'] = '';

    $ndate = convertExcelDate($in_data_array[14]);
    $field_summary_submission['f_summary_contract_end_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $field_summary_submission['f_summary_implementation_cost_coverage'] = $in_data_array[25];
    $field_summary_submission['f_summary_implementation_cost'] = $in_data_array[22];
    $field_summary_submission['f_summary_implementation_cost_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'cost_unit', 'name' => $in_data_array[23]]));
    
    $ndate = convertExcelDate($in_data_array[13]);
    $field_summary_submission['f_summary_date_practice_complete'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $field_summary_submission['f_summary_fiscal_quarter'] = '';
    $field_summary_submission['f_summary_fiscal_year'] = '';
    $field_summary_submission['f_summary_field_commodity_value'] = $in_data_array[18];
    $field_summary_submission['f_summary_field_commodity_volume'] = $in_data_array[19];
    $field_summary_submission['f_summary_field_commodity_volume_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_commodity_volume_unit', 'name' => $in_data_array[20]]));
    $field_summary_submission['f_summary_field_ghg_calculation'] = $in_data_array[38];

    $summary_field_ghg_monitoring = '';
    for($i=26; $i<29; $i++){
        if(!empty($in_data_array[$i])){
            if($summary_field_ghg_monitoring == ''){
                $summary_field_ghg_monitoring = $in_data_array[$i];
            }else{
                $summary_field_ghg_monitoring = $summary_field_ghg_monitoring . ' | ' . $in_data_array[$i];
            }
        }
    }

    $summary_field_ghg_monitoring_array = array_map('trim', explode('|', $summary_field_ghg_monitoring));
    $summary_field_ghg_monitoring_results = [];

    foreach ($summary_field_ghg_monitoring_array as $value) {
        $summary_field_ghg_monitoring_results = array_merge($summary_field_ghg_monitoring_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_ghg_monitoring', 'name' => $value]));
    }

    $field_summary_submission['f_summary_field_ghg_monitoring'] = $summary_field_ghg_monitoring_results;

    $summary_field_ghg_reporting = '';
    for($i=30; $i<33; $i++){
        if(!empty($in_data_array[$i])){
            if($summary_field_ghg_reporting == ''){
                $summary_field_ghg_reporting = $in_data_array[$i];
            }else{
                $summary_field_ghg_reporting = $summary_field_ghg_reporting . ' | ' . $in_data_array[$i];
            }
        }
    }

    $summary_field_ghg_reporting_array = array_map('trim', explode('|', $summary_field_ghg_reporting));
    $summary_field_ghg_reporting_results = [];

    foreach ($summary_field_ghg_reporting_array as $value) {
        $summary_field_ghg_reporting_results = array_merge($summary_field_ghg_reporting_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_ghg_reporting', 'name' => $value]));
    }

    $field_summary_submission['f_summary_field_ghg_reporting'] = $summary_field_ghg_reporting_results;

    $summary_field_ghg_verification = '';
    for($i=34; $i<37; $i++){
        if(!empty($in_data_array[$i])){
            if($summary_field_ghg_verification == ''){
                $summary_field_ghg_verification = $in_data_array[$i];
            }else{
                $summary_field_ghg_verification = $summary_field_ghg_verification . ' | ' . $in_data_array[$i];
            }
        }
    }

    $summary_field_ghg_verification_array = array_map('trim', explode('|', $summary_field_ghg_verification));
    $summary_field_ghg_verification_results = [];

    foreach ($summary_field_ghg_verification_array as $value) {
        $summary_field_ghg_verification_results = array_merge($summary_field_ghg_verification_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_ghg_verification', 'name' => $value]));
    }

    $field_summary_submission['f_summary_field_ghg_verification'] = $summary_field_ghg_verification_results;
    $field_summary_submission['f_summary_field_insets'] = $in_data_array[46];
    $field_summary_submission['f_summary_field_carbon_stock'] = $in_data_array[41];
    $field_summary_submission['f_summary_field_ch4_emission_reduction'] = $in_data_array[43];
    $field_summary_submission['f_summary_field_co2_emission_reduction'] = $in_data_array[42];
    $field_summary_submission['f_summary_field_ghg_emission_reduction'] = $in_data_array[40];
    $field_summary_submission['f_summary_field_official_ghg_calculations'] = $in_data_array[39];
    $field_summary_submission['f_summary_field_n2o_emission_reduction'] = $in_data_array[44];
    $field_summary_submission['f_summary_field_offsets'] = $in_data_array[45];
    $field_summary_submission['f_summary_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $in_data_array[5]]));
    $field_summary_submission['f_summary_incentive_per_acre_or_head'] = $in_data_array[17];
    $field_summary_submission['f_summary_marketing_assistance_provided'] = $in_data_array[16];
    $field_summary_submission['f_summary_mmrv_assistance_provided'] = $in_data_array[15];
    $field_summary_submission['f_summary_implementation_cost_unit_other'] = $in_data_array[24];
    $field_summary_submission['f_summary_field_commodity_volume_unit_other'] = $in_data_array[21];
    $field_summary_submission['f_summary_field_ghg_monitoring_other'] = $in_data_array[29];
    $field_summary_submission['f_summary_field_ghg_reporting_other'] = $in_data_array[33];
    $field_summary_submission['f_summary_field_ghg_verification_other'] = $in_data_array[37];
    $field_summary_submission['f_summary_field_measurement_other'] = $in_data_array[47];

    $summary_practice_type = '';
    for($i=6; $i<13; $i++){
        if(!empty($in_data_array[$i])){
            if($summary_practice_type == ''){
                $summary_practice_type = $in_data_array[$i];
            }else{
                $summary_practice_type = $summary_practice_type . ' | ' . $in_data_array[$i];
            }
        }
    }

    $summary_practice_type_array = array_map('trim', explode('|', $summary_practice_type));
    $summary_practice_type_results = [];

    foreach ($summary_practice_type_array as $value) {
        $summary_practice_type_results = array_merge($summary_practice_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $value]));
    }
   
    $field_summary_submission['f_summary_practice_type'] = $summary_practice_type_results;

    $field_summary_submission['f_summary_field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'field_enrollment', 'name' => $in_data_array[2]]));
    
    $ps_to_save = Log::create($field_summary_submission);

    $ps_to_save->save();
}

function import_ghg_benefits_alt_models($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'gbam'. $dateConst . $cur_count;

    $g_benefits_alternate_modeledsubmission = [];
    $g_benefits_alternate_modeledsubmission['name'] = $entry_name;
    $g_benefits_alternate_modeledsubmission['type'] = 'ghg_benefits_alternate_modeled';
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_fiscal_year'] = '';
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_fiscal_quarter'] = '';
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'field_enrollment', 'name' => $in_data_array[2]]));

    $g_benefits_alternate_modeled_commodity_type = '';
    for($i=5; $i<11; $i++){
        if(!empty($in_data_array[$i])){
            if($g_benefits_alternate_modeled_commodity_type == ''){
                $g_benefits_alternate_modeled_commodity_type = $in_data_array[$i];
            }else{
                $g_benefits_alternate_modeled_commodity_type = $g_benefits_alternate_modeled_commodity_type . ' | ' . $in_data_array[$i];
            }
        }
    }

    $g_benefits_alternate_modeled_commodity_type_array = array_map('trim', explode('|', $g_benefits_alternate_modeled_commodity_type));
    
    $g_benefits_alternate_modeled_commodity_type_results = [];
    foreach ($g_benefits_alternate_modeled_commodity_type_array as $value) {
      $g_benefits_alternate_modeled_commodity_type_results = array_merge($g_benefits_alternate_modeled_commodity_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $value]));
    }
    
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_commodity_type'] = $g_benefits_alternate_modeled_commodity_type_results;

    $g_benefits_alternate_modeled_practice_type = '';
    for($i=11; $i<18; $i++){
        if(!empty($in_data_array[$i])){
            if($g_benefits_alternate_modeled_practice_type == ''){
                $g_benefits_alternate_modeled_practice_type = $in_data_array[$i];
            }else{
                $g_benefits_alternate_modeled_practice_type = $g_benefits_alternate_modeled_practice_type . ' | ' . $in_data_array[$i];
            }
        }
    }

    $g_benefits_alternate_modeled_practice_type_array = array_map('trim', explode('|', $g_benefits_alternate_modeled_practice_type));
    $g_benefits_alternate_modeled_practice_type_results = [];
    foreach ($g_benefits_alternate_modeled_practice_type_array as $value) {
      $g_benefits_alternate_modeled_practice_type_results = array_merge($g_benefits_alternate_modeled_practice_type_results, \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $value]));
    }

    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_practice_type'] = $g_benefits_alternate_modeled_practice_type_results;
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ghg_model'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_model', 'name' => $in_data_array[18]]));
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ghg_model_other'] = $in_data_array[19];
    $ndate = convertExcelDate($in_data_array[20]);
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_model_start_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $ndate = convertExcelDate($in_data_array[21]);
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_model_end_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ghg_benefits_estimated'] = $in_data_array[22];
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_carbon_stock_estimated'] = $in_data_array[23];
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_co2_estimated'] = $in_data_array[24];
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_ch4_estimated'] = $in_data_array[25];
    $g_benefits_alternate_modeledsubmission['g_benefits_alternate_modeled_n2o_estimated'] = $in_data_array[26];

    $gbam_to_save = log::create($g_benefits_alternate_modeledsubmission);

    $gbam_to_save->save();
}

function import_ghg_benefits_measured($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'gbm'. $dateConst . $cur_count;

    $ghg_benefits_measured_submission = [];
    $ghg_benefits_measured_submission['type'] = 'ghg_benefits_measured';
    $ghg_benefits_measured_submission['name'] = $entry_name;
    $ghg_benefits_measured_submission['g_benefits_measured_field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'field_enrollment', 'name' => $in_data_array[2]]));
    $ghg_benefits_measured_submission['g_benefits_measured_fiscal_quarter'] = '';
    $ghg_benefits_measured_submission['g_benefits_measured_fiscal_year'] = '';
    $ghg_benefits_measured_submission['g_benefits_measured_ghg_measurement_method'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_measurement_method', 'name' => $in_data_array[5]]));
    $ghg_benefits_measured_submission['g_benefits_measured_ghg_measurement_method_other'] = $in_data_array[6];
    $ghg_benefits_measured_submission['g_benefits_measured_lab_name'] = $in_data_array[7];
    $ndate = convertExcelDate($in_data_array[8]);
    $ghg_benefits_measured_submission['g_benefits_measured_measurement_start_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $ndate = convertExcelDate($in_data_array[9]);
    $ghg_benefits_measured_submission['g_benefits_measured_measurement_end_date'] = \DateTime::createFromFormat(getExcelDateFormat(), $ndate)->getTimestamp();
    $ghg_benefits_measured_submission['g_benefits_measured_total_co2_reduction'] = $in_data_array[10];
    $ghg_benefits_measured_submission['g_benefits_measured_total_field_carbon_stock'] = $in_data_array[11];
    $ghg_benefits_measured_submission['g_benefits_measured_total_ch4_reduction'] = $in_data_array[12];
    $ghg_benefits_measured_submission['g_benefits_measured_total_n2o_reduction'] = $in_data_array[13];
    $ghg_benefits_measured_submission['g_benefits_measured_soil_sample_result'] = $in_data_array[14];
    $ghg_benefits_measured_submission['g_benefits_measured_soil_sample_result_unit'] = $in_data_array[15];
    $ghg_benefits_measured_submission['g_benefits_measured_soil_sample_result_unit_other'] = $in_data_array[16];
    $ghg_benefits_measured_submission['g_benefits_measured_measurement_type'] = $in_data_array[17];
    $ghg_benefits_measured_submission['g_benefits_measured_measurement_type_other'] =$in_data_array[18];
    
    $ps_to_save = Log::create($ghg_benefits_measured_submission);

    $ps_to_save->save();
}

function import_addl_envl_benefits($in_data_array, $cur_count){
    $dateConst = date('mdYhis', time());
    $entry_name = 'aeb'. $dateConst . $cur_count;

    $environmental_benefits_submission = [];
    $environmental_benefits_submission['type'] = 'environmental_benefits';
    $environmental_benefits_submission['name'] = $entry_name;
    $environmental_benefits_submission['fiscal_year'] = '';
    $environmental_benefits_submission['fiscal_quarter'] = '';
    $environmental_benefits_submission['field_id'] = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'field_enrollment', 'name' => $in_data_array[2]]));
    $environmental_benefits_submission['environmental_benefits'] = $in_data_array[5];
    $environmental_benefits_submission['nitrogen_loss'] = $in_data_array[6];
    $environmental_benefits_submission['nitrogen_loss_amount'] = $in_data_array[7];
    $environmental_benefits_submission['nitrogen_loss_amount_unit'] = $in_data_array[8];
    $environmental_benefits_submission['nitrogen_loss_amount_unit_other'] = $in_data_array[9];
    $environmental_benefits_submission['nitrogen_loss_purpose'] = $in_data_array[10];
    $environmental_benefits_submission['nitrogen_loss_purpose_other'] = $in_data_array[11];
    $environmental_benefits_submission['phosphorus_loss'] = $in_data_array[12];
    $environmental_benefits_submission['phosphorus_loss_amount'] = $in_data_array[13];
    $environmental_benefits_submission['phosphorus_loss_amount_unit'] = $in_data_array[14];
    $environmental_benefits_submission['phosphorus_loss_amount_unit_other'] = $in_data_array[15];
    $environmental_benefits_submission['phosphorus_loss_purpose'] = $in_data_array[16];
    $environmental_benefits_submission['phosphorus_loss_purpose_other'] = $in_data_array[17];
    $environmental_benefits_submission['other_water_quality'] = $in_data_array[18];
    $environmental_benefits_submission['other_water_quality_type'] = $in_data_array[19];
    $environmental_benefits_submission['other_water_quality_type_other'] = $in_data_array[20];
    $environmental_benefits_submission['other_water_quality_amount'] = $in_data_array[21];
    $environmental_benefits_submission['other_water_quality_amount_unit'] = $in_data_array[22];
    $environmental_benefits_submission['other_water_quality_amount_unit_other'] = $in_data_array[23];
    $environmental_benefits_submission['other_water_quality_purpose'] = $in_data_array[24];
    $environmental_benefits_submission['other_water_quality_purpose_other'] = $in_data_array[25];
    $environmental_benefits_submission['water_quality'] = $in_data_array[26];
    $environmental_benefits_submission['water_quality_amount'] = $in_data_array[27];
    $environmental_benefits_submission['water_quality_amount_unit'] = $in_data_array[28];
    $environmental_benefits_submission['water_quality_amount_unit_other'] = $in_data_array[29];
    $environmental_benefits_submission['water_quality_purpose'] = $in_data_array[30];
    $environmental_benefits_submission['water_quality_purpose_other'] = $in_data_array[31];
    $environmental_benefits_submission['reduced_erosion'] = $in_data_array[32];
    $environmental_benefits_submission['reduced_erosion_amount'] = $in_data_array[33];
    $environmental_benefits_submission['reduced_erosion_amount_unit'] = $in_data_array[34];
    $environmental_benefits_submission['reduced_erosion_amount_unit_other'] = $in_data_array[35];
    $environmental_benefits_submission['reduced_erosion_purpose'] = $in_data_array[36];
    $environmental_benefits_submission['reduced_erosion_purpose_other'] = $in_data_array[37];
    $environmental_benefits_submission['reduced_energy_use'] = $in_data_array[38];
    $environmental_benefits_submission['reduced_energy_use_amount'] = $in_data_array[39];
    $environmental_benefits_submission['reduced_energy_use_amount_unit'] = $in_data_array[40];
    $environmental_benefits_submission['reduced_energy_use_amount_unit_other'] = $in_data_array[41];
    $environmental_benefits_submission['reduced_energy_use_purpose'] = $in_data_array[42];
    $environmental_benefits_submission['reduced_energy_use_purpose_other'] = $in_data_array[43];
    $environmental_benefits_submission['avoided_land_conversion'] = $in_data_array[44];
    $environmental_benefits_submission['avoided_land_conversion_amount'] = $in_data_array[45];
    $environmental_benefits_submission['avoided_land_conversion_unit'] = $in_data_array[46];
    $environmental_benefits_submission['avoided_land_conversion_unit_other'] = $in_data_array[47];
    $environmental_benefits_submission['avoided_land_conversion_purpose'] = $in_data_array[48];
    $environmental_benefits_submission['avoided_land_conversion_purpose_other'] = $in_data_array[49];
    $environmental_benefits_submission['improved_wildlife_habitat'] = $in_data_array[50];
    $environmental_benefits_submission['improved_wildlife_habitat_amount'] = $in_data_array[51];
    $environmental_benefits_submission['improved_wildlife_habitat_unit'] = $in_data_array[52];
    $environmental_benefits_submission['improved_wildlife_habitat_amount_unit_other'] = $in_data_array[53];
    $environmental_benefits_submission['improved_wildlife_habitat_purpose'] = $in_data_array[54];
    $environmental_benefits_submission['improved_wildlife_habitat_purpose_other'] = $in_data_array[55];
    
    $ps_to_save = Log::create($environmental_benefits_submission);

    $ps_to_save->save();
}

function convertExcelDate($inDate){
    $unixTimestamp = ($inDate - 25569) * 86400;
    $date = date(getExcelDateFormat(), $unixTimestamp);

    return $date;
  }

function getExcelDateFormat(){
    return "Y-m-d";
}