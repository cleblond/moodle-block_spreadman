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



    $script = '<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/chart/codebase/dhtmlx.css">
                       <script src="'.$CFG->wwwroot.'/filter/chart/codebase/dhtmlx.js"></script>';


$script .= '
        <table>
        <tr><td style="text-align: center;"><b>'.$result->title.'</b></td></tr>
        <tr><td><div id="chart_container" style="width:600px;height:300px;"></div></td></tr></table>
        <div>
        <button id="toggle" >Show/Hide</button>
        <div id="chartoptions" >
        <table>
        
        <tr><td><div id="gridboxuser" style="width:600px; height:60px; background-color:white; float:left;"></div></td></tr>
        <tr><td><div id="gridboxdata" style="width:600px; height:170px; background-color:white; float:left;"></div></td></tr>
        </table>
        <p><a href="javascript:void(0)" onclick="mygrid.addRow((new Date()).valueOf(),[\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\'],mygrid.getRowIndex(mygrid.getSelectedId())+1)">Add row</a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="mygrid.deleteSelectedItem()">Remove Selected Row</a></p>
        <input type="button" name="some_name" value="update" onclick="myDataProcessor.sendData();myDataProcessorFG.sendData();">
        </div>
        </div>
	<script type="text/javascript">
	YUI().use(\'node\', function(Y) {
	    Y.delegate(\'click\', function(e) {
		var buttonID = e.currentTarget.get(\'id\'),
		    node = Y.one(\'#chartoptions\');
		    
		if (buttonID === \'show\') {
		    node.show();
		} else if (buttonID === \'hide\') {
		    node.hide();
		} else if (buttonID === \'toggle\') {
		    node.toggleView();
		}

	    }, document, \'button\');
	});
	</script>
        <script>
        var charttype;
        window.onload = function(){


        chartbarh =  new dhtmlXChart({
                view:"'.$result->type.'",
                //view:"bar",
                color:"#data2#",
                gradient:"rising",
                //gradient:"3d",
                container:"chart_container",
                //xValue: "#data0#",
                value:"#data1#",
                label:"#data1#",  //Bar only
                yAxis:{
                template:"#data0#",
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'",
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
                view:"'.$result->type.'",
                //view:"bar",
                color:"#data2#",
                gradient:"rising",
                //gradient:"3d",
                container:"chart_container",
                //xValue: "#data0#",
                value:"#data1#",
                label:"#data0#",  //Bar only
                yAxis:{
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'",
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

        chartpie =  new dhtmlXChart({
            view:"'.$result->type.'",
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

        chartscatter =  new dhtmlXChart({
                view:"scatter",
                //view:"bar",
                color:"#66ccff",
                //gradient:"3d",
                container:"chart_container",
                xValue: "#data0#",
                value:"#data1#",
                //label:"#data0#",  //Bar only
                yAxis:{
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'",
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
        
        chartline =  new dhtmlXChart({
                view:"'.$result->type.'",
                //view:"bar",
                color:"#66ccff",
                //gradient:"3d",
                container:"chart_container",
                xValue: "#data0#",
                value:"#data1#",
                //label:"#data0#",  //Bar only
                yAxis:{
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'",
                template:"#data0#"
                //template:function(obj){
                //    return (obj%20?"":obj)
                //}
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




        function refresh_chart(){
                charttype.clearAll();
                charttype.parse(mygrid,"dhtmlxgrid");
                //console.log(charttype.parse(mygrid,"dhtmlxgrid"));
        };
        

        function doOnColorChanged(stage,rId,cIn){
                if(stage==2){
                        if(cIn==2){
                                mygrid.cells(rId,3).setValue(mygrid.cells(rId,2).getValue())
                        }else if(cIn==3){
                                mygrid.cells(rId,2).setValue(mygrid.cells(rId,3).getValue())
                        }
                }
                return true;
        }




        if (\''.$result->type.'\' === \'scatter\') {
                //alert("scatter chart");
                //must be scatter
        var charttype = chartscatter;

        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("x1,y1,x2,y2,x3,y3,x4,y4,x5,y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75")
    //    mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
    //    mygrid.setSkin("dhx_skyblue")
    //    mygrid.enableSmartRendering(true);

        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        mygrid.setColSorting("int,int,int,int,int,int,int,int,int,int")





        } else if (\''.$bartype.'\' === \'bar\') {
                //alert("bar chart");
                //must be bar
        if (\''.$result->type.'\' === \'barH\') {var charttype = chartbarh}else{charttype = chartbar;}

        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("Bar Label,Bar Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
    //    mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
    //    mygrid.setSkin("dhx_skyblue")
    //    mygrid.enableSmartRendering(true);
        mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
        mygrid.setColSorting("str,str,str,str")


        } else if (\''.$pietype.'\' === \'pie\') {
        var charttype = chartpie;

        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("Slice Label,Slice Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")

        mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
        mygrid.setColSorting("str,str,str,str")

        } else if (\''.$linetype.'\' === \'line\') {
                //alert("scatter chart");
                //must be scatter
        var charttype = chartline;

        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("x1,y1,x2,y2,x3,y3,x4,y4,x5,y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75")
     //   mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
     //   mygrid.setSkin("dhx_skyblue")
     //   mygrid.enableSmartRendering(true);

        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        mygrid.setColSorting("int,int,int,int,int,int,int,int,int,int")
        }

        mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
        mygrid.setSkin("dhx_skyblue")
        mygrid.enableSmartRendering(true);

        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/get.php?id='.$chartid.'&grid=data",refresh_chart);
        //mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/gridH.xml",refresh_chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });


        //OPtions grid.
        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setHeader("Type,Title,x-axis Title,y-axis Title");
        myformgrid.setInitWidths("75,75,150,150")
        myformgrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
        myformgrid.setSkin("dhx_skyblue")
        myformgrid.enableSmartRendering(true);

        myformgrid.setColTypes("coro,ed,ed,ed");
        myformgrid.setColSorting("int,int,int,int")
        myformgrid.init();
        myformgrid.loadXML("'.$CFG->wwwroot.'/filter/chart/get.php?id='.$chartid.'&grid=user",refresh_chart);
       
        myformgrid.attachEvent("onEditCell",function(stage){
                if (stage == 2) {
                        //charttype.parse(myformgrid,"dhtmlxgrid");
                        xtit = myformgrid.cells2(0,2).getValue();
                        //alert(xtit);
                        console.log(charttype);
                        console.log(charttype._configXAxis.title);
			//chart.clearAll();
			//chart.load("/data/test.json","json");
			//setTimeout(refreshchart,60000);   
			charttype._configXAxis.title = "NEW AXIS TITLE";
                        //charttype.clearAll();
                        charttype.refresh();
			console.log(charttype._configXAxis.title);
                        

                        //xtit = charttype.update(123, { text:"abc", value:22 });
                        //alert(charttype.parse(myformgrid,"dhtmlxgrid"));
                        //refresh_chart();
                }
                return true;
        });

        myDataProcessor = new dataProcessor("'.$CFG->wwwroot.'/filter/chart/update.php?chartid='.$chartid.'"); //lock feed url
        myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessor.setUpdateMode("off"); //disable auto-update
        myDataProcessor.init(mygrid); //link dataprocessor to the grid

        myDataProcessorFG = new dataProcessor("'.$CFG->wwwroot.'/filter/chart/updateform.php?chartid='.$chartid.'"); //lock feed url
        myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessorFG.setUpdateMode("off"); //disable auto-update
        myDataProcessorFG.init(myformgrid); //link dataprocessor to the grid
    }

</script>';

echo $script;
} //end userid check if



}





echo $OUTPUT->footer();



