<?php
include("config.php");
    $id=$_GET['id'];
    $current_page=$_GET['page'];
    $sql = 'update encadrant set est_supp=1 where id ='.$id ;
    mysqli_query($conDb,$sql);
    $sql2='SELECT * FROM encadrant WHERE id='.$id ;
    $result=mysqli_query($conDb,$sql2);
    $encadrant=$result->fetch_assoc() ;
    $full_name=$encadrant['nom'].' '.$encadrant['prenom'];
;    $event="INSERT INTO activitie (
            type_name,action,name
        ) VALUES (
            'encadrant','supprimé','$full_name'
        )";
    mysqli_query($conDb,$event);
    header("Location: encadrants.php?page={$current_page}");
    exit;
?>