<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2014 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+
$lang['privacy_stp9'] = '...og velg handlingen "Hvem kan se bildene?". Nå kan du endre personvernnivå på de valgte bildene.<br><br> Men du kan også gjøre det for et bestemt bilde, på sin redigerings side. Du kan få tilgang til det fra den offentlige delen, eller herfra.<br><br>Nå vil jeg vise deg den andre måten av tillatelse per album basert på grupper og brukere.';
$lang['privacy_stp8'] = 'Velg ett eller flere bilder...';
$lang['privacy_stp7'] = '... eller du kan gjøre det senere, her i Batch Behandler i Global Mode.<br><br>Dette er siden <em>Bilder »Siste bilder</em>, så filteret"Siste import" blir satt.';
$lang['privacy_title6'] = 'Personvern Nivå ';
$lang['privacy_title7'] = 'Personvern Nivå';
$lang['privacy_title8'] = 'Personvern Nivå';
$lang['privacy_title9'] = 'Personvern Nivå';
$lang['privacy_stp6'] = 'Når du laster opp bildene, kan du endre personvernnivået på de opplastede bildene med det samme...';
$lang['privacy_stp5'] = 'Nedenfor er det forklart hvordan du kan administrere grupper. <br>Disse tillatelser er bare for tilgangen av bildene og albumene, når du surfer på den offentlige delen eller for de eksterne applikasjoner. Vi vil senere se andre beskyttelser, men la oss nå øve!';
$lang['privacy_stp4'] = 'Vi har altså to systemer for administrere tilgangs tillatelser til bildene. De er uavhengige, slik at du kan opprette en gruppe som heter Familie, men denne gruppen har ingenting å gjøre med personvernnivå Familie.<br><br>Personvernnivået blir anvendt på hvert bilde, og gruppe/brukertillatelser anvendes på hvert album. Du kan bruke begge eller bare ett system, det er fleksibelt.<br><br>Neste kategori er om gruppe managment.';
$lang['privacy_stp3'] = 'Ta deg tid til å lese informasjonen nedenfor.';
$lang['privacy_stp24'] = 'Denne turen er over.<p style="text-align:center">Nyt Din Piwigo!</p>Hvis du liker Piwigo og ønsker å støtte oss, kan du bidra ved å hjelpe med oversettelser, donere, etc. <a href="http://piwigo.org/basics/contribute" target="_blank">Klikk her for å støtte oss</a>. Takk!';
$lang['privacy_stp22'] = '<em> For avanserte brukere</em><br><br> I Piwigo, kan du beskytte det originale bilde ved hjelp av din lokale konfigurasjon. Bruk variabelen $conf[\'original_url_protection\']: denne er tom som standard, setter du verdien til "bilder"  beskytter du bare bilder eller setter du til "alle" som da beskytte alle typer medier, dette kan være meget ressurs krevende eller kanskje det bare ikke vil virke på serveren din. <br> Dette alternativet fungerer for offentlig og privat innhold. Dette alternativet krever at du nekter adgang til mapper/opplasting og /gallerier, ved bruk av en .htaccess-file (vanligvis en tekstfil med "Ingen adgang" til dette innhold) eller serverkonfigurasjoner.<br><br> Vennligst vær oppmerksom på at filnavnene til bilder lastet opp med andre metoden enn FTP blir<b><b>blandet</b>, slik at det er umulig å gjette: filnavn, og banen til det originale bildet kun kan bli kjent hvis den besøkende har tilgang til en skalert versjon av bildet, som f.eks miniatyrbilde. $conf[\'original_url_protection\']Altså å nekte tilgang til mapper/opplasting og /gallerier er meningen å ungå i dette tilfelle.';
$lang['privacy_stp21'] = 'Løsningen er:.<Ul><li>sette inn et vannmerke, i hvert fall på medium og high definerte bilder</li><li>, deaktivere XL og XXL størrelser</li><li>og deaktivere High Definition (nedlasting. og visning av det originale bildet) for de berørte brukere.';
$lang['privacy_stp20'] = 'Nå vet du hvordan du skal holde bildene dine private, men du lurer kanskje på hvordan du kan beskytte dine offentlige bilder. Du kan først tenke på å blokkere den besøkende til å laste ned bilder: det kan du ikke, fordi nettet har blitt skapt på den måten (nettleseren til den besøkende laste ned alle ressursene so vises og mere til). Høyreklikk kan deaktiveres, et tomt lag kan legges på toppen av bildet osv, men det vil ikke deaktivere nedlastingen. Fullstendige nettsider kan bli lagret av hvilken som helst nettleser.';
$lang['privacy_stp2'] = 'Her kan du få tilgang til den integrerte Hjelp. Klikk Neste for å fortsette direkte til hjelpesiden som omhandler behandling av Rettigheter';
$lang['privacy_stp19'] = 'Her kan du raskt redigere en enkelt bruker ved å peke på det og klikke på koblingen Rediger.<br><br> Du kan velge flere brukere og redigere dem på en gang ved deretter å velge en handling som skal gjelde.';
$lang['privacy_stp17'] = 'En gruppe i Piwigo er bare et sett med brukere: så grupper gjør det enklere å administrere album tillatelser, og å administrere brukeregenskaper. Her er siden hvor du kan behandle grupper, dvs. endre navn, flette, duplisere, slette dem. Du kan også lagre en eller flere grupper som "standardgruppe" som betyr at nyregistrerte brukere vil bli knytte til disse standardgrupper.';
$lang['privacy_stp18'] = 'For å administrere assosiasjoner mellom brukere til grupper, gå til<em>Brukere »Behandle</em> siden';
$lang['privacy_stp16'] = 'Som på datamaskinen, kan du velge flere album med tastene Shift og Control, klikk deretter på pilene for å bytte mellom Privat og Offentlig.<br><br>Nå,la meg presentere gruppene.';
$lang['privacy_stp15'] = 'Før vi går i gang med å forklare grupper, her er et pro tips: her er linken til en side der du kan sette privat/offentlig tillatelse til flere albumer på en gang. Klikk på den';
$lang['privacy_stp14b'] = 'Viktig faktum: på offentlige sider, gjelder tillatelser for webmastere og administratorer som for andre brukere. I administrasjonen, har de tilgang til alle album eller bilder.';
$lang['privacy_stp13'] = 'Nå klikk på Privat';
$lang['privacy_stp14'] = 'Her kan brukere eller grupper av brukere gis tilatelse for å få tilgang til albumet.';
$lang['privacy_stp12'] = 'På den siden kan du velge om albumet vil være tilgjengelig for alle, eller kun til noen begrenset brukere';
$lang['privacy_stp11'] = 'Nå klikk på knappen Tillatelser';
$lang['privacy_stp10'] = 'Vi er i Album listens administrasjon som er tilgjengelig fra menyen ved å klikke<em>Album »Behandle</em><br><br>Rediger albumet ved å holde musen over den, og klikk deretter på Endre.';
$lang['privacy_stp1'] = 'Hei! Jeg vil være din guide som viser deg hvordan du kan beskytte dine bilder i Piwigo. Følg mine instruksjoner, og klikk på Neste (eller bruk pilene på tastaturet) for å navigere. Hvis du går til en annen side i administrasjonen, vil du bli omdirigert til den gjeldende siden av omvisningen. Hvis du står fast og ikke kan  avslutte turen, gå til <em>Tillegsprogrammer »ta en omvisning</ em> dette vil avslutte omvisningen. <br> La oss begynne!';