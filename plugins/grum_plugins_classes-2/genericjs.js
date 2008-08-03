/* -----------------------------------------------------------------------------
  file: genricjs.js
  file version: 1.0
  date: 2008-01-02
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

  this classes provides base functions to make easiest a compliant code with
  FF2.0 & IE7.0
  

  ------------------------------------------------------------------------------
  HISTORY VERSION
              
   -------------------------------------------------------------------------- */



/*
  this is an implementation of the function <indexOf> to the Array class, as
  defined in the ECMA-262 standard
  for more information, see at http://developer.mozilla.org/fr/docs/R%C3%A9f%C3%A9rence_de_JavaScript_1.5_Core:Objets_globaux:Array:indexOf

  not implemented in IE 7.0
*/
if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}
