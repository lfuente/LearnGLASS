function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

 
function usersearch(dir)
{
	ajax=objetoAjax();
	ajax.open("GET", dir);
	ajax.onreadystatechange=function()
    {
		if (ajax.readyState==4) 
        {
            data = eval(ajax.responseText);
            
            if(data!=null)
            {
                 
                for (var i=0; i < data.length; i++)
                {
                    dataAux = data[i];
                                
                    var fila= document.getElementById('users-admin').insertRow(i+1);
                    var cadena0=fila.insertCell(0);
                	var cadena1=fila.insertCell(1);
                    var cadena2=fila.insertCell(2);    
                
                	cadena0.innerHTML = i+1;
                	cadena1.innerHTML = dataAux.name;
                    
                    var sel = document.createElement('select');
                    sel.name = 'selRow' + dataAux.name;
                    sel.options[0] = new Option('Admin', 'admin');
                    sel.options[1] = new Option('Instructor', 'instructor');
                    sel.options[2] = new Option('Observer', 'observer');
                    sel.options[3] = new Option('Student', 'student');
                    sel.onchange = function()
                    {
                        send_data(this.name,0,this.value,"setusertype.php");
                    }
                    cadena2.appendChild(sel).value=dataAux.userType;
                    
                 }
                 len = document.getElementById("users-admin").rows.length;
                 for(var i=len-1; i > data.length; i--)
                 {
                    document.getElementById("users-admin").deleteRow(i);
                 }                  
            }
            else
            {
                len = document.getElementById("users-admin").rows.length;
                for(var i=len-1; i > 0; i--)
                {
                    document.getElementById("users-admin").deleteRow(i);
                } 
            }
		}
	}
	ajax.send(null);
}



function charge_user_model_permits(dir)
{
    ajax1=objetoAjax();
	ajax1.open("GET", dir);
	ajax1.onreadystatechange=function()
    {
		if (ajax1.readyState==4) {
            data = eval(ajax1.responseText);
            if(data!=null)
            {
                for(i=0;i<data.length;i++)
                {   
                    dataAux = data[i];
                    var fila= document.getElementById("users-perm").insertRow(i+1);
                    fila.className="table-row";
                    var cadena0=fila.insertCell(0);
                	var cadena1=fila.insertCell(1);
                    var cadena2=fila.insertCell(2);
                    var cadena3=fila.insertCell(3);
                    var cadena4=fila.insertCell(4);
                    var cadena5=fila.insertCell(5);
                    var cadena6=fila.insertCell(6);
                    var cadena7=fila.insertCell(7);
                    var cadena8=fila.insertCell(8);
                    var cadena9=fila.insertCell(9);
                    var cadena10=fila.insertCell(10);
                    
                   
                    //Usertype field
                    cadena0.innerHTML = dataAux.userType;

                    //visualization level field
                    var num = document.createElement('input');
                    num.setAttribute("type","number");
                    num.setAttribute("min","1");
                    num.setAttribute("max","4");
                    num.setAttribute("step","1");
                    num.setAttribute("size","1");
                    num.setAttribute("name",i);
                    num.onclick = function()
                    {
                        send_data(1,this.name,this.value,"setperm.php");
                    }
                    cadena1.appendChild(num).value=dataAux.userViewLevel;
                    
                    //Modification permit field                    
                    var chk1 = document.createElement('input');
                    chk1.setAttribute("type","checkbox");
                    chk1.setAttribute("name",i);
                    chk1.onchange = function()
                    {
                        send_data(2,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.userModifyPermision==1)
                        cadena2.appendChild(chk1).checked = 1;
                    else
                        cadena2.appendChild(chk1).checked = 0;
                    
                    //Modification permit field                    
                    var chk2 = document.createElement('input');
                    chk2.setAttribute("type","checkbox");
                    chk2.setAttribute("name",i);
                    chk2.onchange = function()
                    {
                        send_data(3,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.userTypeChange==1)
                        cadena3.appendChild(chk2).checked = 1;
                    else
                        cadena3.appendChild(chk2).checked = 0;
                    
                    //Modification permit field                    
                    var chk3 = document.createElement('input');
                    chk3.setAttribute("type","checkbox");
                    chk3.setAttribute("name",i);
                    chk3.onchange = function()
                    {
                        send_data(4,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.moduleInstall==1)
                        cadena4.appendChild(chk3).checked = 1;
                    else
                        cadena4.appendChild(chk3).checked = 0;
                    
                    //Modification permit field                    
                    var chk4 = document.createElement('input');
                    chk4.setAttribute("type","checkbox");
                    chk4.setAttribute("name",i);
                    chk4.onchange = function()
                    {
                        send_data(5,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.importView==1)
                        cadena5.appendChild(chk4).checked = 1;
                    else
                        cadena5.appendChild(chk4).checked = 0;

                    //Modification permit field                    
                    var chk5 = document.createElement('input');
                    chk5.setAttribute("type","checkbox");
                    chk5.setAttribute("name",i);
                    chk5.onchange = function()
                    {
                        send_data(6,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.varSettings==1)
                        cadena6.appendChild(chk5).checked = 1;
                    else
                        cadena6.appendChild(chk5).checked = 0;

                    //Modification permit field                    
                    var chk6 = document.createElement('input');
                    chk6.setAttribute("type","checkbox");
                    chk6.setAttribute("name",i);
                    chk6.onchange = function()
                    {
                        send_data(7,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.addBBDDCAM==1)
                        cadena7.appendChild(chk6).checked = 1;
                    else
                        cadena7.appendChild(chk6).checked = 0;
   
                    //Modification permit field                    
                    var chk7 = document.createElement('input');
                    chk7.setAttribute("type","checkbox");
                    chk7.setAttribute("name",i);
                    chk7.onchange = function()
                    {
                        send_data(8,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.download==1)
                        cadena8.appendChild(chk7).checked = 1;
                    else
                        cadena8.appendChild(chk7).checked = 0;
        
                    //Modification permit field                    
                    var chk8 = document.createElement('input');
                    chk8.setAttribute("type","checkbox");
                    chk8.setAttribute("name",i);
                    chk8.onchange = function()
                    {
                        send_data(9,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.viewUser==1)
                        cadena9.appendChild(chk8).checked = 1;
                    else
                        cadena9.appendChild(chk8).checked = 0;
     
                    //Modification permit field                    
                    var chk9 = document.createElement('input');
                    chk9.setAttribute("type","checkbox");
                    chk9.setAttribute("name",i);
                    chk9.onchange = function()
                    {
                        send_data(10,this.name,this.checked,"setperm.php");
                    }
                    if(dataAux.viewSuggest==1)
                        cadena10.appendChild(chk9).checked = 1;
                    else
                        cadena10.appendChild(chk9).checked = 0;
                                   
                    
                    //disabled admin settings
                    if(dataAux.userType=='admin')
                    {
                        num.disabled=true;
                        chk1.disabled=true;
                        chk2.disabled=true;
                        chk3.disabled=true;
                        chk4.disabled=true;
                        chk5.disabled=true;
                        chk6.disabled=true;
                        chk7.disabled=true;
                        chk8.disabled=true;
                        chk9.disabled=true;
                    }    
                }
            }
		}
	}
	ajax1.send(null);
}


function send_data(ref1,ref2, value, dir)
{
	ajax=objetoAjax();
	ajax.open("POST", dir,true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("ref1="+ref1+"&ref2="+ref2+"&value="+value);
}




function findValue(li)
{
    if( li == null ) 
        return alert("No match!");
        
    else
    {
        var sValue = li.selectValue;
        //do the search
        usersearch("viewusers.php?uName=" + sValue);
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
	return false;
}