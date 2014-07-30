<?php
/*
 * Settings Page, It's required by WPWSLGeneral Class only.
 *
 */
define("SELECT_ROWS_AMOUNT", 100);
require_once( 'class-wpwsl-history-table.php' );

if(isset($_GET['clear_all_records'])){
	global $wpdb;
    $wpdb->query("delete from wechat_subscribers_lite_keywords");
}

function delete_record($id){
	global $wpdb;
    $wpdb->query("delete from wechat_subscribers_lite_keywords where id='$id'");
}
if(isset($_GET['action']) && isset($_GET['action2'])){
	if($_GET['action']=='delete' || $_GET['action2']=='delete'){
		if(isset($_GET['record'])){
	        foreach($_GET['record'] as $r){
	        	 delete_record($r);
	        }
        }
	}
}
function results_order() {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'time';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
		return $orderby." ".$order;
	}


$order = results_order();
$paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
$start = ($paged-1)*SELECT_ROWS_AMOUNT;
global $wpdb;
//history
$total = $wpdb->get_results("select count(id) as total from wechat_subscribers_lite_keywords");
$match   = $wpdb->get_results("select count(id) as count from wechat_subscribers_lite_keywords where is_match = 'y';");
$unmatch = $wpdb->get_results("select count(id) as count from wechat_subscribers_lite_keywords where is_match = 'n';");
$match   = $match ? $match[0]->count : 0;
$unmatch = $unmatch ? $unmatch[0]->count : 0;
$unmatch_ = $unmatch == 0 && $match == 0 ? 0 : $unmatch;
$unmatch =  $unmatch == 0 && $match == 0 ? 1 : $unmatch;
$matchTopTen   = $wpdb->get_results("select keyword,count(keyword) as count from wechat_subscribers_lite_keywords where is_match='y' group by keyword order by count(keyword) desc limit 10 ");
$unmatchTopTen = $wpdb->get_results("select keyword,count(keyword) as count from wechat_subscribers_lite_keywords where is_match='n' group by keyword order by count(keyword) desc limit 10 ");

//records
$raw = $wpdb->get_results("select id,openid,keyword,is_match,time from wechat_subscribers_lite_keywords order by $order limit $start,".SELECT_ROWS_AMOUNT);
$data=array();
foreach($raw as $d){
	 $d->is_match = $d->is_match=="y"? __("Yes","WPWSL") :"<span style='color:red;'>".__("No","WPWSL")."<span>";
	 $data[]=array('ID'=>$d->id, 'openid'=>$d->openid, 'keyword'=>$d->keyword, 'is_match' =>$d->is_match, 'time'=>$d->time);
}

//Prepare Table of elements 
$wp_list_table = new WPWSL_History_Table($data);
$wp_list_table->prepare_items();

//Load content
require_once( 'content.php' );
?>
<link href="<?php echo WPWSL_PLUGIN_URL;?>/css/style.css" rel="stylesheet">
<div class="wrap">
	<?php echo $content['header'];?>
	<hr>
	<h2>
	 <?php _e('Statistics','WPWSL');?>
     <form action="" method="get" style="float:right;">
     <input type="hidden" name="page" value="wpwsl-history-page" />
	 <button  id="clear_all_records" type="submit" name="clear_all_records" value="rows" class="add-new-h2"><?php _e("Clear All Records","WPWSL");?></button>
	 </form>
	</h2>
    <br>
    	<br>
<style type="text/css">
	#container,#container2,#container3{
	width : 570px;
	height: 300px;
	border: 1px solid #E1E1E1;
	}
</style>
<div style="float:left">
<h2>匹配关键词百分比</h2>
<div id="container">
</div>
</div>
<div style="float:left">
<h2>关键字匹配率</h2>
<div id="container2">
</div>
</div>
<div style="float:left">
<h2>不匹配关键词百分比</h2>
<div id="container3">
</div>
</div>
<script type="text/javascript" src="<?php echo WPWSL_PLUGIN_URL;?>/js/flotr2.min.js"></script>
<script type="text/javascript">
(function basic_pie(container){
					var
					<?php
					$i=0; 
					foreach ($matchTopTen as $key => $value) {
						$i++;
						echo "d$i=[[0,".$value->count."]],";
					}
					?>
					graph;

					graph = Flotr.draw(container,[
					<?php
					$i=0; 
					foreach ($matchTopTen as $key => $value) {
						$i++;
						echo "{data:d$i,label:'".$value->keyword."'},";
					}
					?>	
					], 
					{
					   HtmlText: false,
					   grid: {
						verticalLines: false,
						horizontalLines: false,
						outlineWidth: 0,
						backgroundColor: "#FFFFFF"
					},
					xaxis: {
						showLabels: false
					},
					yaxis: {
						showLabels: false
					},
					pie: {
						show: true,
						explode: 6
					},
					mouse: {
						track: true
					},
					legend: {
					position: 'ab',
					backgroundColor: '#D2E8FF'
					}
					});
					})(document.getElementById("container"));
	(function basic_pie(container){
					var
					d3 = [
					[0, <?php _e($match);?>]
					],
					d4 = [
					[0, <?php _e($unmatch);?>]
					],
					graph;

					graph = Flotr.draw(container, [
					{
						data: d3,
						label: '匹配 ( <?php _e($match);?> )',
					pie: {
						explode: 10
					}
					}, {
						data: d4,
						label: '不匹配 ( <?php _e($unmatch_);?> )'
					}], {
					HtmlText: false,
					grid: {
						verticalLines: false,
						horizontalLines: false,
						outlineWidth: 0,
						backgroundColor: "#FFFFFF"
					},
					xaxis: {
						showLabels: false
					},
					yaxis: {
						showLabels: false
					},
					pie: {
						show: true,
						explode: 6
					},
					mouse: {
						track: true
					},
					legend: {
					position: 'ab',
					backgroundColor: '#D2E8FF'
					}
					});
					})(document.getElementById("container2"));	
     (function basic_pie(container){
					var
					<?php
					$i=0; 
					foreach ($unmatchTopTen as $key => $value) {
						$i++;
						echo "d$i=[[0,".$value->count."]],";
					}
					?>
					graph;

					graph = Flotr.draw(container,[
					<?php
					$i=0; 
					foreach ($unmatchTopTen as $key => $value) {
						$i++;
						echo "{data:d$i,label:'".$value->keyword."'},";
					}
					?>	
					], 
					{
					   HtmlText: false,
					   grid: {
						verticalLines: false,
						horizontalLines: false,
						outlineWidth: 0,
						backgroundColor: "#FFFFFF"
					},
					xaxis: {
						showLabels: false
					},
					yaxis: {
						showLabels: false
					},
					pie: {
						show: true,
						explode: 6
					},
					mouse: {
						track: true
					},
					legend: {
					position: 'ab',
					backgroundColor: '#D2E8FF'
					}
					});
					})(document.getElementById("container3"));
	</script>
    <br>
	<form action="" method="get">
		<input type="hidden" name="page" value="<?php echo WPWSL_HISTORY_PAGE;?>" />
		<input type="hidden" name="per_page" value="<?php _e($per_page); ?>" />
		<?php $wp_list_table->display();?>
	</form>
</div>
<script>document.getElementById("clear_all_records").onclick = function(){var r=confirm("<?php _e('Empty all the records ?','WPWSL');?>"); if(!r) return false;}</script>