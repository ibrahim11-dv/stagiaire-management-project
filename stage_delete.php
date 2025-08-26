<?php
include("config.php");
    $id=$_GET['id'];
    $current_page=$_GET['page'];
    $sql = 'update stage set est_supp=true where id ='.$id ;
    mysqli_query($conDb,$sql);
    $sql2='SELECT * FROM encadrant WHERE id='.$id ;
    $result=mysqli_query($conDb,$sql2);
    $stage=$result->fetch_assoc() ;
    $full_name=$stage['sujet'];
;    $event="INSERT INTO activitie (
            type_name,action,name
        ) VALUES (
            'stage','supprimé','$full_name'
        )";
    mysqli_query($conDb,$event);
    header("Location: stages.php?page={$current_page}");
    exit;
?>