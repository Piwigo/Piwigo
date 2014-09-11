<?php
$lang['privacy_stp16'] = 'Som på din computer, kan du vælge flere albummer ved hjælp af tasterne  Shift og Control, og klik dernæst på pilen for at skifte dem.<br><br>Lad mig nu introducere grupperne.';
$lang['privacy_stp1'] = 'Hej! Jeg vil forklare dig, hvordan man beskytter sine billeder i Piwigo. Følg min vejledning, og klik på Næste (eller benyt pilene på dit tastatur) for at navigere. Hvis du skifter til en anden administrationsside, vil du blive sendt tilbage til udflugtens aktuelle side. Hvis du er kørt fast og ikke kan afslutte udflugten, vil udflugten blive afsluttet ved at skifte til <em>Plugins » Tag på en udflugt</em>.<br>Lad os komme i gang!';
$lang['privacy_stp10'] = 'Vi er i håndteringen af albummer, som er tilgængelig fra menuen ved hjælp af <em>Albummer » Håndtering</em><br><br>Rediger et album ved at lade musemarkøren svæve over det, og klik dernæst på Rediger.';
$lang['privacy_stp11'] = 'Klik nu på fanebladet Rettigheder';
$lang['privacy_stp12'] = 'På den side kan du vælge hvorvidt albummet skal være tilgængeligt for alle eller begrænset til nogle brugere';
$lang['privacy_stp13'] = 'Klik nu på privat.';
$lang['privacy_stp14'] = 'Herefter kan brugere og brugergrupper få tildelt adgang til et album.';
$lang['privacy_stp15'] = 'Før grupperne forklares, er her et professionelt tip: Her er linket til en side, som opsættes som privat/offentlig for flere albummer på en gang. Klik på det';
$lang['privacy_stp24'] = 'Udflugten er færdig.<p style="text-align:center">God fornøjelse med din Piwigo!</p>Hvis du kan lide Piwigo, og ønsker at sætte os, kan du bidrage ved at hjælpe med oversættelser, økonomiske bidrag, osv. <a href="http://piwigo.org/basics/contribute" target="_blank">Klik her for at støtte os</a>. Tak!';
$lang['privacy_stp22'] = '

<em>For avancerede brugere</em><br><br>I Piwigo kan du beskytte de oprindelige fotografier ved hjælp af din lokale opsætning. Anvend variablen $conf[\'original_url_protection\']: den er som standard tom, men kan opsættes til værdierne "images" for kun at beskytte billeder, eller "all" for også at beskytte alle former for medier, hvilket kan være ressourcekrævende eller det vil måske slet ikke fungere på din server.<br><br>Valgmulighedne fungerer på offentig tog privat indhold. I øjeblikket kræver valgmuligheden, at du nægter adgang til mapperne /upload og /galleries, ved at anvende en .htaccess-fil (normalt en tekstfil med "Deny from all" som indhold) eller via serveropsætningen.<br><br>Bemærk at filnavne på fotografier uploadet ved hjælp af andre metoder end ftp, er  <b>gjort tilfældige</b>, så de er umulige at gætte: filnavnet og stien til det oprindelige fotografi, er kun kendt hvis den besøgende har adgang til en udgave af billedet med en andne størrelse, så som et miniaturebillede. $conf[\'original_url_protection\'] og nægtelse af adgang til mapperne /upload og /galleries har til formål at forhindre den situation.
';
$lang['privacy_stp3'] = 'Brug et øjeblik på at læse oplysningerne herunder.';
$lang['privacy_stp4'] = 'Så vi har to systemer til håndtering af adgangsrettigheder til billederne. De er uafhængige, så du kan oprette en gruppe kaldet Familie, men den gruppe har intet at gøre med privatlivsniveauet Familie.<br><br>Privatlivsniveauerne gælder pr. billede, og gruppe-/brugerrettigheder gælder pr. album. Du kan anvende begge dele eller kun det ene system; det er fleksibelt.<br><br>Det næste faneblad handler om gruppehåndtering.';
$lang['privacy_stp5'] = 'Herunder forklares hvordan man håndterer grupper.<br>Disse rettigheder gælder kun adgang til billeder og albummer, når man kigger på den offentlige del eller ved hjælp af eksterne applikationer. Vi kigger senere på andre beskyttelsesmuligheder, men lad os nu øve os!';
$lang['privacy_title21'] = 'Offentlige fotografier';
$lang['privacy_title22'] = 'Lokal opsætning: Beskyttelse af original';
$lang['privacy_title24'] = 'Det har været en hyggelig udflugt';
$lang['privacy_title3'] = 'Rettigheder';
$lang['privacy_title4'] = 'Rettigheder';
$lang['privacy_title5'] = 'Rettigheder > Grupper';
$lang['privacy_title6'] = 'Privatlivsniveau';
$lang['privacy_title7'] = 'Privatlivsniveau';
$lang['privacy_title8'] = 'Privatlivsniveau';
$lang['privacy_title9'] = 'Privatlivsniveau';
$lang['privacy_stp6'] = 'Når du uploader billeder, kan du samme sted ændre privatlivsniveauet på de uploadede billeder...';
$lang['privacy_stp7'] = '... eller du kan gøre det senere, her i Batch Manager i Global tilstand.<br><br>Det er på siden <em>Fotografier » Nye fotografier</em>, så filteret "Seneste import" er opsat.';
$lang['privacy_stp8'] = 'Vælg et eller flere billeder...';
$lang['privacy_stp9'] = '... og vælg handlingen "Hvem kan se billederne?". Nu kan du ændre privatlivsniveauet på de valgte billeder.<br><br>Men du kan også gøre det for det specifikke billede, på dets redigeringsside. Du kan tilgå det fra den offentlige del, eller herfra.<br><br>Nu viser jeg dig det andet rettighedssystem, pr. album baseret på grupperne og brugerne.';
$lang['privacy_title1'] = 'Velkommen til privatlivsudflugten';
$lang['privacy_title10'] = 'Albumrettigheder';
$lang['privacy_title11'] = 'Albumrettigheder';
$lang['privacy_title12'] = 'Albumrettigheder';
$lang['privacy_title15'] = 'Tip';
$lang['privacy_title17'] = 'Grupper';
$lang['privacy_title18'] = 'Grupper';
$lang['privacy_title19'] = 'Brugere';
$lang['privacy_title2'] = 'Hjælp inde i Piwigo';
$lang['privacy_title20'] = 'Offentlige fotografier';
$lang['privacy_stp17'] = 'En gruppe i Piwigo er bare en samling brugere: Så grupper gør det lettere at håndtere albummers rettigheder, og håndtering af brugeres egenskaber. Her er siden, hvor du kan håndtere grupper, omdøbe, sammenlægge, kopiere og slette. Du kan også opsætte en eller flere grupper som "standardgruppe", hvilket betyder at nyligt registrerede brugere vil blive påvirket af de grupper, som er opsat som standard.';
$lang['privacy_stp18'] = 'For at tilføje eller fjerne brugere fra en gruppe, går man til siden <em>Brugere » Håndtering</em>';
$lang['privacy_stp19'] = 'Her kan man hurtigt redigere en enkelt bruger, ved at lade musemarkøren svæve over vedkommende og dernæst klikke på Redigering-linket.<br><br>Du kan vælge flere brugere og redigere dem på samme tid, ved at vælge en Handling, som skal udføres.';
$lang['privacy_stp2'] = 'Her kan du tilgå den integrerede hjælp. Klik på Næste for at fortsætte direkte til hjælpesiden om håndtering af rettigheder';
$lang['privacy_stp20'] = 'Nu ved du hvordan dine fotografier holdes private, men måske spekulerer du på, hvordan dine offentlige fotografier beskyttes. Først kunne du overveje at forhindre besøgende i at downloade fotografierne; det er ikke muligt, fordi web\'et ikke er indrettet sådan (den besøgendes browser downloader alle viste ressourcer, og flere til).  Højreklik kan slås fra, et tomt lag kan lægges oven på fotografiet osv, men det deaktiverer ikke download. Komplette websider kan gemmes af alle webbrowsere. ';
$lang['privacy_stp21'] = 'Nogle løsninger:<ul><li>tilføj et vandmærke, som minimum på fotografier i mellem og høj opløsning.</li><li>og deaktiver størrelserne XL og XXL.</li><li>samt deaktiver High Definition (download og visning af det oprindelige fotografi) ved de pågældende brugere.';
$lang['privacy_title14b'] = '';
$lang['privacy_stp14b'] = 'Vigtigt faktum: Webmasterne og administratorerne er ikke alvidende når de gennemse den offentlige del, men de kan tilgå ethvert album og fotografi gennem administrationsdelen.';
