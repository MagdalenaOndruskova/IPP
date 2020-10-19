<?php

 #
 # Name: lexsyntax_control.php
 # Caption: Project 1 of IPP  
 # Author: Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 # Date: 10.02.2020 
 #


#
# Function checks if header is in right format 
#
# @in $line first (not empty / commented) line of input
#
# @return OK if header is in right format 
# @exit   ERR_HEADER if there was any error with header 
#
function checkHeader( $line)
{
    $line = trim($line);
    if (preg_match('/^\.(ippcode20)$/i', $line) )
    {
        return OK;
    } 
    fprintf(STDERR, "Wrong format of header.");
    exit (ERR_HEADER);
}

#
# Function checks if the name of var is all-right
#
# @in $word name that is being checked
#
# @return ERR_LEX_SYX if the name is not a var
#         OK if everything is okay
#
function checkVar($word)
{
    # ^ $ anchors - make sure it matchs whole 
    if (preg_match('/^(GF|LF|TF)@[A-Za-z0-9_\-$&%\*!\?]*$/', $word))
    {
        ### checks if first character(s) after @ is number
        if (preg_match('/^(GF|LF|TF)@[0-9]+[A-Za-z\d_\-$&%\*!\?]*$/', $word))
        {
            return ERR_LEX_SYX;
        }
        return OK;
    }
    else 
    {
        return ERR_LEX_SYX;
    }
}

#
# Function checks if the name of constant is all-right
# also checking if after int@ is number, after bool@ is true/false and so on
#
# @in $word name that is being checked
#
# @return ERR_LEX_SYX if the name is not a constant
#         OK if everything is okay
#
function checkConst($word)
{
    $splited_word = preg_split('/@/', $word);
    if ( $splited_word[0] === "int")
    {
        if (preg_match('/^(\+|\-)?[0-9]+$/', $splited_word[1]))
        {
            return OK;
        }
        else 
        {
            return ERR_LEX_SYX;
        }
    }
    else if ($splited_word[0] === "bool")
    {
        if (preg_match('/^(true|false)$/', $splited_word[1]))
        {
            return OK;
        }
        else 
        {
            return ERR_LEX_SYX;
        }
    }
    else if ($splited_word[0] === "string")
    {
        if (preg_match("/(?!\\\\[0-9]{3})[\s\\\\#]/", $splited_word[1]))
        {
            return ERR_LEX_SYX;
        }
        else 
        {
            return OK;
        }
    }
    else if ($splited_word[0] === "nil")
    {
        if ($splited_word[1] === "nil")
        {
            return OK;
        }
        else 
        {
            return ERR_LEX_SYX;
        }
    }
    else 
    {
        return ERR_LEX_SYX;
    }
}

#
# Function checks if the name is constant or var
#
# @in $word name that is being checked
#
# @return ERR_LEX_SYX if the name isn't either of them
#         OK if name is constant or var
#
function checkSymbol($word)
{
    $checkVar = checkVar($word);
    $checkConst = checkConst($word);
    if (  $checkVar === OK || $checkConst === OK)
    {
        return OK;
    }
    else 
    {
        return ERR_LEX_SYX;
    }
}

#
# Function checks if the name of label is all-right
#
# @in $word name that is being checked
#
# @return ERR_LEX_SYX if the name is not a label
#         OK if everything is okay
#
function checkLabel($word)
{
    if (preg_match('/[^[A-Za-z0-9_\-$&%\*!\?]]*/', $word))
    {
        return ERR_LEX_SYX;
    }
    else 
    {
        return OK;
    }
}

#
# Function checks if Opcode is all-right and generates XML 
#
# @in $word  array of line, one element of array = one word (splitted by white spaces)
#
# @return ERR_LEX_SYX if there was lexical/syntax error
#         ERR_OPERATOR_CODE if operator code doesn't exist 
#         OK if everything is okay
#
function checkOpcode($word)
{
    global $count_locs;
    global $count_labels;
    global $count_jumps;
    global $domtree; 
    global $xmlRoot;
    $count_locs++;
    # opcode to uppercase 
    $word[0] = strtoupper($word[0]);
    $xmlInstruction = $domtree->createElement("instruction");
    $xmlInstruction->setAttribute("order", $count_locs);
    $xmlInstruction->setAttribute("opcode", $word[0]);
    switch ($word[0])
    {
        # OP code: OPCODE 
        #instructions without var/symb, so not generating anything else 
        case "CREATEFRAME":
        case "PUSHFRAME":
        case "POPFRAME":   
        case "RETURN":
        case "BREAK":  
            $max= max(array_keys($word));
            # on line is not only opcode
            if ( $max  !== OPCODE)
            {  
                return ERR_LEX_SYX;
            }
            if ( $word[0] === "RETURN")
            {
                $count_jumps++;
            }
            $xmlRoot->appendChild($xmlInstruction);
            return OK;
        # opcode: OPCODE <var>
        case "DEFVAR":
        case "POPS":
            $max= max(array_keys($word));
            if ( $max !== OPCODE_VAR)
            {
                return ERR_LEX_SYX;
            }
            $checkVariable = checkVar($word[1]);
            if ($checkVariable === OK)
            {
                $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($word[1]));
                $xmlArg1->setAttribute("type", "var");
                $xmlInstruction->appendChild($xmlArg1);
                $xmlRoot->appendChild($xmlInstruction);
                return OK; 
            }
            else 
            {
                return ERR_LEX_SYX;
            }
        #opcode: OPCODE <var> <symb1> <symb2>
        case "ADD":
        case "SUB":
        case "MUL":
        case "IDIV":
        case "LT":
        case "GT":
        case "EQ":
        case "AND":
        case "OR":
        case "STRI2INT":
        case "CONCAT":
        case "GETCHAR":
        case "SETCHAR":
            $max= max(array_keys($word));
            if ( $max !== OPCODE_VAR_SYMB1_SYMB2 )
            {
                return ERR_LEX_SYX;
            }
            $var = checkVar($word[1]);
            $symb1 = checkSymbol($word[2]);
            $symb2 = checkSymbol($word[3]);                
            if ( $var === OK && $symb1 === OK && $symb2 === OK)
            {
                $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($word[1]));
                $xmlArg1->setAttribute("type", "var");
                
                $isConstant = checkConst($word[2]);
                if ( $isConstant === OK )
                {
                    $constant = preg_split('/@/',$word[2]);
                    $xmlArg2 = $domtree->createElement("arg2",htmlspecialchars($constant[1]));
                    $xmlArg2->setAttribute("type", $constant[0]);
                }
                else 
                {
                    $xmlArg2 = $domtree->createElement("arg2",htmlspecialchars($word[2]));
                    $xmlArg2->setAttribute("type", "var");
                }

                $isConstant = checkConst($word[3]);
                if ( $isConstant === OK )
                {
                    $constant = preg_split('/@/',$word[3]);
                    $xmlArg3 = $domtree->createElement("arg3",htmlspecialchars($constant[1]));
                    $xmlArg3->setAttribute("type", $constant[0]);
                }
                else 
                {
                    $xmlArg3 = $domtree->createElement("arg3",htmlspecialchars($word[3]));
                    $xmlArg3->setAttribute("type", "var");
                }

                $xmlInstruction->appendChild($xmlArg1);
                $xmlInstruction->appendChild($xmlArg2);
                $xmlInstruction->appendChild($xmlArg3);
                $xmlRoot->appendChild($xmlInstruction);
                return OK;
            }
            else
            {
                return ERR_LEX_SYX;
            }
        #opcode: OPCODE <var> <symb> 
        case "NOT":
        case "MOVE":
        case "INT2CHAR":
        case "STRLEN":  
        case "TYPE":  
            $max= max(array_keys($word));
            if ( $max !== OPCODE_VAR_SYMB)
            {
                return ERR_LEX_SYX;
            }
            $var = checkVar($word[1]);
            $symb = checkSymbol($word[2]);                
            if ( $var === OK && $symb === OK)
            {
                $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($word[1]));
                $xmlArg1->setAttribute("type", "var");
                $isConstant = checkConst($word[2]);
                if ( $isConstant === OK )
                {
                    $constant = preg_split('/@/',$word[2]);
                    $xmlArg2 = $domtree->createElement("arg2",htmlspecialchars($constant[1]));
                    $xmlArg2->setAttribute("type", $constant[0]);
                }
                else 
                {
                    $xmlArg2 = $domtree->createElement("arg2",htmlspecialchars($word[2]));
                    $xmlArg2->setAttribute("type", "var");
                }
                $xmlInstruction->appendChild($xmlArg1);
                $xmlInstruction->appendChild($xmlArg2);
                $xmlRoot->appendChild($xmlInstruction);
                return OK;
            }
            else 
            {
                return ERR_LEX_SYX;
            }
        #opcode: OPCODE <symb>
        case "PUSHS":
        case "WRITE":
        case "EXIT":
        case "DPRINT":
            $max= max(array_keys($word));
            if ( $max !== OPCODE_SYMB)
            {
                return ERR_LEX_SYX;
            }
            $symb = checkSymbol($word[1]);              
            if ( $symb === OK)
            {
                $isConstant = checkConst($word[1]);
                if ( $isConstant === OK )
                {
                    $constant = preg_split('/@/',$word[1]);
                    $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($constant[1]));
                    $xmlArg1->setAttribute("type", $constant[0]);
                }
                else 
                {
                    $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($word[1]));
                    $xmlArg1->setAttribute("type", "var");
                }
                $xmlInstruction->appendChild($xmlArg1);
                $xmlRoot->appendChild($xmlInstruction);
                return OK;
            }
            else 
            {
                return ERR_LEX_SYX;
            }
        #opcode: OPCODE <label>
        case "LABEL": 
        case "JUMP":
        case "CALL":
            $max= max(array_keys($word));
            if ( $max !== OPCODE_LABEL)
            {  
                return ERR_LEX_SYX;
            }                
            $label = checkLabel($word[1]);
            if ( $label === OK)
            {
                $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($word[1]));
                $xmlArg1->setAttribute("type", "label");
                $xmlInstruction->appendChild($xmlArg1);
                $xmlRoot->appendChild($xmlInstruction);
                if ( $word[0] === "LABEL")
                {
                    $count_labels++;                        
                }
                if ( $word[0] === "JUMP" || $word[0] === "CALL")
                {
                    $count_jumps++;                        
                }
                return OK;
            }
            else 
            {
                return ERR_LEX_SYX;
            }
        #opcode: OPCODE <label> <symb1> <symb2>
        case "JUMPIFEQ":
        case "JUMPIFNEQ":
            $max= max(array_keys($word)); 
            if ( $max !== OPCODE_LABEL_SYMB1_SYMB2)
            {            
                return ERR_LEX_SYX;
            }
            $label = checkLabel($word[1]);
            $symb1 = checkSymbol($word[2]);
            $symb2 = checkSymbol($word[3]);
            if ( $label === OK && $symb1 === OK && $symb2 === OK)
            {
                $xmlArg1 = $domtree->createElement("arg1",htmlspecialchars($word[1]));
                $xmlArg1->setAttribute("type", "label");

                $isConstant = checkConst($word[2]);
                if ( $isConstant === OK )
                {
                    $constant = preg_split('/@/',$word[2]);
                    $xmlArg2 = $domtree->createElement("arg2",htmlspecialchars($constant[1]));
                    $xmlArg2->setAttribute("type", $constant[0]);
                }
                else 
                {
                    $xmlArg2 = $domtree->createElement("arg2", htmlspecialchars($word[2]));
                    $xmlArg2->setAttribute("type", "var");
                }

                $isConstant = checkConst($word[3]);
                if ( $isConstant === OK )
                {
                    $constant = preg_split('/@/',$word[3]);
                    $xmlArg3 = $domtree->createElement("arg3",htmlspecialchars($constant[1]));
                    $xmlArg3->setAttribute("type", $constant[0]);
                }
                else 
                {
                    $xmlArg3 = $domtree->createElement("arg3",htmlspecialchars($word[3]));
                    $xmlArg3->setAttribute("type", "var");
                }

                $xmlInstruction->appendChild($xmlArg1);
                $xmlInstruction->appendChild($xmlArg2);
                $xmlInstruction->appendChild($xmlArg3);
                $xmlRoot->appendChild($xmlInstruction);
                $count_jumps++;
                return OK;
            }
            else 
            {
                return ERR_LEX_SYX;
            }
        #opcode: OPCODE <var> <type>
        case "READ":
            $max= max(array_keys($word)); 
            if ( $max !== OPCODE_VAR_TYPE)
            {
                return ERR_LEX_SYX;
            }
            $var = checkVar($word[1]);
            $type = preg_match('/^(int|bool|string)$/', $word[2]);
            if ($var === OK && $type === 1)
            {
                $xmlArg1 = $domtree->createElement("arg1", htmlspecialchars($word[1]));
                $xmlArg1->setAttribute("type", "var");

                $xmlArg2 = $domtree->createElement("arg2", htmlspecialchars($word[2]));
                $xmlArg2->setAttribute("type", "type");

                $xmlInstruction->appendChild($xmlArg1);
                $xmlInstruction->appendChild($xmlArg2);
                $xmlRoot->appendChild($xmlInstruction);
                return OK;
            }
            else 
            {
                return ERR_LEX_SYX;
            }
        default: 
            return ERR_OPERATOR_CODE;
    }
}

#
# Function loads line of STDIN 
#
# @return ERR_LEX_SYX if the name is not a var
#         OK if everything is okay
#
function checkCode()
{
    $firstline = true; 
    global $count_comments;
    # loads from stdin line after line 
    while ($line = fgets(STDIN))
    {
        # deleting comments 
        $comment_line =strstr($line, '#', True);
        if ( $comment_line !== FALSE)
        {
            $count_comments++;
            $line = $comment_line . PHP_EOL;
        }
        # skipping empty line 
        if ( preg_match('/^\s*$/', $line) )
        {
            continue;
        }        
        # controling header
        if ($firstline)
        {
            $header = checkHeader($line); 
            if ($header == ERR_HEADER)
            {
                exit(ERR_HEADER);
            }
            $firstline = false ;
            continue;
            
        }
        #not empty line
        $line = trim($line);
        $opcode = preg_split('/[\s]/', $line,-1, PREG_SPLIT_NO_EMPTY);
        $result = checkOpcode($opcode); 
        if ( $result === ERR_LEX_SYX || $result === ERR_OPERATOR_CODE)
        {
            exit ($result);
        }      
    }
    return OK;
}

?>
