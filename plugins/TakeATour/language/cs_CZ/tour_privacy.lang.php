<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2015 Piwigo Team                  http://piwigo.org |
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
$lang['privacy_stp22'] = '<em>pro pokročilé uživatele</em><br>V Piwigo, můžete chránit původní fotografie pomocí místní konfigurace. Pomocí proměnné $conf[\'original_url_protection\']: ve výchozím nastavení prázdné, nastavte hodnotu na "images" a tím nastavíte ochranu pouze fotografiím nebo hodnotu "all" a tím chráníte i všechny ostatní typů médií na serveru. <br> Tato volba funguje pro veřejný i soukromý obsah. Tato možnost v současné době vyžaduje, abyste odepřeli dostupnost do složky /upload a /galeries, pomocí .htaccess souboru (obvykle textový soubor s "Deny from all" pro obsah) nebo konfigurací serveru. <br> Prosím berte na vědomí, že názvy souborů fotek nahraných za použití jiné metody než FTP jsou <b>náhodné</b>, takže je nemožné odhadnout: že název souboru, a tak i cesta k originální fotografii může být zjištěna až v případě, že návštěvník má přístup ke změněné verzi této fotky, podobně jako u miniatur. $conf[\'original_url_protection\'], a popírat přístup ke složkám /upload a /galeries má za účel zabránit této věci.';
$lang['privacy_stp4'] = 'Takže tu máme dva systémy pro správu přístupových oprávnění na fotografiích. Jsou nezávislé, takže si můžete vytvořit skupinu s názvem Rodina, ale skupina nemá nic společného s úrovní ochrany soukromí rodiny. <br> Hladiny soukromí jsou aplikovány na fotografie, skupiny / uživatelská oprávnění jsou použity pro každé album. Můžete používat oba nebo jen jeden systému je flexibilní. <br> <br> Další stránka je o správě skupin.';
$lang['privacy_stp24'] = 'Tento průvodce je u konce. <p style="text-align:center"> Užijte si Piwigo! </p> Pokud se vám líbí Piwigo a chcete nás podpořit, můžete přispět tím, že pomáháte s překlady, darem, atd. <A href="http://piwigo.org/basics/contribute" target="_ blank"> Klikněte zde pro podpoření </a>. Díky!';
$lang['privacy_stp20'] = 'Nyní víte, jak udržet své fotky v soukromí, ale můžete uvažovat o tom, jak chránit své veřejné fotografie. Možná přemýšlíte o blokování návštěvníka aby nemohl stáhnout fotografii: nemůžete, protože web byl vytvořen tímto způsobem (prohlížeč návštěvníka stáhne všechny prostředky zobrazují a mnohé další). Klepnutí pravým tlačítkem myši lze zakázat, prázdná vrstva může být zobrazena přes fotografie a tak dále, ale nelze zakázat stahování. Kompletní webové stránky mohou být uloženy libovolným webovým prohlížečem.';
$lang['privacy_stp10'] = 'Jsme v seznamu alb který je k dispozici v nabídce pomocí <em>Alba » Správa</em><br><br> Upravte album tak že na něj najedete myší a potom klikněte na Upravit.';
$lang['privacy_stp9'] = '... a vyberte akci "Kdo může vidět fotografie?". Nyní můžete změnit úroveň ochrany vybraných fotografií. <br>, Ale můžete to také udělat pro konkrétní fotografii na její editační stránce. K ní máte přístup z veřejné části, nebo tady. <br> <br> Teď jsme vám ukázali jiný systém práv na alba na základě uživatelů a skupin uživatelů.';
$lang['privacy_stp7'] = '... nebo to můžete udělat později, tady v dávkovém spracování v globálním režimu. <br> Toto je stránka <em> Fotografie »Poslední fotografie </ em>, takže filtr je nastaven jako "poslední import".';
$lang['privacy_stp5'] = 'Níže je vysvětleno, jak lze spravovat skupiny. <br> Tato oprávnění jsou pouze pro přístup fotografiím a albům, při procházení ve veřejné části, nebo pro externí aplikace. Později uvidíme další ochranu, ale pojďme teď k praktické části!';
$lang['privacy_stp17'] = 'Skupina v Piwigo je jen sada uživatelů: tak skupiny usnadňují správu oprávnění alb, a správu vlastnosti uživatelů. Zde je stránka, kde můžete spravovat skupiny, tj přejmenovat, sloučit, duplikovat, odstranit je. Můžete také nastavit jednu nebo více skupin jako "výchozí skupina", což znamená, že nově registrovaní uživatelé budou přiřazeni těmto výchozím skupinám.';
$lang['privacy_stp16'] = 'Stejně jako na vašem počítači, můžete si vybrat několik alb pomocí tlačítka Shift a Control, pak klikněte na šipku k jejich přepnutí. <br> A teď nám dovolte, abychom představili skupiny.';
$lang['privacy_stp19'] = 'Zde můžete rychle upravit uživatele tím že přejedete myší nad ním a pak klikněte na odkaz Upravit. <br> <br> Můžete vybrat více uživatelů a upravovat je najednou a pak výběrem změnu použít.';
$lang['privacy_stp14b'] = 'Důležitý fakt: na straně veřejnosti, oprávnění platí pro webmastery a správce jako pro ostatní uživatele. V administraci, mohou správci přistupovat k jakékoli fotografii nebo albu.';
$lang['privacy_stp15'] = 'Těsně předtím, než vysvětlíme skupiny, zde je tip: odkaz na stránku kde lze nastavit soukromé / veřejné několika albům najednou. Klikněte na něj';
$lang['privacy_stp21'] = 'Řěšení jsou: <ul><li>přidat vodoznak, aspoň pro střední a velké fotky.</li><li>dále zrušit XL a XXL velikosti.</li><li>a zrušit vysoké rozlišení (stažení a zobrazení originální fotografie) pro uživatele.';
$lang['privacy_stp2'] = 'Zde můžete vstoupit do integrované nápovědy. Klikněte na Další pro pokračování přímo na stránku nápovědy o správě práv';
$lang['privacy_stp18'] = 'Pro správu asociace mezi uživateli a skupinou, použijte stránku <em>Uživatelé » Správa</em>';
$lang['privacy_stp6'] = 'Když nahrajete fotky, můžete změnit úroveň soukromý nahraných fotek právě tam...';
$lang['privacy_stp1'] = 'Dobrý den! Budu váš průvodce a společně zjistíme, jak chránit vaše fotografie v Piwigo. Prosím, postupujte podle mých instrukcí, a klepněte na tlačítko Další (nebo pomocí šipek na vaší klávesnici). Vydáte-li se na jinou stránku administrace, budete přesměrováni na aktuální stránku průvodce. Pokud se zasekne nebo nepůjde ukončit průvodce, použijte <em> Doplňky » Take A Tour </ em> a tak se ukončí průvodce. <br> Tak začněme!';
$lang['privacy_title9'] = 'Úroveň soukromý';
$lang['privacy_title7'] = 'Úroveň soukromý';
$lang['privacy_title8'] = 'Úroveň soukromý';
$lang['privacy_title5'] = 'Práva > Skupiny';
$lang['privacy_title6'] = 'Úroveň soukromý';
$lang['privacy_title3'] = 'Práva';
$lang['privacy_title4'] = 'Práva';
$lang['privacy_title22'] = 'Lokální nastavení: Originální ochrana';
$lang['privacy_title24'] = 'Byl to skvělý čas';
$lang['privacy_title20'] = 'Veřejné fotky';
$lang['privacy_title21'] = 'Veřejné fotky';
$lang['privacy_title19'] = 'Uživatelé';
$lang['privacy_title2'] = 'Pomoc uvnitř vašeho Piwigo';
$lang['privacy_title17'] = 'Skupiny';
$lang['privacy_title18'] = 'Skupiny';
$lang['privacy_title12'] = 'Práva alba';
$lang['privacy_title15'] = 'Tip';
$lang['privacy_title10'] = 'Práva alba';
$lang['privacy_title11'] = 'Práva alba';
$lang['privacy_stp8'] = 'Vyberte jednu nebo více fotek...';
$lang['privacy_title1'] = 'Vítejte v průvodci soukromým';
$lang['privacy_stp11'] = 'Nyní klikněte na záložku Práv';
$lang['privacy_stp12'] = 'Na této stránce můžete zvolit alba která budou dostupná pro všechny nebo pouze některým uživatelům';
$lang['privacy_stp13'] = 'Nyní klikněte na soukromé';
$lang['privacy_stp14'] = 'Pak uživatelé a skupiny uživatelů mohou dostat přístup do alba.';
$lang['privacy_stp3'] = 'Udělejte si čas a přečtěte si následující informace.';