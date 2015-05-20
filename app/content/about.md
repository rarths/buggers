# Buggers Project

Jag valde att skapa en site likt Stackoverflow men bytte ut frågor och svar mot "buggrapporter" samt att istället för att svara på en fråga kan man tillföra information till rapporten.

Frågorna kallar jag reports och svaren extends. Både reports och extends skrivs i Markdown samt går att kommentera.

## Krav 1-3
Siten uppfyller de grundkrav som ställs på projektet. 
Då funktionaliteten för siten i stort sett redan fanns tillgänglig blev startupen att integrera och anpassa de moduler som skapats under kursens gång.

## Krav 4 
Varje report och extend kan röstas upp eller ner av alla användare förutom skaparen själv. Reports ordnas efter datum som standard men kan även ordnas efter antal extends eller röster. Reports hämtas från datbasen via en nestlad SQL fråga som räknar total antal extend och röster samt inkluderar information om användaren som publicerade report. SQL frågan räknar även användarens  totala antal gjorda reports.

## Krav 5

## Krav 6

Ett tidigt problem jag upptäckte i projeket var beslutet om data från databasen skulle hämtas via nestlade SQL strängar eller via separata metoder som bygger upp varje objekt (report eller extend) för att till sist skicka den till en vy och skriva ut den på sidan. 
Jag började projektet med alternativ två, skapa metoder och bygga objekten individuellt för varje fall då objektet skulle visas. Det visade sig ganska snabbt att antalet metoder i projektet blev allt för många och något för statiska. För att inte fastna i ett fullständigt kaos fick jag tänka om till alternativ två, nestlade SQL strängar.
Nu kunde alla värden som behövdes hämtas direkt från databasen i en och samma sträng. Lösningen är inte den vackraste med den gör sitt jobb med betydligt mindre kod.

Då jag ville utnyttja CForm för att hantera fälten för kommentarerna skapade jag en egen  kommentar-controller som skulle hantera detta. Ett problem som dök upp iom att samma CForm fält upprepas för varje kommentarfält är att submit-knappen får samma namn för alla fälten. Detta leder till att enbart data för första kommentaren kan skickas vidare. 
För att kunna skicka data från ett specifikt fält behöver submit-knappens namn vara unik. Det finns stöd i CForm för att ändra fältens namn genom att definiera 'name' till fälten. Stödet finns men skrivs av nån anledning alltid över av 'elementets namn'.

```
'submit' => [   <---- Elementets namn
    'type'          => 'submit',
    'value'         => 'Comment',
    'name'          => 'submit-' . $id, <---- Unikt namn för fältet
    'callback'      => function ($form)  { }
```

Fältets namn kommer med andra ord alltid vara det namn man anger som arrayens plats dvs 'elementets namn'. Ett annat försök att få submit unikt var att göra fältets namn unikt.

```
"submit-{$id}" => [   <---- Elementets namn
    'type'          => 'submit',
    'value'         => 'Comment',
    'callback'      => function ($form)  { } 
```

Detta löser problemet med fältets namn som nu blir unikt men skapar istället ett annat problem som gör, av nån anledning, att det skrivs ut +1 knapp för varje kommentarsfält så kommentar 5 i listan har nu 5 submit-knappar......  

Då jag inte kunde hitta något fel i min egen lösning fann jag tillslut problemet i CForm.
När CForm instansieras så tar konstruktorn emot namnet på elementet samt en array av attribut (de definierade fälten som görs med CForm->create())

```
/**
* Constructor
*
* @param string name of the element.
* @param array attributes to set to the element. Default is an empty array.
*/
public function __construct($name, $attributes=array()) {
	$this->attributes = $attributes; 
	$this['name'] = $name;
	//$this['key'] = $name;
	//$this['name'] = isset($this['name']) ? $this['name'] : $name;
	if(is_callable('CLydia::Instance()')) {
	
	$this->characterEncoding = CLydia::Instance()->config['character_encoding'];
	} else {
		$this->characterEncoding = 'UTF-8';
	}
}
```

Det är här problemet uppstår. 
$this['name'] refererar till fältets namn men skrivs istället direkt över till elementets namn.
Så när GetHTML() körs så kommer fältets namn alltid vara samma som elementet

```
$name = " name='{$this['name']}'";
```

Kollar man i konstruktorn så finns där ett försök till att lösa problemet, dock bortkommenterat.

```
//$this['name'] = isset($this['name']) ? $this['name'] : $name;
```

Detta löser problemet med att kunna sätta fältets namn men skapar såklart andra problem. Så en tillfällig lösning för att få CForm fungera gjordes såhär

```
$this->attributes = $attributes;
$this['element-name'] = $name;
//$this['key'] = $name;
$this['name'] = isset($this['name']) ? $this['name'] : $name;
```

Sätter fältets namn om det anges men sparar även undan elementets namn.
Metoden getElementId() behövdes nu också justeras med korekt namn... 
...och när jag ändå höll på så skapade jag möjligheten att undvika alla störiga <br>
som sätts mellan label och fältet

```
$break  = isset($this['break']) ? ($this['break'] == true ? "<br/>" : null) : "<br/>";
```
och använder sen {$break} för alla <br/>..

Efter detta gick projektet mer eller mindre smärtfritt att få ihop. 
Jag funderade tidigt på att försöka mig på ett ORM för lättare kunna jobba med relationerna i databasen, vilket jag verkligen ångrade i slutet.

Kursen var i övrigt bra. Dock så hade jag gärna sett mer fokus på själva kärnan i MVC för att förstå bättre hur alla delarna hänger ihop. Trots all den tid som lagts på projektet och dektetivarbetet på kryptiska felmeddelanden så har jag fortfarande inte full förståelse för hur alla bitarna sitter ihop.
Jag hade också gärna sett mer jämförelser med andra populära ramverk för att få ännu bredare förståelse. Att jobba med Anax har fungerat bra men hade gärna utforskat några alternativ.