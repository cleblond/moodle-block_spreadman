<?php
// --------------------------------------------------------- 
// block_cmanager is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// block_cmanager is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// COURSE REQUEST MANAGER BLOCK FOR MOODLE
// by Kyle Goslin & Daniel McSweeney
// Copyright 2012-2014 - Institute of Technology Blanchardstown.
// --------------------------------------------------------- 
/**
 * COURSE REQUEST MANAGER
 *
 * @package    block_cmanager
 * @copyright  2014 Kyle Goslin, Daniel McSweeney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");

global $CFG, $USER, $DB;
require_once("$CFG->libdir/formslib.php");
require_once('../../course/lib.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_login();

/** Navigation Bar **/
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('spreadmanDisplay', 'block_spreadman'));
$PAGE->navbar->add('View Spreadsheet/Chart');
$PAGE->set_url('/blocks/spreadman/view.php');
$context = context_system::instance();
$PAGE->set_context($context);
$courseid=optional_param('id',NULL,PARAM_INT);
$sheetid=optional_param('sheetid',NULL,PARAM_INT);
$chartid=optional_param('chartid',NULL,PARAM_INT);

$PAGE->set_heading('View Spreadsheet');
$PAGE->set_title('title');
echo $OUTPUT->header();
//echo "sheetid=".$sheetid;
//echo "chartid=".$chartid;

if(isset($sheetid)){
require_once($CFG->dirroot . "/filter/spreadsheet/codebase/php/grid_cell_connector.php");
$result = $DB->get_record('filter_spreadsheet_sheet', array('sheetid'=>$sheetid));

		if($result->userid == $USER->id){

    			$script = '<script src="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/spreadsheet.php?load=js"></script>';
            		$script .= '<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlx_core.css">
                       <link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlxspreadsheet.css">
                       <link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlxgrid_wp.css">';



            		$script .= '<script>window.onload = function() {
		        
			var dhx_sh1 = new dhtmlxSpreadSheet({
				load: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/php/data.php",
				save: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/php/data.php",
				parent: "gridobj",
				icons_path: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/imgs/icons/",
				math: true,
				autowidth: false,
				autoheight: false
			}); 
			dhx_sh1.load("'.$result->sheetid.'" , "'.$result->accesskey.'");
		        }
	                </script>
                        <div class="ssheet_cont" id="gridobj"></div>';

                        echo $script;
		}

}

if(isset($chartid)){

        $result = $DB->get_record('filter_chart_users', array('id'=>$chartid));
if($result->userid == $USER->id){


   $css = '<link rel="stylesheet" href="' . $CFG->wwwroot . '/filter/chart/codebase/dhtmlx.css">';
   $dhtmlxmods = $css . "<script type='text/javascript'>
            //Y.on('load', function () {
            YUI().applyConfig({
                modules: {
                    'dhtmlxcommon': {
                    fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxcommon.js'
                    },
                    'dhtmlxchart': {
                    fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxchart.js'
                    },
                    'dhtmlxgrid': {
                    fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxgrid.js'
                    },
                }
            });
        </script>";

$readonly = false;
        //Take care of pie type graphs.
        $pietypes = array("pie", "pie3D", "donut");
        $bartype='';
        $pietype='';
        $linetype='';
        if(in_array($result->type, $pietypes)){
        $pietype = "pie";
        }
        $bartypes = array("bar", "barH");
        if(in_array($result->type, $bartypes)){
        $bartype = "bar";
        }
        $linetypes = array("line", "spline");
        if(in_array($result->type, $linetypes)){
        $linetype = "line";
        }



//echo "MUST BE A CHART";
///////////////////////////

$test = "
<script>
YUI().use('tabview', function(Y) {
    var tabview = new Y.TabView({
        srcNode: '#demo'
    });

    tabview.render();
});
</script>";



        $pre = $result->title . '
        <div id="chart_container" style="width:600px;height:300px;"></div>
        <div>
        <div id="demo">
	    <ul style = "border-style: none;">
		<li><a href="#options">Chart Options</a></li>
		<li><a href="#data">Data</a></li>
	    </ul>

            <div style="width:605px; height:200px;">
            <div id="options"><div id="gridboxuser" style="width:600px; height:95px; background-color:white; float:left;"><p> here </p></div></div>
            <div id="data"><div id="gridboxdata" style="width:600px; height:170px; background-color:white; float:left;"></div>
            <p><a id="addrow" href="javascript:void(0)">Add row</a>&nbsp;&nbsp;<a id="deleterow" href="javascript:void(0)">Remove Selected Row</a></p>
            </div>
            </div>
        </div>
        <input type="button" id="savedata" value="Save"/>
        </div>
        <script>
    YUI().use(\'node\', \'dhtmlxcommon\', \'dhtmlxchart\', \'dhtmlxgrid\', function(Y) {

        // Handle add/delete rows and save data events.
        var addrowinput = Y.one(\'#addrow\');
        addrowinput.on(\'click\', function() {
        mygrid.addRow((new Date()).valueOf(),[\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\'],mygrid.getRowIndex(mygrid.getSelectedId())+1);
        });

        var deleterowinput = Y.one(\'#deleterow\');
        deleterowinput.on(\'click\', function() {
        mygrid.deleteSelectedItem();
        });

        var saveinput = Y.one(\'#savedata\');
        saveinput.on(\'click\', function() {
        myDataProcessor.sendData();myDataProcessorFG.sendData();
        });

        function refresh_chart(){
                charttype.clearAll();
                charttype.parse(mygrid,"dhtmlxgrid");

                        //charttype.hideSeries(0);
        };
        function init_rochart(){
        charttype.parse(mygrid,"dhtmlxgrid");
        }
        function init_rochartline(){
        charttype.parse(mygrid,"dhtmlxgrid");
        }

        function init_chart(){
                charttype.parse(mygrid,"dhtmlxgrid");
                //charttype.hideSeries(0);
                //mygrid.hdr.rows[2].cells[0].firstChild.firstChild.checked = "false";
                cbxs = "' . $result->chartoptions . '";
                cbx = cbxs.split("~");
                //console.log(cbx);
                   if ("' . $linetype . '" == "line"){
                        j = 0;
            for (i = 0; i < cbx.length; i++) {

                //text += cars[i] + "<br>";
                            if (cbx[i]=="false"){
                              //console.log("init_chart");
                              charttype.hideSeries(i);
                              mygrid.hdr.rows[2].cells[j].firstChild.firstChild.checked = false;
                            }
                            j = j + 1;
            }
                   }
                 //       charttype.hideSeries(0);

        };

        function init_chartline(){

                charttype.parse(mygrid,"dhtmlxgrid");
                //mygrid.hdr.rows[2].cells[0].firstChild.firstChild.checked = "false";
                cbxs = "' . $result->chartoptions . '";
                cbx = cbxs.split("~");
                //console.log(cbx);

            for (i = 0; i < cbx.length; i++) {

                //text += cars[i] + "<br>";
                            if (cbx[i]=="false"){
                              charttype.hideSeries(i);
                              mygrid.hdr.rows[2].cells[i+1].firstChild.firstChild.checked = false;
                            }
            }
        };

        function doOnColorChanged(stage,rId,cIn){
                //console.log("HERERERE");
                if(stage==2){
                        if(cIn==2){
                                mygrid.cells(rId,3).setValue(mygrid.cells(rId,2).getValue())
                        }else if(cIn==3){
                                mygrid.cells(rId,2).setValue(mygrid.cells(rId,3).getValue())
                        }
                }
                return true;
        }

        function doOnCheck(rowId,cellInd,state){

                if(state == 0) {
                charttype.hideSeries(cellInd/2);
        } else {
                charttype.showSeries(cellInd/2);
        }

                ///build up new options string
                j = 0;
                var options = "";
                for (i = 0; i < 5; i++) {
        options = options + "~" + mygrid.hdr.rows[2].cells[j].firstChild.firstChild.checked;
                //console.log(j);
                j = j + 1;
                }
                options=options.substring(1);
                //console.log(options);
                myformgrid.cells(' . $chartid . ',4).setValue(options);
        charttype.refresh();

                myDataProcessorFG.setUpdated(' . $chartid . ',"updated");
                myDataProcessorFG.sendData();


    }


        function doOnCheckline(rowId,cellInd,state){
                //console.log(myformgrid.cells(' . $chartid . ',4).getValue());
                //console.log(rowId+","+cellInd);
                if(state == 0) {
                charttype.hideSeries(cellInd-1);
                } else {
                charttype.showSeries(cellInd-1);
                }
                //charttype.refresh();

                ///build up new options string
                var j = 0;
                var options = "";
                for (i = 0; i < 5; i++) {
        options = options + "~" + mygrid.hdr.rows[2].cells[i+1].firstChild.firstChild.checked;
                //console.log(i);

                }
                options=options.substring(1);
                //console.log(options);
                myformgrid.cells(' . $chartid . ',4).setValue(options);
        //charttype.refresh();
                myDataProcessorFG.setUpdated(' . $chartid . ',"updated");
                myDataProcessorFG.sendData();

    }';

////////////

        switch ($result->type) {
            case "scatter":
                $script = '
        charttype =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"red",
                container:"chart_container",
                xValue: "#data0#",
                value:"#data1#",
                //label:"#data0#",  //Bar only
                yAxis:{
                title:"' . $result->yaxistitle . '"
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                },
                legend:{
                    layout:"y",
                    align:"right",
                    valign:"middle",
                    width:120,
                   // toggle:true,
                    marker:{ type: "item"},
                    values:[' . $legend . ']},
                    item:{
                       radius:4,
                       // borderColor:"red",
                       borderWidth:1,
                       color:"red",
                       type:"d",
                       shadow:true
                    },
               /* tooltip:{
                  template:"(#data0# , #data1#)"
                }, */
                    border:false
        });
        ' . $addseries . '
        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("x1,y1,x2,y2,x3,y3,x4,y4,x5,y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75");
        //mygrid.attachHeader("#master_checkbox,,#master_checkbox,,#master_checkbox,,#master_checkbox,,#master_checkbox");
        mygrid.attachHeader("#master_checkbox,#cspan,#master_checkbox,#cspan,#master_checkbox,#cspan,#master_checkbox,#cspan,#master_checkbox,#cspan");
        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        mygrid.setColSorting("int,int,int,int,int,int,int,int,int,int");
        //mygrid.attachEvent("onCheckbox",doOnCheck);
        mygrid.setColumnColor("silver,silver,lightgrey,lightgrey,silver,silver,lightgrey,lightgrey,silver,silver");
        mygrid.checkAll(true);


        mygrid.setImagePath(\'' . $CFG->wwwroot . '/filter/chart/codebase/imgs/\');
        mygrid.setSkin("dhx_skyblue");

        mygrid.enableSmartRendering(true);
        mygrid.attachEvent("customMasterChecked", doOnCheck);
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=data",init_' . $ro . 'chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=user");';

        if($readonly == false) {
        	$script .= '
		myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessor.setUpdateMode("off"); //disable auto-update
		myDataProcessor.init(mygrid); //link dataprocessor to the grid
		myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessorFG.setUpdateMode("off"); //disable auto-update
		myDataProcessorFG.init(myformgrid);';
         }
         $endscript = '});</script>';
         $script = $script . $endscript;


                break;
            case "line":
            case "spline":
                $script = '

        charttype =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"red",
                container:"chart_container",
                xValue: "#data0#",
                value:"#data1#",
                yAxis:{
                title:"' . $result->yaxistitle . '"
                },
                line:{
                     color:"red",
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                template:"#data0#"
                },
                item:{
                radius:3,
                type:"s",
                borderWidth:1,
                color:"red"},
                legend:{
            layout:"y",
            align:"right",
            valign:"middle",
            width:120,
            toggle:true,
                        marker:{type: "item"},
            values:[' . $legend . ']},
              /*  tooltip:{
                  template:"(#data0# , #data1#)"
                }, */
                border:false
        });

        ' . $addseries . '

        //var charttype = chartscatter;

        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("x1,y1,y2,y3,y4,y5,j,j,j,j");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75");
        mygrid.attachHeader(",#master_checkbox,#master_checkbox,#master_checkbox,#master_checkbox,#master_checkbox,,,,");
        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        mygrid.setColumnColor("grey,lightgrey,silver,lightgrey,silver,silver,silver,silver,silver,silver");
        mygrid.checkAll(true);
        mygrid.setImagePath(\'' . $CFG->wwwroot . '/filter/chart/codebase/imgs/\');
        mygrid.setSkin("dhx_skyblue");
        mygrid.enableSmartRendering(true);
        mygrid.attachEvent("customMasterChecked", doOnCheckline);
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);
        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=data",init_' . $ro . 'chartline);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=user");';

        if($readonly == false) {
        	$script .= '
		myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessor.setUpdateMode("off"); //disable auto-update
		myDataProcessor.init(mygrid); //link dataprocessor to the grid
		myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessorFG.setUpdateMode("off"); //disable auto-update
		myDataProcessorFG.init(myformgrid);';
         }
         $endscript = '});</script>';
         $script = $script . $endscript;

                break;
            case "barH";
            case "bar":
                $script = '
        chartbarh =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"#data2#",
                gradient:"rising",
                container:"chart_container",
                value:"#data1#",
                label:"#data1#",  //Bar only
                yAxis:{
                template:"#data0#",
                title:"' . $result->yaxistitle . '"
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                //template:"#data0#"
                template:function(obj){
                    return (obj%20?"":obj)
                }

                },
                item:{
                   radius:5,
                   borderColor:"#f38f00",
                   borderWidth:1,
                   color:"#ff9600",
                   type:"d",
                   shadow:true
                },
                tooltip:{
                  template:"(#data0# , #data1#)"
                },
                border:false
        });

        chartbar =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"#data2#",
                gradient:"rising",
                container:"chart_container",
                value:"#data1#",
                label:"#data0#",  //Bar only
                yAxis:{
                title:"' . $result->yaxistitle . '"
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                template:"#data0#"
                },
                item:{
                   radius:5,
                   borderColor:"#f38f00",
                   borderWidth:1,
                   color:"#ff9600",
                   type:"d",
                   shadow:true
                },
                tooltip:{
                  template:"(#data0# , #data1#)"
                },
                border:false
        });
        if (\'' . $result->type . '\' === \'barH\') {var charttype = chartbarh}else{charttype = chartbar;}
        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("Bar Label,Bar Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
        mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
        mygrid.setColSorting("str,str,str,str")
        mygrid.setSkin("dhx_skyblue");

        mygrid.enableSmartRendering(true);
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=data",init_chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=user");';

        if($readonly == false) {
        	$script .= '
		myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessor.setUpdateMode("off"); //disable auto-update
		myDataProcessor.init(mygrid); //link dataprocessor to the grid
		myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessorFG.setUpdateMode("off"); //disable auto-update
		myDataProcessorFG.init(myformgrid);';
         }
         $endscript = '});</script>';
         $script = $script . $endscript;

                break;
            case "pie3D";
            case "donut";
            case "pie":
                $script = '
        chartpie =  new dhtmlXChart({
            view:"' . $result->type . '",
            container:"chart_container",
            value:"#data1#",
            color:"#data2#",
            tooltip:"#data1#",
            label:"#data0#",
            shadow:0,
            radius:65,
            x:280,
            y:120
        });
        var charttype = chartpie;
        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("Slice Label,Slice Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
        mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
        mygrid.setColSorting("str,str,str,str")
        mygrid.setSkin("dhx_skyblue");
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=data",init_chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $chartid . '&grid=user");';

        if($readonly == false) {
        	$script .= '
		myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessor.setUpdateMode("off"); //disable auto-update
		myDataProcessor.init(mygrid); //link dataprocessor to the grid
		myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $chartid . '"); //lock feed url
		myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
		myDataProcessorFG.setUpdateMode("off"); //disable auto-update
		myDataProcessorFG.init(myformgrid); ';
         }
         $endscript = '});</script>';
         $script = $script . $endscript;
                break;
        }

//print_object($pre);
//print_object($script);
//print_object($test);
echo $dhtmlxmods. $pre . $script. $test;


/////////////////////////



//echo $script;
} //end userid check if



}





echo $OUTPUT->footer();



