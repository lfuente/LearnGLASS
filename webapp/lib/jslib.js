//Cretate an ajax object
//return the created object
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

//Ajax authentication
//Call the authentication process
//show in error tage the give back message
function autentication()
{
	ajax_auth=objetoAjax();
	ajax_auth.open("POST", "access.php",true);
    	ajax_auth.onreadystatechange=function() {
		if (ajax_auth.readyState==4){
            //redirect to the page ej: index.html?goto=domain.com/glass/home.php
            url_site = document.location.href
            //get what it is behind =
            url_split = url_site.split('=');
            //go to the page without going through the cache
            if(ajax_auth.responseText==1){
                //Print the message
                if(url = url_split[1]){
                    //redirect to the page domain.con/glass/home.php
                    go_to_page("http://"+url);   
                }
                else{
                    go_to_page("home.php");  
                }  
            }
            else if(ajax_auth.responseText==2){
                //Go to install process
                go_to_page("install/form.html");
            }
            else{
                document.getElementById('error').innerHTML = ajax_auth.responseText;
            }
        }
	}
    ajax_auth.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax_auth.send("user="+document.login.user.value+"&pass="+document.login.pass.value);
}




//Sugest filters search
//Sugest the metadatafilters than the user belong
function sugest(page,role,value,db){
	sugest_ajax=objetoAjax();
	sugest_ajax.open("POST",page,false);//sync
    sugest_ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	sugest_ajax.send("value="+value+"&role="+role+"&CAMdb="+db);
    return sugest_ajax.responseText;
}

//Load the page selected
//var page: page to load
//no return
function go_to_page(page){
    document.location.href=page;
}



//add an element to dashboard or my views
//var conf: json string with the configuration of the view
//var module: name of the folder where are the files of the module. Only the folder name that we found in the visualization folder
//var page: page where the todashboard.php is. Ii is '../../lib/todashboard.php' normaly
//no return
//In msg-info div apear the return information
function add_to_page_said(conf,module,page,dbId,name,des,dashId){
	todbajax=objetoAjax();
	todbajax.open("POST", page, true );
    	todbajax.onreadystatechange=function() {
		if (todbajax.readyState==4) {
			document.getElementById('msg-info').innerHTML = todbajax.responseText;
            window.setTimeout("document.getElementById('msg-info').innerHTML=''",5000);            
        }
	}
    todbajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	todbajax.send("conf="+conf+"&module="+module+"&CAMid="+dbId+"&name="+name+"&des="+des+"&dashId="+dashId);
}




//check characters
//var checkStr: string to analize
//return allValid true or false (due to the format of checkStr)
function chk_characters(checkStr){
    var checkOK = "ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����" + "abcdefghijklmn�opqrstuvwxyz����� " + "0123456789_:/-#?��!%";
    var allValid = true;
    if(checkStr=='' || checkStr==null){
        allValid = false;
    }
    for (i = 0; i < checkStr.length; i++){
        ch = checkStr.charAt(i); 
        for (j = 0; j < checkOK.length; j++)
            if (ch == checkOK.charAt(j))
                break;
        if (j == checkOK.length) 
        { 
            allValid = false; 
            break; 
        }
    }
    return allValid;
}

//check the text field of an input
//obj form: the object which represent the form
//var msg: error message
//teturn: true or false
function validate_send(form,msg1,msg2){
    //walk the form except the last element is the submit
    for(var i=0; i < form.elements.length-1; i++) {
        //Check if the field is not empty
        checkStr = form.elements[i].value
        if(checkStr==''){
            alert(msg2); 
            form.elements[i].focus(); 
            return false;
        }
        //Check if chars are correct
        valid = chk_characters(checkStr);
        if (!valid){ 
            alert(msg1); 
            form.elements[i].focus(); 
            return false; 
        }
    }
    return true;
} 


