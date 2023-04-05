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
        marekt activities:
        <form action="/csv_import/upload_market_activities" enctype="multipart/form-data" method="post">
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