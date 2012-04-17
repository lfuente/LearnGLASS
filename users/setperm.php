<?php
	session_start();
    include("../config.php");
	
    if(isset ($_SESSION['s_username']))
	{
        include_once($CFG->dir."lib/mainlib.php");
	
        $my_permision = new permision($CFG,$_SESSION['s_username']);
        if($my_permision->get_userModifyPermision()==1)
        {
            $input = $_POST['ref1'];
            $uType = $_POST['ref2']; 
            $value = $_POST['value'];
            
            $uType = getUserType($uType);
            $my_permision->set_userType($uType);
            if($input=='1')
            {
                $my_permision->set_userViewLevel($value);
                $my_permision->set_DB_userViewLevel($CFG);
            }
            else if($input=='2')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_userModifyPermision($value);
                $my_permision->set_DB_userModifyPermision($CFG);
            }
            else if($input=='3')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_userTypeChange($value);
                $my_permision->set_DB_userTypeChange($CFG);
            }
            else if($input=='4')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_moduleInstall($value);
                $my_permision->set_DB_moduleInstall($CFG);
            }
            else if($input=='5')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_importView($value);
                $my_permision->set_DB_importView($CFG);
            }
            else if($input=='6')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_varSettings($value);
                $my_permision->set_DB_varSettings($CFG);
            }
            else if($input=='7')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_addBBDDCAM($value);
                $my_permision->set_DB_addBBDDCAM($CFG);
            }
            else if($input=='8')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_download($value);
                $my_permision->set_DB_download($CFG);
            }
            else if($input=='9')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_viewUser($value);
                $my_permision->set_DB_viewUser($CFG);
            }
            else if($input=='10')
            {
                if($value=='true')
                    $value = 1;
                else
                    $value = 0;
                $my_permision->set_viewSuggest($value);
                $my_permision->set_DB_viewSuggest($CFG);
            }
            
         }      
	}
	else
	{
		header("Location: ".$CFG->url."index.html");
	}
?>