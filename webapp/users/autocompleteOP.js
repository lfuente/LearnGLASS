function findValue(li)
{
    if( li == null ) 
        return alert("No match!");
        
    else
    {
        var sValue = li.selectValue;
        //do the search
        usersearch("consulta.php?uName=" + sValue);
    }
    
    
}

function selectItem(li)
{
	findValue(li);
}

function lookupAjax()
{
    var oSuggest = $("#CityAjax")[0].autocompleter;
    oSuggest.findValue();
    return false;
}

function lookupLocal()
{
	var oSuggest = $("#CityLocal")[0].autocompleter;

	oSuggest.findValue();
    alert("d");
	return false;
}


$("#CityAjax").autocomplete("autocomplete.php",
  {
		delay:10,
		minChars:1,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		onItemSelect:selectItem,
		onFindValue:findValue,
		autoFill:true
	}
);

