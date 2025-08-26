<?php
include("config.php");
    $id=$_GET['id'];
    $current_page=$_GET['page'];
    $sql = 'update stagiaire set est_supp=1 where id ='.$id ;
    mysqli_query($conDb,$sql);
    $sql2='SELECT * FROM stagiaire WHERE id='.$id ;
    $result=mysqli_query($conDb,$sql2);
    $stagiaire1=$result->fetch_assoc() ;
    $full_name=$stagiaire1['nom'].' '.$stagiaire1['prenom'];
;    $event="INSERT INTO activitie (
            type_name,action,name
        ) VALUES (
            'stagiaire','supprimé','$full_name'
        )";
    mysqli_query($conDb,$event);
    header("Location: stagiaires.php?page={$current_page}");
    exit;
?>