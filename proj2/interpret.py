"""
Name: interpret.py
Caption:  Interpret for IPPcode20
Author: Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
Date: 27.03.2020
"""
from intlib.progArgs import ProgArgParse
from intlib.processXML import XMLparser


# Process program arguments #
program_arg = ProgArgParse(add_help=False)
xml_source, input_source, stats = program_arg.add_arg()

# Starts parsing xml and interpretation of code #
xml = XMLparser(xml_source, input_source)
xml.parse(stats)




