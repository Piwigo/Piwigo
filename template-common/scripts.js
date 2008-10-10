function SelectAll( formulaire )
{
var len = formulaire.elements.length;
var i=0;
for( i = 0; i < len; i++)
{
	if ( formulaire.elements[i].type=='checkbox'
		&& formulaire.elements[i].name != 'copie')
	{
		formulaire.elements[i].checked = true;
	}
}
}

function DeselectAll( formulaire )
{
var len = formulaire.elements.length;
var i=0;
for( i = 0; i < len; i++)
{
	if ( formulaire.elements[i].type=='checkbox'
		&& formulaire.elements[i].name != 'copie')
	{
	formulaire.elements[i].checked = false;
	}
}
}

function Inverser( formulaire )
{
var len = formulaire.elements.length;
var i=0;
for( i=0; i<len; i++)
{
	if ( formulaire.elements[i].type=='checkbox'
		&& formulaire.elements[i].name != 'copie')
	{
	formulaire.elements[i].checked = !formulaire.elements[i].checked;
	}
}
}

function phpWGOpenWindow(theURL,winName,features)
{
	img = new Image();
	img.src = theURL;
	if (img.complete)
	{
		var width=img.width +40;
		var height=img.height +40;
	}
	else
	{
		var width=640;
		var height=480;
		img.onload = resizeWindowToFit;
	}
	newWin = window.open(theURL,winName,features+',left=2,top=1,width=' + width + ',height=' + height);
}

function resizeWindowToFit()
{
	newWin.resizeTo( img.width+50, img.height+100);
}

function popuphelp(url)
{
	window.open( url, 'dc_popup',
		'alwaysRaised=yes,dependent=yes,toolbar=no,height=420,width=500,menubar=no,resizable=yes,scrollbars=yes,status=no'
	);
}



Function.prototype.pwgBind = function() {
	var __method = this, object = arguments[0], args = new Array();
	for (var i=1; i<arguments.length; i++)
		args[i-1] = arguments[i];
	return function() { return __method.apply(object, args); }
}

function PwgWS(urlRoot)
{
	this.urlRoot = urlRoot;
	this.options = {
		method: "GET",
		async:  true,
		onFailure: null,
		onSuccess: null
	};
};

PwgWS.prototype = {

	callService : function(method, parameters, options)
	{
		if (options)
		{
			for (var property in options)
				this.options[property] = options[property];
		}
		try { this.transport = new XMLHttpRequest();}
		catch(e) {
			try { this.transport = new ActiveXObject('Msxml2.XMLHTTP'); }
			catch(e) {
				try { this.transport = new ActiveXObject('Microsoft.XMLHTTP'); }
				catch (e){
					dispatchError(0, "Cannot create request object");
				}
			}
		}
		this.transport.onreadystatechange = this.onStateChange.pwgBind(this);

		var url = this.urlRoot;
		url += "ws.php?format=json&method="+method;
		if (parameters)
		{
			for (var property in parameters)
			{
				if ( typeof parameters[property] == 'object' && parameters[property])
				{
					for (var i=0; i<parameters[property].length; i++)
						url += "&"+property+"[]="+encodeURIComponent(parameters[property][i]);
				}
				else
					url += "&"+property+"="+encodeURIComponent(parameters[property]);
			}
		}
		this.transport.open(this.options.method, url, this.options.async);
		this.transport.send(null);
	},

	onStateChange: function() {
		var readyState = this.transport.readyState;
		if (readyState == 4)
			this.respondToReadyState(this.transport.readyState);
	},

	dispatchError: function( httpCode, text )
	{
		!this.options.onFailure || this.options.onFailure( httpCode, text);
	},

	respondToReadyState: function(readyState)
	{
		var transport = this.transport;
		if (readyState==4 && transport.status == 200)
		{
			var resp;
			try {
				eval('resp = ' + transport.responseText);
			}
			catch (e) {
				this.dispatchError( 200, e.message + '\n' + transport.responseText.substr(0,512) );
			}
			if (resp!=null)
			{
				if (resp.stat==null)
					this.dispatchError( 200, "Invalid response" );
				else if (resp.stat=='ok')
				{
					if (this.options.onSuccess) this.options.onSuccess( resp.result );
				}
				else
					this.dispatchError( 200, resp.err + " " + resp.message);
			}
		}
		if (readyState==4 && transport.status != 200)
			this.dispatchError( transport.status, transport.statusText );
	},


	transport: null,
	urlRoot: null,
	options: {}
}
