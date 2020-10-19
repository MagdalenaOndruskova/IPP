## Implementačná dokumetácia k 1. úlohe do IPP 2019/2020
**Meno a priezvisko:** Magdaléna Ondrušková
**Login:** xondru16

### Parse.php
Skript **parse.php** je implementovaný pomocou hlavného kódu, ktorý sa nachádza v  súbore parse.php a takisto pomocou skriptu **lexsyntax_control.php**, ktorý slúži na implementáciu lexikálnej a syntaktickej kontroly vstupného kódu v jazyku IPPcode20 a takisto na generovanie výstupného XML. Každý skript je implementovaný pomocou vlastných funkcií, ktoré sú okomentované. Vstupný kód je analyzovaný riadok za riadkom a v hlavnom skripte je volaná funkcia **checkCode()** ktorá riadi celú syntaktickú a lexikálnu kontrolu. 
Kontrola argumentov, s ktorými bol skript spustený je prevádzaná vrámci skriptu parse.php, vrámci ktorého sa nastavujú premenné pre rozšírenie na true, ak bol zadaný daný argument pre rozšírenie.  
V rámci implementácie parse.php prevádzam už aj typovú kontrolu pri konštantách, či sa jedná o správne zadanú dvojcu [typ, názov], napr. ak sa jedná o int, či je naozaj v tvare int@číslo. 
Komentáre spracovávam tak, že vždy daný vstupný riadok skontrolujem, či sa tam nachádza znak **#**, ak áno, všetko za týmto znakom sa odstráni (vrátane #). Následne pri kontrole operačného kódu sledujem, či je na riadku presne toľko slov, ako daný operačný kód vyžaduje. Ak natrafím na prázdny riadok, ukončujem danú iteráciu while-u a pokračujem na načítanie ďalšieho riadku. 
### Rozšírenie:
V rámci riešenia som implementovala aj rozšírenie **STATP**. Pri načítávaní argumentov vrámci tohto rozšírenia, ak sa v argumentoch zopakuje dvakrát argument **-\-stats=file**, skript parse.php sa ukončí s chybovým kódom 10. 




