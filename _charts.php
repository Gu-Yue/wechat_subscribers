<?php
/*
 * Settings Page, It's required by WPWSLGeneral Class only.
 *
 */
global $wpdb;
$match   = $wpdb->get_results("select count(id) as count from wechat_subscribers_lite_keywords where is_match = 'y';");
$unmatch = $wpdb->get_results("select count(id) as count from wechat_subscribers_lite_keywords where is_match = 'n';");
$match   = $match ? $match[0]->count : 0;
$unmatch = $unmatch ? $unmatch[0]->count : 0;
//Load content
require_once( 'content.php' );
?>
<link href="<?php echo WPWSL_PLUGIN_URL;?>/css/style.css" rel="stylesheet">
<div class="wrap">
	<?php echo $content['header'];?>
	<hr>
	<h2>
	<?php _e('Charts',"WPWSL");?> 
	<a href="<?php menu_page_url(WPWSL_HISTORY_PAGE);?>" class="add-new-h2"><?php _e('Statistics',"WPWSL");?></a>
	</h2>
	<br>
	<style type="text/css">
      #container {
        width : 600px;
        height: 384px;
      }
    </style>
    <h2>关键字匹配率</h2>
	<div id="container">  
    </div>
    
    <script type="text/javascript" src="<?php echo WPWSL_PLUGIN_URL;?>/js/flotr2.min.js"></script>
    <script type="text/javascript">
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
        label: '匹配     ( <?php _e($match);?> )',
        pie: {
            explode: 10
        }
    }, {
        data: d4,
        label: '不匹配 ( <?php _e($unmatch);?> )'
    }], {
        HtmlText: false,
        grid: {
            verticalLines: false,
            horizontalLines: false
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
    </script>
</div>