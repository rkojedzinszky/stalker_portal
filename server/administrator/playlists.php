<?php
session_start();

ob_start();

include "../common.php";
include "../conf_serv.php";
include "../lib/func.php";

$error = '';
$action_name = 'add';
$action_value = 'Добавить';

moderator_access();

if (@$_SESSION['login'] != 'alex' && @$_SESSION['login'] != 'duda' && !check_access()){ 
    exit;
}

foreach (@$_POST as $key => $value){
    $_POST[$key] = trim($value);
}

$playlist = new Playlist();
    
if (@$_POST['add']){
    
    $playlist->add($_POST['name'], $_POST['group_id']);
    
    header("Location: playlists.php");
}

$id = @intval($_GET['id']);

if (!empty($id)){
    
    if (@$_POST['edit']){
        $playlist->set(array('name' => $_POST['name'], 'group_id' => $_POST['group_id']), $_GET['id']);
        
        header("Location: playlists.php");
    }elseif (@$_GET['del']){
        
        $playlist->del($id);
        
        header("Location: playlists.php");
    }
}

if (@$_GET['edit'] && !empty($id)){
    $action_name = 'edit';
    $action_value = 'Сохранить';
    $edit_playlist = $playlist->getById($id);
}

$playlists = $playlist->getAll();

$debug = '<!--'.ob_get_contents().'-->';
ob_clean();
echo $debug;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">

body {
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
}
td {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    text-decoration: none;
    color: #000000;
}
.list, .list td, .form{
    border-width: 1px;
    border-style: solid;
    border-color: #E5E5E5;
}
a{
	color:#0000FF;
	font-weight: bold;
	text-decoration:none;
}
a:link,a:visited {
	color:#5588FF;
	font-weight: bold;
}
a:hover{
	color:#0000FF;
	font-weight: bold;
	text-decoration:underline;
}
</style>
<title>Плейлисты</title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
    <font size="5px" color="White"><b>&nbsp;Плейлисты&nbsp;</b></font>
    </td>
</tr>
<tr>
    <td width="100%" align="left" valign="bottom">
        <a href="index.php"><< Назад</a>
    </td>
</tr>
<tr>
    <td align="center">
    <font color="Red">
    <strong>
    <? echo $error?>
    </strong>
    </font>
    <br>
    <br>
    </td>
</tr>
<tr>
<td align="center">
    <table class='list' cellpadding='3' cellspacing='0'>
        <tr>
            <td>ID</td>
            <td>Имя</td>
            <td>&nbsp;</td>
        </tr>
        <? foreach ($playlists as $playlist){
                echo '<tr>';
                echo '<td>'.$playlist['id'].'</td>';
                echo '<td><a href="playlist.php?playlist_id='.$playlist['id'].'">'.$playlist['name'].'</a></td>';
                echo '<td>';
                
                echo '<a href="?edit=1&id='.$playlist['id'].'">edit</a>&nbsp;';
                echo '<a href="?del=1&id='.$playlist['id'].'" onclick="if(confirm(\'Вы действительно хотите удалить плейлист '.$playlist['name'].' из базы?\')){return true}else{return false}">del</a>';
                echo '</td>';
                echo '</tr>';
           }?>
    </table>
</td>
</tr>
<tr>
    <td align="center">
<br>
<br>
        <form method="POST">
            <table class="form">
                <tr>
                    <td>Имя</td>
                    <td><input type="text" name="name" value="<?echo @$edit_playlist['name']?>"></input></td>
                </tr>
                <tr>
                    <td>Группа</td>
                    <td>
                        <select name="group_id">
                            <option value="0">--------</option>
                            <?
         
                            $stb_groups = new StbGroup();
                            $all_groups = $stb_groups->getAll();
                            
                            foreach ($all_groups as $group){
                                $selected = '';
                                
                                if ($edit_playlist['group_id'] == $group['id']){
                                    $selected = 'selected';
                                }
                                
                                echo '<option value="'.$group['id'].'" '.$selected.'>'.$group['name'].'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="<? echo $action_name ?>" value="<? echo $action_value?>"></input></td>
                </tr>
            </table>
        </form>
    </td>
</tr>
</table>