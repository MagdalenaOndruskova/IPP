<?php 

 #
 # Name: parse.php
 # Caption: Project 1 of IPP  
 # Author: Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 # Date: 10.02.2020 
 #

 #TODO 5 argumentov, rozšírenie padá :D 

 #### extension ####
 $comments = false; 
 $locs = false; 
 $jumps = false;
 $labels = false; 
 $file; 

 $count_comments =0; 
 $count_locs = 0; 
 $count_jumps =0;
 $count_labels = 0; 

#### ERROR CODES ####
define("ERR_HEADER",21);
define("ERR_OPERATOR_CODE",22);
define("ERR_LEX_SYX", 23);
define("ERR_WRONG_PARAMS",10);
define("ERR_OPEN_INPUT_FILE", 11);
define("ERR_OPEN_OUTPUT_FILE",12);
define("ERR_INTERN", 99);
define("OK_HELP",-1);
define("OK",0);

#### OPCODE shapes ####
define("OPCODE", 0);
define("OPCODE_VAR",1);
define("OPCODE_SYMB",1);
define("OPCODE_LABEL",1);
define("OPCODE_VAR_SYMB",2);
define("OPCODE_VAR_TYPE",2);
define("OPCODE_LABEL_SYMB1_SYMB2",3);
define("OPCODE_VAR_SYMB1_SYMB2", 3);

 require_once __DIR__.'/parselib/lexsyntax_control.php';

# 
# Function checks arguments given to the script
function checkArguments()
{
    global $argc; 
    global $argv;
    global $comments;
    global $locs;
    global $labels;
    global $jumps;
    global $file;

    #$count=0;
    # no argument
    if ( $argc == 1)
    {
        return OK;
    }
    else 
    { 
        $options = getopt(null, ["help", "stats:", "loc", "comments", "labels", "jumps"]);
        if ( array_key_exists("help", $options))
        {
            if ( $argc == 2)
            {
                fprintf(STDOUT, "Skript typu filter (parse.php v jazyku PHP 7.4) \n načíta zo štandardného vstupu zdrojový kód v IPP-code20, \n skontroluje lexikálnu a syntaktickú správnosť kódu \n a vypíše na štandartný výstup XML reprezentáciu programu. \n");
                exit (OK);
            }
            else 
            {
                exit (ERR_WRONG_PARAMS);
            }   
        } 
        if ( $argc > 1)
        {         
            if ( array_key_exists("stats", $options) )
            {
                $file_name = $options["stats"];
                if ( is_array($file_name))
                {
                    exit (ERR_WRONG_PARAMS);
                }
                $file = fopen($file_name, "w");
                if ( $file == false )
                {
                    return ERR_OPEN_OUTPUT_FILE;
                }
                if (array_key_exists("loc", $options) )
                {
                   $locs= true;
                }
                if (array_key_exists("comments", $options) )
                {
                    $comments = true;
                }
                if (array_key_exists("jumps", $options) )
                {
                    $jumps= true;
                }
                if (array_key_exists("labels", $options) )
                {
                    $labels= true;
                }

            }
        }
        else   
        {
            exit (ERR_WRONG_PARAMS);
        }
        return OK;
        
    }
}


#### MAIN PROGRAM #### 

## XML configuration ##
$domtree = new DOMDocument('1.0', 'UTF-8');
$domtree->formatOutput = true;
$xmlRoot = $domtree->createElement("program");
$xmlRoot->setAttribute("language", "IPPcode20");
$xmlRoot = $domtree->appendChild($xmlRoot);

## call function to check arguments ##
$arguments = checkArguments();

## call function to check code ##
$code = checkCode();
if ( $code === ERR_HEADER || $code === ERR_LEX_SYX)
{
    exit ($code);
}

## extension ##
array_shift($argv);
foreach ($argv as $i=>$value)
{
    
    if ( $comments == true && $value == "--comments")
    {
        fwrite($file, $count_comments . "\n");
    }
    if ( $locs == true && $value == "--loc")
    {
        fwrite($file, $count_locs . "\n");
    }
    if ( $jumps == true && $value == "--jumps")
    {
        fwrite($file, $count_jumps . "\n");
    }
    if ( $labels == true && $value == "--labels")
    {
        fwrite($file, $count_labels . "\n");
    }
}
echo $domtree->saveXML();
?>   
