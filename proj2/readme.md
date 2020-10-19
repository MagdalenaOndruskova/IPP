## Implementačná dokumetácia k 2. úlohe do IPP 2019/2020

**Meno a priezvisko:** Magdaléna Ondrušková

**Login:** xondru16 <xondru16@stud.fit.vutbr.cz>

## Interpret.py 
Skript **interpret.py** je realizovaný objektovo, pomocou hlavného skriptu **interpret.py** a pomocných skriptov nachádzajúcich sa v priečinku **intlib**. V intlib sa nachádza hneď niekoľko skriptov, ktorý každý implementuje jednu triedu. 
Skript **error.py** definuje niekoľko podtried triedy Exception pre jednotlivé chybové kódy. Hlavná trieda **Error** pokrýva vypísanie všetkých chybových hlášok a ukončenie programu so správnym návratovým kódom. 
Skript **progArgs.py** definuje triedu **ProgArgsParse(arg.ArgumentParser)** je podtrieda triedy ArgumentParser z knižnice arg. Táto trieda kontroluje parametre programu a získava vstupné súbory pre parsovanie XML a pre inštrukciu READ, prípadne pre výpis štatistík. 
Skript **processXML.py** definuje triedu **XMLParser**. Táto trieda spolu s knižnicou **xml.etree.ElementTree** kontroluje zadaný XML súbor (či už paramaterom --source alebo pomocou stdin) jeho formátovanie, jeho syntaktickú a lexikálnu správnosť, pre ktorú je voľaná funkcia **check_instruction(opcode, arguments)** z triedy **Instruction**. 
Skript **instruction.py** definuje triedu **Instruction**. Jednotlivé operačné kódy sú rozdelené do skupín, podľa ktorých sa kontroluje správny počet a typ argumentov pomocou jednotlivých pomocných funkcií. 
Skript **frames.py** definuje triedu **Frames**, ktorá zastrešuje všetky rámce a zásobník rámcov vrámci IPPcode20. Nachádzajú sa tu podporné funkcie na získavanie premennej z daného rámca, pridanie premennej do rámca a pridanie rámca na zásobník rámcov a takisto aj získanie rámca zo zásobníka rámcov. 
Skript **variables.py** definuje triedu **Variables**, ktorá ma na starosti premenné v programe. Definuje aj pomocné funkcie na overenie typu danej premennej. 
Posledným a najdôležitejším skriptom je **run_code.py** s triedou **Interpret**, ktorá riadi celú interpretáciu kódu. Na začiatok sa prejde celý kód ešte raz a zapíšu sa do listu dvojce [pozícia, náveštie], aby nevznikali problémy pri následnom riadení toku programu. Následne sa začne celý kód od začiatku prechádzať znovu, volá sa funkcia, ktorá zistí o akú inštrukciu ide a následne zavolá funkciu, ktorá danú inštrukciu vykoná. 

## Test.php 
Skript **test.php** je inicializovaný pomocou hlavného skriptu **test.php** a pomocných skriptov, ktoré sa nachádzajú v súbore **testlib**, ktoré sú volané z hlavného skriptu.
Skript **test_default_settings.php** rieši východzie nastavenie premenných, ako sa majú správať, ak chýba nejaký konkrétny argument skriptu. 
Skript **test_arguments** kontroluje programové argumenty a prípadne upravuje premenné, ktoré dané argumenty reprezentujú. 
Skript **test_control** spúšťa jednotlivé testy. Na základe zadaných argumentov prechádza buď aktuálny priečinok alebo všetky podpriečinky zadaného argumentu, hľadá .src súbory, hľadá zvyšné súbory k danému testu (.rc, .in., .out) a ak neexistujú, sú vytvorené. Následne, na základe obsahu premenných --int-only a --parse-only sa rozhoduje, ktoré testovanie sa vykoná - ak sú obe premenné TRUE, testuje sa aj parser aj interpret, ak je TRUE iba jedno, testuje sa ten skript, pre ktorého je argument TRUE. 
Posledný skript **test_html** slúži na vygenerovanie výslednej HTML stránky spolu s výsledkami testov. V tejto HTML stránke sa nachádza údaj o celkovom počte testov, o počte chybných a úspešných testov. Následne sa tam nachádza prehľadná tabuľka všetkých testovaných priečinkov spolu s celkovým počtom testov, úspešných a chybných testov z daného priečinku. A následne, za touto tabuľkou sa nachádzajú tabuľky pre jednotlivé priečinky, kde je názov testu a jeho výsledok - či prešiel alebo nie.

### Rozšírenie 
V rámci skriptu **interpret.py** sú implementované aj nasledujúce rozšírenia: **STATI** , **STACK**, **FLOAT**. Vrámci implementácie rozšírenia **STACK** som implementovala aj rozširujúce zásobníkové inštrukcie, ktoré vznikli rozšírením **FLOAT** ako je **INT2FLOATS**, **FLOAT2INTS** a **DIVS**. Takisto aj zvyšné zásobnikové inštrukcie sú prispôsobené na prácu s dátovým typom float. Štatistiky z rozšírenia **STATI** sa vypíšu aj v prípade inštrukcie EXIT, ale len ak je návratový kód 0. 
