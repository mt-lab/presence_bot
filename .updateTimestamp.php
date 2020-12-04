<?php
    $TIMESTAMP_FILEPATH = ".botTimestamp";

    function validateRequest(){
        // PLACE HERE VALIDATION LOGIC IF NECESSARY
        // if ($_GET['key'] != getenv("LAB_NODE_KEY")){
        //     exit;
        // }
        if ($_GET['key'] != ''){
            exit;
        }
    }

    function setFileTimestamp($time){
        global $TIMESTAMP_FILEPATH;

        $timestampFile = fopen($TIMESTAMP_FILEPATH, "w") or die("Unable to open file!");
        fwrite($timestampFile, $time);
        fclose($timestampFile);
    }

    validateRequest();

    setFileTimestamp(time());
?>
