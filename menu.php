<?php
session_start();
include("header.php"); 
?>
<body>    
    <?php 
    // Selectionne tout les posts visibles   
    $cases = $bdd->prepare("SELECT * FROM cases WHERE devoile=1");
    $cases->execute(); 

    $cases_cachees = $bdd->prepare("SELECT * FROM cases WHERE devoile=0");
    $cases_cachees->execute();
    
    
    foreach($cases as $case){
    ?>
        <div id='case' onclick='document.location.href="jour.php?case=<?=$case["id_case"]?>"'>
            <h3><?php echo $case['id_case'] ?></h3>
        </div>
    <?php
    }
    foreach($cases_cachees as $case_cachee){
    ?>
        <div id='case_cahee'>
            <h3><?php echo $case_cachee['id_case'] ?></h3>
        </div>
    <?php
    }
    ?>
    

