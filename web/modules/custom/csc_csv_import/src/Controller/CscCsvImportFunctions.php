<?php
use Drupal\log\Entity\Log;
use Drupal\asset\Entity\Asset;
use Drupal\Core\Entity\EntityInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\Validator\ConstraintValidationInterface;
use Drupal\cig_pods_csc\Plugin\Validation\Constraint\RequiredConstraint;
use Drupal\cig_pods_csc\Plugin\Validation\Constraint\MinMaxConstraint;
use Drupal\cig_pods_csc\Plugin\Validation\Constraint\SumConstraint;


function is_custom_violation ($violation) {
    $constraints = Array(
        RequiredConstraint::class,
        MinMaxConstraint::class,
        SumConstraint::class,
    );
    
    return in_array(get_class($violation->getConstraint()), $constraints);
}

function decode_trim ($enc_value) {
    $value = base64_decode($enc_value);
    if ($value == NULL or $value == '') {
        return '';
    }
    return trim($value);
}

function find_entity ($entities, $properties) {
    foreach ($entities as $e) {
        $match_count = 0;
        foreach ($properties as $field => $val) {
            if (array_values($e['entity']->get($field)->getValue()[0])[0] == $val) {
                $match_count += 1;
            }
            else {
                break;
            }
        }
        if ($match_count == count($properties)) {
            return $e['entity'];
        }
    }
}

function import_coversheet($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    
    $in_data_array = array_map('decode_trim', $in_data_array);

    $coversheet_submission = [];
    $coversheet_submission['type'] = 'csc_project';
    $coversheet_submission['name'] = $in_data_array[0];
    $coversheet_submission['csc_project_id_field'] = $in_data_array[1];
    $coversheet_submission['csc_project_grantee_org'] = $in_data_array[2];
    $coversheet_submission['csc_project_grantee_cont_name'] = $in_data_array[3];
    $coversheet_submission['csc_project_grantee_cont_email'] = $in_data_array[4];
    $coversheet_submission['csc_project_year_reporting'] = $year;
    $coversheet_submission['csc_project_month_reporting'] = $quarter;

    $ndate = cscConvertExcelDate($in_data_array[7]);
    $coversheet_submission['csc_project_start'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();

    $ndate = cscConvertExcelDate($in_data_array[8]);
    $coversheet_submission['csc_project_end'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();

    $coversheet_submission['csc_project_budget'] = $in_data_array[9];

    foreach ($coversheet_submission as $key => $val) {
        if ($val === NULL) {
            $coversheet_submission[$key] = "";
        }
    }
    
    $compound_key = ["csc_project_id_field"];
    $new_entity = update_or_insert('asset', $coversheet_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['asset', $coversheet_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_project_summary($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'ps'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $project_summary_submission = [];
    $project_summary_submission['type'] = 'csc_project_summary';
    $project_summary_submission['name'] = $entry_name;

    $project_summary_submission['csc_p_summary_csc_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $project_summary_submission['csc_p_summary_fiscal_year'] = $year;
    $project_summary_submission['csc_p_summary_fiscal_quarter'] = $quarter;
    $project_summary_submission['csc_p_summary_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $in_data_array[0]]));
    $project_summary_submission['csc_p_summary_commodity_sales'] = filter_var(strtolower($in_data_array[1]), FILTER_VALIDATE_BOOLEAN);
    $project_summary_submission['csc_p_summary_farms_enrolled'] = filter_var($in_data_array[2], FILTER_VALIDATE_BOOLEAN);
    $project_summary_submission['csc_p_summ_ghg_calculation_mthds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_calculation_methods', 'name' => $in_data_array[3]]));
    $project_summary_submission['csc_p_summ_ghg_cum_calculation'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_cumulative_calculation', 'name' => $in_data_array[4]]));
    $project_summary_submission['csc_p_summary_ghg_benefits'] = $in_data_array[5];
    $project_summary_submission['csc_p_summ_cum_carbon_stack'] = $in_data_array[6];
    $project_summary_submission['csc_p_summ_cum_co2_benefit'] = $in_data_array[7];
    $project_summary_submission['csc_p_summ_cum_ch4_benefit'] = $in_data_array[8];
    $project_summary_submission['csc_p_summ_cum_n2o_benefit'] = $in_data_array[9];
    $project_summary_submission['csc_p_summary_offsets_produced'] = $in_data_array[10];
    $project_summary_submission['csc_p_summary_offsets_sale'] = $in_data_array[11];
    $project_summary_submission['csc_p_summary_offsets_price'] = $in_data_array[12];
    $project_summary_submission['csc_p_summary_insets_produced'] = $in_data_array[13];
    $project_summary_submission['csc_p_summary_cost_on_farm'] = $in_data_array[14];
    $project_summary_submission['csc_p_summary_mmrv_cost'] = $in_data_array[15];
    $project_summary_submission['csc_p_summ_ghg_monitoring_mthd'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_monitoring_method', 'name' => $in_data_array[16]]));
    $project_summary_submission['csc_p_summ_ghg_reporting_mthd'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_reporting_method', 'name' => $in_data_array[22]]));
    $project_summary_submission['csc_p_summ_ghg_verification_mthd'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_verification_method', 'name' => $in_data_array[28]]));

    foreach ($project_summary_submission as $key => $val) {
        if ($val === NULL) {
            $project_summary_submission[$key] = "";
        }
    }

    $compound_key = ["csc_p_summary_csc_project_id", "csc_p_summary_fiscal_year", "csc_p_summary_fiscal_quarter", "csc_p_summary_commodity_type"];
    $new_entity = update_or_insert('asset', $project_summary_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['asset', $project_summary_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_partner_activities($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'pa'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $partner_activities_submission = [];
    $partner_activities_submission['type'] = 'csc_partner_activities';
    $partner_activities_submission['name'] = $entry_name;

    $partner_activities_submission['csc_prtnr_act_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $partner_activities_submission['csc_prtnr_act_partner_ein'] = $in_data_array[0];
    $partner_activities_submission['csc_prtnr_act_partner_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'partner_type', 'name' => $in_data_array[2]]));
    $partner_activities_submission['csc_prtnr_act_partner_poc'] = $in_data_array[3];
    $partner_activities_submission['csc_prtnr_act_partner_poc_email'] = $in_data_array[4];

    $ndate = cscConvertExcelDate($in_data_array[5]);
    $partner_activities_submission['csc_prtnr_act_partnership_start'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    
    $ndate = cscConvertExcelDate($in_data_array[6]);
    $partner_activities_submission['csc_prtnr_act_partnership_end'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();

    $partner_activities_submission['csc_prtnr_act_partnership_initation'] = filter_var($in_data_array[7], FILTER_VALIDATE_BOOLEAN);
    $partner_activities_submission['csc_prtnr_act_partner_total_requested'] = $in_data_array[8];
    $partner_activities_submission['csc_prtnr_act_total_match_contrib'] = $in_data_array[9];
    $partner_activities_submission['csc_prtnr_act_total_match_incent'] = $in_data_array[10];
    $partner_activities_submission['csc_prtnr_act_match_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $in_data_array[11]]));
    $partner_activities_submission['csc_prtnr_act_match_amount_1'] = $in_data_array[12];
    $partner_activities_submission['csc_prtnr_act_match_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $in_data_array[13]]));
    $partner_activities_submission['csc_prtnr_act_match_amount_2'] = $in_data_array[14];
    $partner_activities_submission['csc_prtnr_act_match_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'match_type', 'name' => $in_data_array[15]]));
    $partner_activities_submission['csc_prtnr_act_match_amount_3'] = $in_data_array[16];
    $partner_activities_submission['csc_prtnr_act_match_type_other'] = $in_data_array[17];

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
    $partner_activities_submission['csc_prtnr_act_training_provided'] = $training_provided_results;

    $partner_activities_submission['csc_prtnr_act_training_other'] = $in_data_array[21];
    $partner_activities_submission['csc_partner_activity_activity1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $in_data_array[22]]));
    $partner_activities_submission['csc_prtnr_act_activity1_cost'] = $in_data_array[23];
    $partner_activities_submission['csc_partner_activity_activity2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $in_data_array[24]]));
    $partner_activities_submission['csc_prtnr_act_activity2_cost'] = $in_data_array[25];
    $partner_activities_submission['csc_partner_activity_activity3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'activity_by_partner', 'name' => $in_data_array[26]]));
    $partner_activities_submission['csc_prtnr_act_activity3_cost'] = $in_data_array[27];
    $partner_activities_submission['csc_prtnr_act_activity_other'] = $in_data_array[28];
    $partner_activities_submission['csc_prtnr_act_products_supplied'] = $in_data_array[29];
    $partner_activities_submission['csc_prtnr_act_product_source'] = $in_data_array[30];
    
    foreach ($coversheet_submission as $key => $val) {
        if ($val === NULL) {
            $coversheet_submission[$key] = "";
        }
    }
     
    $compound_key = ['csc_prtnr_act_project_id', 'csc_prtnr_act_partner_ein'];
    $new_entity = update_or_insert('asset', $partner_activities_submission, []);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['asset', $partner_activities_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_market_activities($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'ma'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $market_activities_submission = [];
    $market_activities_submission['type'] = 'csc_market_activities';
    $market_activities_submission['name'] = $entry_name;
    $market_activities_submission['csc_m_activities_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $market_activities_submission['csc_m_activities_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $in_data_array[0]]));
    $market_activities_submission['csc_m_act_mktng_chnl_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_type', 'name' => $in_data_array[1]]));
    $market_activities_submission['csc_m_act_mktng_chnl_type_otr'] = $in_data_array[2];
    $market_activities_submission['csc_m_act_number_of_buyers'] = $in_data_array[3];

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
    $market_activities_submission['csc_m_activities_buyer_names'] = array_map('trim', explode('|', $buyer_names));

    $market_activities_submission['csc_m_act_mktng_chnl_geography'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'marketing_channel_geography', 'name' => $in_data_array[5]]));
    $market_activities_submission['csc_m_activities_value_sold'] = $in_data_array[6];
    $market_activities_submission['csc_m_activities_volume_sold'] = $in_data_array[7];
    $market_activities_submission['csc_m_act_volume_sold_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'volume_sold_unit', 'name' => $in_data_array[8]]));
    $market_activities_submission['csc_m_act_volume_unit_otr'] = $in_data_array[9];
    $market_activities_submission['csc_m_activities_price_premium'] = $in_data_array[10];
    $market_activities_submission['csc_m_act_price_premium_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'price_premium_unit', 'name' => $in_data_array[11]]));
    $market_activities_submission['csc_m_act_price_premium_unit_otr'] = $in_data_array[12];
    $market_activities_submission['csc_m_act_price_premium_to_prod'] = $in_data_array[13];

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
    $market_activities_submission['csc_m_act_product_diff_mthd'] = $product_differentiation_method_results;

    $market_activities_submission['csc_m_act_product_diff_mthd_otr'] = $in_data_array[17];

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
    $market_activities_submission['csc_m_act_mktng_mthd'] = $marketing_method_results;

    $market_activities_submission['csc_m_act_mktng_mthd_otr'] = $in_data_array[21];

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
    $market_activities_submission['csc_m_act_mktng_chnl_id'] = $marketing_channel_identification_results;

    $market_activities_submission['csc_m_act_mktng_chnl_id_mthd_otr'] = $in_data_array[25];

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
    $market_activities_submission['csc_m_act_traceability_mthd'] = $traceability_method_results;

    $market_activities_submission['csc_m_act_traceability_mthd_otr'] = $in_data_array[29];

    foreach ($market_activities_submission as $key => $val) {
        if ($val === NULL) {
            $market_activities_submission[$key] = "";
        }
    }
    

    $compound_key = ['csc_m_activities_project_id', 'csc_m_activities_commodity_type', 'csc_m_act_mktng_chnl_type'];
    $new_entity = update_or_insert('log', $market_activities_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $market_activities_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_producer_enrollment($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'pe'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $producer_enrollment_submission = [];
    $producer_enrollment_submission['type'] = 'csc_producer_enrollment';
    $producer_enrollment_submission['name'] = $in_data_array[5];
    $producer_enrollment_submission['csc_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);

    $producer_enrollment_submission['csc_p_enrollment_farm_id'] = $in_data_array[0];
    $producer_enrollment_submission['csc_p_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' =>  $in_data_array[1]]));
    $producer_enrollment_submission['csc_p_enrollment_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' =>  $in_data_array[2]]));

    $ndate = cscConvertExcelDate($in_data_array[4]);
    $producer_enrollment_submission['csc_p_enrollment_start_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $producer_enrollment_submission['csc_p_enrlmnt_underserved_status'] = $in_data_array[6];
    $producer_enrollment_submission['csc_p_enrollment_total_area'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'total_area', 'name' => $in_data_array[7]]));
    $producer_enrollment_submission['csc_p_enrlmnt_total_crop_area'] = $in_data_array[8];
    $producer_enrollment_submission['csc_p_enrlmnt_total_livstk_area'] = $in_data_array[9];
    $producer_enrollment_submission['csc_p_enrlmnt_total_forest_area'] = $in_data_array[10];
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $in_data_array[11]]));
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_1_cnt'] = $in_data_array[12];
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $in_data_array[13]]));
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_2_cnt'] = $in_data_array[14];
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'livestock_type', 'name' => $in_data_array[15]]));
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_3_cnt'] = $in_data_array[16];
    $producer_enrollment_submission['csc_p_enrlmnt_livstk_type_otr'] = $in_data_array[17];
    $producer_enrollment_submission['csc_p_enrollment_organic_farm'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_farm', 'name' => $in_data_array[18]]));
    $producer_enrollment_submission['csc_p_enrollment_organic_fields'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'organic_fields', 'name' => $in_data_array[19]]));
    $producer_enrollment_submission['csc_p_enrlmnt_prod_motivation'] = $in_data_array[20];

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
    $producer_enrollment_submission['csc_p_enrlmnt_prod_outreach'] = $producer_outreach_results;
    $producer_enrollment_submission['csc_p_enrlmnt_prod_outreach_otr'] = $in_data_array[24];
    $producer_enrollment_submission['csc_p_enrlmnt_csaf_experience'] =array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_experience', 'name' => $in_data_array[25]]));
    $producer_enrollment_submission['csc_p_enrlmnt_csaf_federal_fds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_federal_funds', 'name' => $in_data_array[26]]));
    $producer_enrollment_submission['csc_p_enrlmnt_csaf_st_local_fds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_state_or_local_funds', 'name' => $in_data_array[27]]));
    $producer_enrollment_submission['csc_p_enrlmnt_csaf_nonprofit_fds'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_nonprofit_funds', 'name' => $in_data_array[28]]));
    $producer_enrollment_submission['csc_p_enrlmnt_csaf_market_incent'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'csaf_market_incentives', 'name' => $in_data_array[29]]));
    
    foreach ($producer_enrollment_submission as $key => $val) {
        if ($val === NULL) {
            $producer_enrollment_submission[$key] = "";
        }
    }
    
    $compound_key = ['csc_project_id', 'name'];
    $new_entity = update_or_insert('asset', $producer_enrollment_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['asset', $producer_enrollment_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];

}

function import_field_enrollment($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fe'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_enrollment_submission = [];
    $field_enrollment_submission['type'] = 'csc_field_enrollment';
    $field_enrollment_submission['name'] = $entry_name;

    $field_enrollment_submission['csc_f_enrollment_producer_id'] = find_entity($new_entities, ['type' => 'csc_producer_enrollment', 'csc_p_enrollment_farm_id' => $in_data_array[0]]);
    $field_enrollment_submission['csc_f_enrollment_tract_id'] = $in_data_array[1];
    $field_enrollment_submission['csc_f_enrollment_field_id'] = $in_data_array[2];
    $field_enrollment_submission['csc_f_enrollment_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $in_data_array[3]]));
    $field_enrollment_submission['csc_f_enrollment_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' => $in_data_array[4]]));
    $field_enrollment_submission['csc_f_enrollment_prior_field_id'] = $in_data_array[5];

    $ndate = cscConvertExcelDate($in_data_array[7]);
    $field_enrollment_submission['csc_f_enrollment_start_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $field_enrollment_submission['csc_f_nrlmnt_total_field_area'] = $in_data_array[8];
    $field_enrollment_submission['csc_f_nrlmnt_commodity_category'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_category', 'name' => $in_data_array[9]]));
    $field_enrollment_submission['csc_f_enrollment_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $in_data_array[10]]));
    $field_enrollment_submission['csc_f_enrollment_baseline_yield'] = $in_data_array[11];
    $field_enrollment_submission['csc_f_nrlmnt_base_yield_unit'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'baseline_yield_unit', 'name' => $in_data_array[12]]));
    $field_enrollment_submission['csc_f_nrlmnt_base_yield_unit_otr'] = $in_data_array[13];
    $field_enrollment_submission['csc_f_nrlmnt_base_yield_loc'] = $in_data_array[14];
    $field_enrollment_submission['csc_f_nrlmnt_base_yield_loc_otr'] = $in_data_array[15];
    $field_enrollment_submission['csc_f_enrollment_field_land_use'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_land_use', 'name' => $in_data_array[16]]));
    $field_enrollment_submission['csc_f_nrlmnt_field_irrigated'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_irrigated', 'name' => $in_data_array[17]]));
    $field_enrollment_submission['csc_f_enrollment_field_tillage'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_tillage', 'name' => $in_data_array[18]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_pri_util_prcnt'] = $in_data_array[19];
    $field_enrollment_submission['csc_f_nrlmnt_field_any_csaf_prac'] = $in_data_array[20];
    $field_enrollment_submission['csc_f_nrlmnt_field_prac_pri_util'] = $in_data_array[21];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_1'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[22]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_1'] = $in_data_array[23];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_1'] = $in_data_array[24];
    $field_enrollment_submission['csc_f_enrlmnt_prac_year_1'] = $in_data_array[25];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_1'] = $in_data_array[26];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_1'] = $in_data_array[27];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_1'] = $in_data_array[28];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_2'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[29]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_2'] = $in_data_array[30];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_2'] = $in_data_array[31];
    $field_enrollment_submission['csc_f_nrlmnt_prac_year_2'] = $in_data_array[32];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_2'] = $in_data_array[33];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_2'] = $in_data_array[34];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_2'] = $in_data_array[35];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_3'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[36]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_3'] = $in_data_array[37];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_3'] = $in_data_array[38];
    $field_enrollment_submission['csc_f_nrlmnt_prac_year_3'] = $in_data_array[39];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_3'] = $in_data_array[40];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_3'] = $in_data_array[41];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_3'] = $in_data_array[42];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_4'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[43]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_4'] = $in_data_array[44];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_4'] = $in_data_array[45];
    $field_enrollment_submission['csc_f_nrlmnt_prac_year_4'] = $in_data_array[46];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_4'] = $in_data_array[47];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_4'] = $in_data_array[48];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_4'] = $in_data_array[49];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_5'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[50]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_5'] = $in_data_array[51];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_5'] = $in_data_array[52];
    $field_enrollment_submission['csc_f_nrlmnt_prac_year_5'] = $in_data_array[53];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_5'] = $in_data_array[54];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_5'] = $in_data_array[55];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_5'] = $in_data_array[56];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_6'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[57]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_6'] = $in_data_array[58];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_6'] = $in_data_array[59];
    $field_enrollment_submission['csc_f_nrlmnt_prac_year_6'] = $in_data_array[60];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_6'] = $in_data_array[61];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_6'] = $in_data_array[62];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_6'] = $in_data_array[63];
    $field_enrollment_submission['csc_f_nrlmnt_prac_type_7'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'practice_type', 'name' => $in_data_array[64]]));
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_7'] = $in_data_array[65];
    $field_enrollment_submission['csc_f_nrlmnt_prac_std_otr_7'] = $in_data_array[66];
    $field_enrollment_submission['csc_f_nrlmnt_prac_year_7'] = $in_data_array[67];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_7'] = $in_data_array[68];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_7'] = $in_data_array[69];
    $field_enrollment_submission['csc_f_nrlmnt_prac_ext_unit_otr_7'] = $in_data_array[70];
    
    foreach ($field_enrollment_submission as $key => $val) {
        if ($val === NULL) {
            $field_enrollment_submission[$key] = "";
        }
    }
    
    $compound_key = ['csc_f_enrollment_producer_id', 'csc_f_enrollment_state', 'csc_f_enrollment_county', 'csc_f_enrollment_tract_id', 'csc_f_enrollment_field_id', 'csc_f_enrollment_prior_field_id'];
    $new_entity = update_or_insert('asset', $field_enrollment_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['asset', $field_enrollment_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_farm_summary($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fa'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $farm_summary_submission = [];
    $farm_summary_submission['type'] = 'csc_farm_summary';
    $farm_summary_submission['name'] = $entry_name;
    $farm_summary_submission['csc_farm_summary_fiscal_year'] = $year;
    $farm_summary_submission['csc_farm_summary_fiscal_quarter'] = $quarter;
    $farm_summary_submission['csc_farm_summary_state'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'state', 'name' => $in_data_array[1]]));
    $farm_summary_submission['csc_farm_summary_county'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'county', 'name' => $in_data_array[2]]));
    $farm_summary_submission['csc_farm_summary_producer_id'] = find_entity($new_entities, ['type' => 'csc_producer_enrollment', 'csc_p_enrollment_farm_id' => $in_data_array[0]]);
    
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
    $farm_summary_submission['csc_fa_summ_prdcr_ta_rcvd'] = $producer_ta_received_results;
    $farm_summary_submission['csc_fa_summ_prdcr_ta_rcvd_otr'] = $in_data_array[6];
    $farm_summary_submission['csc_fa_summ_prdcr_inc_amt'] = $in_data_array[7];

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
    $farm_summary_submission['csc_fa_summ_inc_reason'] = $incentive_reason_results;
    $farm_summary_submission['csc_fa_summ_inc_reason_otr'] = $in_data_array[12];
 
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
    $farm_summary_submission['csc_fa_summ_inc_structure'] = $incentive_structure_results;
    $farm_summary_submission['csc_fa_summ_inc_structure_otr'] = $in_data_array[17];

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
    $farm_summary_submission['csc_farm_summary_incentive_type'] = $incentive_type_results;
    $farm_summary_submission['csc_fa_summ_inc_type_otr'] = $in_data_array[22];
    $farm_summary_submission['csc_fa_summ_pay_on_enrollment'] = $in_data_array[23];
    $farm_summary_submission['csc_fa_summ_pay_on_impl'] = $in_data_array[24];
    $farm_summary_submission['csc_fa_summ_pay_on_harvest'] = $in_data_array[25];
    $farm_summary_submission['csc_fa_summ_pay_on_mmrv'] = $in_data_array[26];
    $farm_summary_submission['csc_fa_summ_pay_on_sale'] = $in_data_array[27];
    
    foreach ($farm_summary_submission as $key => $val) {
        if ($val === NULL) {
            $farm_summary_submission[$key] = "";
        }
    }
    
    $compound_key = ['csc_farm_summary_producer_id', 'csc_farm_summary_fiscal_year', 'csc_farm_summary_fiscal_quarter', 'csc_farm_summary_state', 'csc_farm_summary_county'];
    $new_entity = update_or_insert('log', $farm_summary_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $farm_summary_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];

}

function import_field_summary($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fs'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_summary_submission = [];
    $field_summary_submission['type'] = 'csc_field_summary';
    $field_summary_submission['name'] = $entry_name;
    $field_summary_submission['status'] = '';
    $field_summary_submission['flag'] = '';
    $field_summary_submission['notes'] = '';

    $field_summary_submission['csc_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $field_summary_submission['csc_f_summary_field_id'] = find_entity($new_entities, ['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]);
    $field_summary_submission['csc_f_summary_fiscal_year'] = $year;
    $field_summary_submission['csc_f_summary_fiscal_quarter'] = $quarter;
    $ndate = cscConvertExcelDate($in_data_array[14]);
    $field_summary_submission['csc_f_summary_contract_end_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $field_summary_submission['csc_fi_summ_impl_cost_coverage'] = $in_data_array[25];
    $field_summary_submission['csc_fi_summ_impl_cost'] = $in_data_array[22];
    $field_summary_submission['csc_fi_summ_impl_cost_ut'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'cost_unit', 'name' => $in_data_array[23]]));
    
    $ndate = cscConvertExcelDate($in_data_array[13]);
    $field_summary_submission['csc_fi_summ_date_pract_complete'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $field_summary_submission['csc_fi_summ_fld_comm_value'] = $in_data_array[18];
    $field_summary_submission['csc_fi_summ_fld_comm_vol'] = $in_data_array[19];
    $field_summary_submission['csc_fi_summ_fld_comm_vol_ut'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'field_commodity_volume_unit', 'name' => $in_data_array[20]]));
    $field_summary_submission['csc_fi_summ_fld_ghg_calc'] = $in_data_array[38];

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

    $field_summary_submission['csc_fi_summ_fld_ghg_monitor'] = $summary_field_ghg_monitoring_results;

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

    $field_summary_submission['csc_fi_summ_fld_ghg_report'] = $summary_field_ghg_reporting_results;

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

    $field_summary_submission['csc_fi_summ_fld_ghg_verifi'] = $summary_field_ghg_verification_results;
    $field_summary_submission['csc_f_summary_field_insets'] = $in_data_array[46];
    $field_summary_submission['csc_fi_summ_fld_co2_stock'] = $in_data_array[41];
    $field_summary_submission['csc_fi_summ_fld_ch4_emission_rd'] = $in_data_array[43];
    $field_summary_submission['csc_fi_summ_fld_co2_emission_rd'] = $in_data_array[42];
    $field_summary_submission['csc_fi_summ_fld_ghg_emission_rd'] = $in_data_array[40];
    $field_summary_submission['csc_fi_summ_fld_ofc_ghg_calc'] = $in_data_array[39];
    $field_summary_submission['csc_fi_summ_fld_n2o_emission_rd'] = $in_data_array[44];
    $field_summary_submission['csc_f_summary_field_offsets'] = $in_data_array[45];
    $field_summary_submission['csc_f_summary_commodity_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'commodity_term', 'name' => $in_data_array[5]]));
    $field_summary_submission['csc_fi_summ_inc_per_acre_or_hd'] = $in_data_array[17];
    $field_summary_submission['csc_fi_summ_marketing_ast_prv'] = $in_data_array[16];
    $field_summary_submission['csc_fi_summ_mmrv_ast_prv'] = $in_data_array[15];
    $field_summary_submission['csc_fi_summ_impl_cost_ut_otr'] = $in_data_array[24];
    $field_summary_submission['csc_fi_summ_fld_comm_vol_ut_otr'] = $in_data_array[21];
    $field_summary_submission['csc_fi_summ_fld_ghg_monitor_otr'] = $in_data_array[29];
    $field_summary_submission['csc_fi_summ_fld_ghg_report_otr'] = $in_data_array[33];
    $field_summary_submission['csc_fi_summ_fld_ghg_verifi_otr'] = $in_data_array[37];
    $field_summary_submission['csc_fi_summ_fld_measurement_otr'] = $in_data_array[47];

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
   
    $field_summary_submission['csc_f_summary_practice_type'] = $summary_practice_type_results;

    foreach ($field_summary_submission as $key => $val) {
        if ($val === NULL) {
            $field_summary_submission[$key] = "";
        }
    }


    $compound_key = ['csc_f_summary_field_id', 'csc_f_summary_commodity_type'];
    $new_entity = update_or_insert('log', $field_summary_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $field_summary_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_ghg_benefits_alt_models($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'gbam'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $g_benefits_alternate_modeled_submission = [];
    $g_benefits_alternate_modeled_submission['name'] = $entry_name;
    $g_benefits_alternate_modeled_submission['type'] = 'csc_ghg_benefits_alt_modeled';
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_fiscal_year'] = $year;
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_fiscal_quart'] = $quarter;
    $g_benefits_alternate_modeled_submission['csc_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_fld_id'] = find_entity($new_entities, ['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]);

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
    
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_comm_type'] = $g_benefits_alternate_modeled_commodity_type_results;

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

    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_pract_type'] = $g_benefits_alternate_modeled_practice_type_results;
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_ghg_md'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_model', 'name' => $in_data_array[18]]));
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_ghg_md_otr'] = $in_data_array[19];
    $ndate = cscConvertExcelDate($in_data_array[20]);
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_md_start_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $ndate = cscConvertExcelDate($in_data_array[21]);
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_md_end_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_ghg_bene_est'] = $in_data_array[22];
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_co2_stock_est'] = $in_data_array[23];
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_co2_est'] = $in_data_array[24];
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_ch4_est'] = $in_data_array[25];
    $g_benefits_alternate_modeled_submission['csc_g_bene_alt_md_n2o_est'] = $in_data_array[26];

    foreach ($g_benefits_alternate_modeled_submission as $key => $val) {
        if ($val === NULL) {
            $g_benefits_alternate_modeled_submission[$key] = "";
        }
    }


    $compound_key = ['csc_g_bene_alt_md_fld_id', 'csc_g_bene_alt_md_fiscal_quart', 'csc_g_bene_alt_md_fiscal_year'];
    $new_entity = update_or_insert('log', $g_benefits_alternate_modeled_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $g_benefits_alternate_modeled_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_ghg_benefits_measured($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'gbm'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $ghg_benefits_measured_submission = [];
    $ghg_benefits_measured_submission['type'] = 'csc_ghg_benefits_measured';
    $ghg_benefits_measured_submission['name'] = $entry_name;
    $ghg_benefits_measured_submission['csc_g_bene_msrd_fld_id'] = find_entity($new_entities, ['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]);
    $ghg_benefits_measured_submission['csc_g_bene_msrd_fiscal_quarter'] = $quarter;
    $ghg_benefits_measured_submission['csc_g_bene_msrd_fiscal_year'] = $year;
    $ghg_benefits_measured_submission['csc_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $ghg_benefits_measured_submission['csc_g_bene_msrd_ghg_msrt_mt'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'ghg_measurement_method', 'name' => $in_data_array[5]]));
    $ghg_benefits_measured_submission['csc_g_bene_msrd_ghg_msrt_mt_otr'] = $in_data_array[6];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_lab_name'] = $in_data_array[7];
    $ndate = cscConvertExcelDate($in_data_array[8]);
    $ghg_benefits_measured_submission['csc_g_bene_msrd_msrt_start_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $ndate = cscConvertExcelDate($in_data_array[9]);
    $ghg_benefits_measured_submission['csc_g_bene_msrd_msrt_end_date'] = \DateTime::createFromFormat(cscGetExcelDateFormat(), $ndate)->getTimestamp();
    $ghg_benefits_measured_submission['csc_g_bene_msrd_total_co2_rd'] = $in_data_array[10];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_t_fld_co2_stock'] = $in_data_array[11];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_total_ch4_rd'] = $in_data_array[12];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_total_n2o_rd'] = $in_data_array[13];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_sl_sp_rs'] = $in_data_array[14];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_sl_sp_rs_ut'] = $in_data_array[15];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_sl_sp_rs_ut_otr'] = $in_data_array[16];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_msrt_type'] = $in_data_array[17];
    $ghg_benefits_measured_submission['csc_g_bene_msrd_msrt_type_otr'] =$in_data_array[18];
    
    foreach ($ghg_benefits_measured_submission as $key => $val) {
        if ($val === NULL) {
            $ghg_benefits_measured_submission[$key] = "";
        }
    }

    $compound_key = ["csc_g_bene_msrd_fld_id", "csc_g_bene_msrd_fiscal_quarter", "csc_g_bene_msrd_fiscal_year"];
    $new_entity = update_or_insert('log', $ghg_benefits_measured_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $ghg_benefits_measured_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_addl_envl_benefits($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];
    $new_entities = $data['new_entities'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'aeb'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $environmental_benefits_submission = [];
    $environmental_benefits_submission['type'] = 'csc_environmental_benefits';
    $environmental_benefits_submission['name'] = $entry_name;
    $environmental_benefits_submission['csc_fiscal_year'] = $year;
    $environmental_benefits_submission['csc_fiscal_quarter'] = $quarter;
    $environmental_benefits_submission['csc_project_id'] = find_entity($new_entities, ['type' => 'csc_project', 'csc_project_id_field' => $project_id_field]);
    $environmental_benefits_submission['csc_field_id'] = find_entity($new_entities, ['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]);
    $environmental_benefits_submission['csc_environmental_benefits'] = $in_data_array[5];
    $environmental_benefits_submission['csc_nitrogen_loss'] = $in_data_array[6];
    $environmental_benefits_submission['csc_nitrogen_loss_amount'] = $in_data_array[7];
    $environmental_benefits_submission['csc_nitrogen_loss_amount_unit'] = $in_data_array[8];
    $environmental_benefits_submission['csc_nitrogen_loss_amt_ut_otr'] = $in_data_array[9];
    $environmental_benefits_submission['csc_nitrogen_loss_purpose'] = $in_data_array[10];
    $environmental_benefits_submission['csc_nitrogen_loss_purpose_other'] = $in_data_array[11];
    $environmental_benefits_submission['csc_phosphorus_loss'] = $in_data_array[12];
    $environmental_benefits_submission['csc_phosphorus_loss_amount'] = $in_data_array[13];
    $environmental_benefits_submission['csc_phosphorus_loss_amount_unit'] = $in_data_array[14];
    $environmental_benefits_submission['csc_phosphorus_loss_amt_ut_otr'] = $in_data_array[15];
    $environmental_benefits_submission['csc_phosphorus_loss_purpose'] = $in_data_array[16];
    $environmental_benefits_submission['csc_phosphorus_loss_purpose_otr'] = $in_data_array[17];
    $environmental_benefits_submission['csc_other_water_quality'] = $in_data_array[18];
    $environmental_benefits_submission['csc_other_water_quality_type'] = $in_data_array[19];
    $environmental_benefits_submission['csc_otr_water_quality_type_otr'] = $in_data_array[20];
    $environmental_benefits_submission['csc_other_water_quality_amount'] = $in_data_array[21];
    $environmental_benefits_submission['csc_otr_water_quality_amt_ut'] = $in_data_array[22];
    $environmental_benefits_submission['csc_otr_h2o_quality_amt_ut_otr'] = $in_data_array[23];
    $environmental_benefits_submission['csc_other_water_quality_purpose'] = $in_data_array[24];
    $environmental_benefits_submission['csc_otr_h2o_quality_purpose_otr'] = $in_data_array[25];
    $environmental_benefits_submission['csc_water_quantity'] = $in_data_array[26];
    $environmental_benefits_submission['csc_water_quantity_amount'] = $in_data_array[27];
    $environmental_benefits_submission['csc_water_quantity_amount_unit'] = $in_data_array[28];
    $environmental_benefits_submission['csc_water_quantity_amt_ut_otr'] = $in_data_array[29];
    $environmental_benefits_submission['csc_water_quantity_purpose'] = $in_data_array[30];
    $environmental_benefits_submission['csc_water_quantity_purpose_otr'] = $in_data_array[31];
    $environmental_benefits_submission['csc_reduced_erosion'] = $in_data_array[32];
    $environmental_benefits_submission['csc_reduced_erosion_amount'] = $in_data_array[33];
    $environmental_benefits_submission['csc_reduced_erosion_amount_unit'] = $in_data_array[34];
    $environmental_benefits_submission['csc_reduced_erosion_amt_ut_otr'] = $in_data_array[35];
    $environmental_benefits_submission['csc_reduced_erosion_purpose'] = $in_data_array[36];
    $environmental_benefits_submission['csc_reduced_erosion_purpose_otr'] = $in_data_array[37];
    $environmental_benefits_submission['csc_reduced_energy_use'] = $in_data_array[38];
    $environmental_benefits_submission['csc_reduced_energy_use_amount'] = $in_data_array[39];
    $environmental_benefits_submission['csc_reduced_energy_use_amt_ut'] = $in_data_array[40];
    $environmental_benefits_submission['csc_red_energy_use_amt_ut_otr'] = $in_data_array[41];
    $environmental_benefits_submission['csc_reduced_energy_use_purpose'] = $in_data_array[42];
    $environmental_benefits_submission['csc_red_energy_use_purpose_otr'] = $in_data_array[43];
    $environmental_benefits_submission['csc_avoided_land_conversion'] = $in_data_array[44];
    $environmental_benefits_submission['csc_avoided_land_conversion_amt'] = $in_data_array[45];
    $environmental_benefits_submission['csc_avoided_land_conversion_ut'] = $in_data_array[46];
    $environmental_benefits_submission['csc_avo_land_conversion_ut_otr'] = $in_data_array[47];
    $environmental_benefits_submission['csc_avo_land_conversion_purpose'] = $in_data_array[48];
    $environmental_benefits_submission['csc_avo_land_conv_purpose_otr'] = $in_data_array[49];
    $environmental_benefits_submission['csc_improved_wildlife_habitat'] = $in_data_array[50];
    $environmental_benefits_submission['csc_imp_wildlife_habitat_amt'] = $in_data_array[51];
    $environmental_benefits_submission['csc_imp_wildlife_habitat_ut'] = $in_data_array[52];
    $environmental_benefits_submission['csc_imp_wld_habitat_amt_ut_otr'] = $in_data_array[53];
    $environmental_benefits_submission['csc_imp_wld_habitat_purpose'] = $in_data_array[54];
    $environmental_benefits_submission['csc_imp_wld_habitat_purpose_otr'] = $in_data_array[55];
    
    foreach ($environmental_benefits_submission as $key => $val) {
        if ($val === NULL) {
            $environmental_benefits_submission[$key] = "";
        }
    }

    $compound_key = ["csc_field_id", "csc_fiscal_year", "csc_fiscal_quarter"];
    $new_entity = update_or_insert('log', $environmental_benefits_submission, $compound_key);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $environmental_benefits_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_alley_cropping($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'ac'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_alley_cropping';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p311_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p311_species_density'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_combustion_system_improvement($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'csi'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_combustion_sys_improvement';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p372_prior_fuel_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'fuel_type', 'name' => $in_data_array[5]]));
    $supplemental_data_submission['csc_p372_prior_fuel_type_other'] = $in_data_array[6];
    $supplemental_data_submission['csc_p372_prior_fuel_amount'] = $in_data_array[7];
    $supplemental_data_submission['csc_p372_prior_fuel_amount_unit'] = $in_data_array[8];
    $supplemental_data_submission['csc_p372_pri_fuel_amnt_unit_otr'] = $in_data_array[9];
    $supplemental_data_submission['csc_p372_fuel_type_after'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'fuel_type', 'name' => $in_data_array[10]]));
    $supplemental_data_submission['csc_p372_fuel_type_after_other'] = $in_data_array[11];
    $supplemental_data_submission['csc_p372_fuel_amount_after'] = $in_data_array[12];
    $supplemental_data_submission['csc_p372_fuel_amount_unit_after'] = $in_data_array[13];
    $supplemental_data_submission['csc_p372_fuel_amnt_unit_aft_otr'] = $in_data_array[14];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_conservation_cover($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'cc'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_conservation_cover';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p327_species_category'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_conservation_crop_rotation($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'ccr'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_conservation_crop_rotation';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p328_conservation_crop_type'] = $in_data_array[5];
    $supplemental_data_submission['csc_p328_change_implemented'] = $in_data_array[6];
    $supplemental_data_submission['csc_p328_rotation_tillage_type'] = $in_data_array[7];
    $supplemental_data_submission['csc_p328_rotation_till_type_otr'] = $in_data_array[8];
    $supplemental_data_submission['csc_p328_total_rotation_length'] = $in_data_array[9];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_contour_buffer_strips($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'cbs'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_contour_buffer_strips';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p332_strip_width'] = $in_data_array[5];
    $supplemental_data_submission['csc_p332_species_category'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_cover_crop($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'cocr'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_cover_crop';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p340_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p340_planned_management'] = $in_data_array[6];
    $supplemental_data_submission['csc_p340_termination_method'] = $in_data_array[7];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_critical_area_planting($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'cap'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_critical_area_planting';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p342_species_category'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_feed_management($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fm'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_feed_management';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p592_crude_protein_percent'] = $in_data_array[5];
    $supplemental_data_submission['csc_p592_fat_percent'] = $in_data_array[6];
    $supplemental_data_submission['csc_p592_feed_additives'] = $in_data_array[7];
    $supplemental_data_submission['csc_p592_feed_additives_other'] = $in_data_array[8];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_field_border($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fb'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_field_border';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p386_species_category'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_filter_strip($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fs'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_filter_strip';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p393_strip_width'] = $in_data_array[5];
    $supplemental_data_submission['csc_p393_species_category'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_forest_farming($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'ff'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_forest_farming';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p379_land_use_prev_years'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_forest_stand_improvement($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'fsi'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_forest_stand_improvement';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p666_implementation_purpose'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => '666_implementation_purpose', 'name' => $in_data_array[5]]));;

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_grassed_waterway($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'gw'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_grassed_waterway';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p412_species_category'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_hedgerow_planting($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'hp'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_hedgerow_planting';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p422_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p422_species_density'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_herbaceous_wind_barriers($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'hwb'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_herbaceous_wind_barriers';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p603_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p603_barrier_width'] = $in_data_array[6];
    $supplemental_data_submission['csc_p603_number_of_rows'] = $in_data_array[7];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_mulching($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'm'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_mulching';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p484_mulch_type'] = $in_data_array[5];
    $supplemental_data_submission['csc_p484_mulch_coverage'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_nutrient_management($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'nm'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_nutrient_management';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p590_nutrient_type'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'nutrient_type', 'name' => $in_data_array[5]]));
    $supplemental_data_submission['csc_p590_application_method'] = $in_data_array[6];
    $supplemental_data_submission['csc_p590_pri_aplctn_method'] = $in_data_array[7];
    $supplemental_data_submission['csc_p590_application_timing'] = $in_data_array[8];
    $supplemental_data_submission['csc_p590_pri_aplctn_timing'] = $in_data_array[9];
    $supplemental_data_submission['csc_p590_application_rate'] = $in_data_array[10];
    $supplemental_data_submission['csc_p590_application_rate_unit'] = $in_data_array[11];
    $supplemental_data_submission['csc_p590_aplctn_rate_change'] = $in_data_array[12];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_pasture_and_hay_planting($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'pahp'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_pasture_hay_planting';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p512_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p512_termination_process'] = $in_data_array[6];
    $supplemental_data_submission['csc_p512_otr_term_process'] = $in_data_array[7];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_prescribed_grazing($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'pg'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_prescribed_grazing';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p528_grazing_type'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_range_planting($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'rp'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_range_planting';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p550_species_category'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_residue_and_tillage_management_notill($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'rtmnt'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_residue_tillage_no_till';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p329_surface_disturbance'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_residue_and_tillage_management_redtill($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'rtmrt'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_residue_till_reduced_till';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p345_surface_disturbance'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_riparian_forest_buffer($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'rfb'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_riparian_forest_buffer';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p391_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p391_species_density'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}


function import_riparian_herbaceous_cover($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'rhc'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_riparian_herbaceous_cover';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p390_species_category'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}


function import_roofs_and_covers($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'rac'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_roofs_and_covers';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p367_roof_cover_type'] = $in_data_array[5];
    $supplemental_data_submission['csc_p367_roof_cover_type_other'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_silvopasture($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'silvop'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_silvopasture';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p381_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p381_species_density'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}


function import_stripcropping($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'strip'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_stripcropping';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p585_strip_width'] = $in_data_array[5];
    $supplemental_data_submission['csc_p585_crop_category'] = $in_data_array[6];
    $supplemental_data_submission['csc_p585_number_of_strips'] = $in_data_array[7];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}


function import_tree_shrub_establishment($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'tse'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_tree_shrub_establishment';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p612_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p612_species_density'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_vegetative_barrier($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'vb'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_vegetative_barrier';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p601_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p601_barrier_width'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_waste_separation_facility($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'wsepf'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_waste_separation_facility';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p632_separation_type'] = $in_data_array[5];
    $supplemental_data_submission['csc_p632_use_of_solids'] = $in_data_array[6];
    $supplemental_data_submission['csc_p632_use_of_solids_other'] = $in_data_array[7];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}


function import_waste_storage_facility($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'wstof'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_waste_storage_facility';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p313_pri_waste_storage_sys'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'waste_storage_system', 'name' => $in_data_array[5]]));

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_waste_treatment($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'wt'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_waste_treatment';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p629_treatment_type'] = $in_data_array[5];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_waste_treatment_lagoon($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'wtl'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_waste_treatment_lagoon';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p359_pri_waste_storage_sys'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'waste_storage_system', 'name' => $in_data_array[5]]));
    $supplemental_data_submission['csc_p359_lagoon_cover_or_crust'] = filter_var($in_data_array[6], FILTER_VALIDATE_BOOLEAN);
    $supplemental_data_submission['csc_p359_lagoon_aeration'] = filter_var($in_data_array[7], FILTER_VALIDATE_BOOLEAN);

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_windshelter_est_reno($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'wreno'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_windbreak_shelterbelt';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_field_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p380_species_category'] = $in_data_array[5];
    $supplemental_data_submission['csc_p380_species_density'] = $in_data_array[6];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');

    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}

function import_anaerobic_digester($data, &$context){
    $year = $data['year'];
    $quarter = $data['quarter'];
    $in_data_array = $data['data_array'];
    $cur_count = $data['count'];
    $project_id_field = $data['project_id'];

    $dateConst = date('mdYhis', time());
    $entry_name = 'ad'. $dateConst . $cur_count;
    $in_data_array = array_map('decode_trim', $in_data_array);

    $field_id = array_pop(\Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'csc_field_enrollment', 'csc_f_enrollment_field_id' => $in_data_array[2]]));
    $producer_id = $field_id->csc_f_enrollment_producer_id->first()->get('entity')->getTarget()->getValue();
    $project_id = $producer_id->csc_project_id->first()->get('entity')->getTarget()->getValue();

    $supplemental_data_submission = [];
    $supplemental_data_submission['type'] = 'csc_anaerobic_digester';
    $supplemental_data_submission['name'] = $entry_name;
    $supplemental_data_submission['csc_fkield_id'] = $field_id;
    $supplemental_data_submission['csc_project_id'] = $project_id;
    $supplemental_data_submission['csc_p366_prior_waste_storage_sys'] = array_pop(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'waste_storage_system', 'name' => $in_data_array[5]]));
    $supplemental_data_submission['csc_p366_digester_type'] = $in_data_array[6];
    $supplemental_data_submission['csc_p366_digester_type_other'] = $in_data_array[7];
    $supplemental_data_submission['csc_p366_addtl_feedback_source'] = $in_data_array[8];
    $supplemental_data_submission['csc_p366_addtl_fdbk_source_otr'] = $in_data_array[9];

    foreach ($supplemental_data_submission as $key => $val) {
        if ($val === NULL) {
            $supplemental_data_submission[$key] = "";
        }
    }

    $new_entity = update_or_insert('log', $supplemental_data_submission, ['csc_project_id', 'csc_field_id']);
    $violations = array_filter(iterator_to_array($new_entity['entity']->validate()), 'is_custom_violation');


    return [
      'sheetname' => $data['sheetname'],
      'type' => ['log', $supplemental_data_submission['type']],
      'row_num' => $cur_count,
      'updated' => $new_entity['updated'],
      'entity' => $new_entity['entity'],
      'violations' => $violations,
    ];
}


function cscConvertExcelDate($inDate){
    $unixTimestamp = ($inDate - 25569) * 86400;
    $date = date(cscGetExcelDateFormat(), $unixTimestamp);

    return $date;
  }

function cscGetExcelDateFormat(){
    return "Y-m-d";
}

function update_or_insert($type, $submission, $unique_keys) {
    $compound_keys['type'] = $submission['type'];
    $for_sure_new = FALSE;
    foreach ($unique_keys as $key) {
        $val = $submission[$key];
        $compound_keys[$key] = $val;
        if ($val instanceof EntityInterface || $val instanceof TermInterface) {
            $compound_keys[$key] = $val->id();
            if ($compound_keys[$key] === NULL) {
                $for_sure_new = TRUE;
            }
        }
    }

    if (!$for_sure_new) {
        $existing = reset(\Drupal::entityTypeManager()->getStorage($type)->loadByProperties($compound_keys));
    }

    if($existing) { // update
        foreach($submission as $k => $v) {
            $existing->set($k, $v);
        }
        return ['entity' => $existing, 'updated' => TRUE];
    } else { // insert
        if ($type == 'asset') {
            $entity = Asset::create($submission);
        }
        else {
            $entity = Log::create($submission);
        }
        return ['entity' => $entity, 'updated' => FALSE];
    }
}