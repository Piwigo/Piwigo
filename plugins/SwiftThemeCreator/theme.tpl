BODY, H3, #imageHeaderBar, #imageToolBar A:hover, 
.row1, UL.tabsheet LI.normal_tab {ldelim} background-color: {$main.color1}; }

BODY, H1, H3, DT,
INPUT.rateButtonSelected /* <= why IE doesn't inherit this ? */ {ldelim}
	color:{$main.color2}; }
#theImage IMG {ldelim} border-color: {$main.color2}; }

H2, #menubar DT, .throw,
A, INPUT.rateButton {ldelim} color: {$main.color3}; }

UL.tabsheet LI.normal_tab:hover {ldelim} border-color: {$main.color4}; }
A:hover {ldelim} 	color: {$main.color4}; }

.content UL.thumbnails SPAN.wrap2:hover,
.content UL.thumbnailCategories DIV.thumbnailCategory:hover,
.content UL.thumbnailCategories DIV.thumbnailCategory:hover A {ldelim}
 color: {$main.color4}; border-color: {$main.color5}; background-color: {$main.color6}; }

#menubar DL, .content, #comments DIV.comment BLOCKQUOTE,
#imageHeaderBar, H2, #menubar DT, #imageToolBar {ldelim} border-color: {$main.color5}; }

#menubar DL, .content, #imageToolBar, .header_notes, UL.tabsheet LI.selected_tab {ldelim}
	background-color: {$main.color6}; }

FIELDSET, INPUT, SELECT, TEXTAREA, .content DIV.comment  A.illustration IMG,
.content DIV.thumbnailCategory, .content UL.thumbnails SPAN.wrap2 {ldelim}
 border-color: {$main.color6}; }

#comments DIV.comment BLOCKQUOTE {ldelim} border-color: {$main.color7}; }

H2, #menubar DT, .throw {ldelim} background-image: url(stc.png); }
#imageHeaderBar H2 {ldelim} background-image: none; background-color: transparent; border: none; }

#imageHeaderBar {ldelim} 
  background: transparent url(stc.png) scroll repeat-x center top; }