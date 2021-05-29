
<?php
session_start();
include("header.php"); 
?>
<body>    
    <?php 

    if(!$_GET['case']){
	echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";
        exit();	
    }
    $cases = $bdd->prepare("SELECT * FROM cases WHERE id_case=?");
    $cases->execute([$_GET['case']]);
    foreach($cases as $case){
	if ($case['devoile']!=1){ 
		echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";
        	exit();	   	
	}
    }
    
    //Selectionne tout les posts visibles
    $post = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE visible=1 AND id_case=? ORDER BY p.date ASC");
    
    //Compte le nombre de post dans la page menu
    $conteur = $bdd->prepare("SELECT COUNT(*) FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE visible=1 AND id_case=? ");
    $conteur->execute([$_GET['case']]);
    foreach($conteur as $test){
        //Si aucun post n'est présent alors :
        if($test[0]==0){
            ?><h1 id='no_post'>Aucun post, soyez le premier !</h1><?php
        }
    }
    $post->execute([$_GET['case']]); 
    
 // Verifie les bouttons de l'admin sur les posts
    // Bouton supprimer :
    if (isset($_POST['idADelete'])) {
        $idDelete = $_POST['idADelete'];
        // Suppression de l'image du post
        $get_image = $bdd->prepare("SELECT post_image FROM posts WHERE id_post=$idDelete");
        $get_image->execute();
        $lien = $get_image->fetchAll();
        $fichier = 'img/'.$lien[0]['post_image'];
        unlink($fichier); 
        // Suppression des commentaires du post
        $delete_allcom = $bdd->prepare("DELETE c FROM comments c INNER JOIN posts p ON p.id_post = c.id_post WHERE p.id_post=$idDelete");
        $delete_allcom->execute();
        // Suppression du post 
        $delete_post = $bdd->prepare("DELETE FROM posts WHERE id_post=$idDelete");
        $delete_post->execute();
	echo "<script type='text/javascript'>document.location.replace('$idModified');</script>";exit();	
    }
    if (isset($_POST['idACacher'])) {
        $idHidden = $_POST['idACacher'];
        $hidde_post = $bdd->prepare("UPDATE posts SET visible=0 WHERE id_post=$idHidden");
        $hidde_post->execute();
        echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";exit();	
    }
    if (isset($_POST['idAModifier'])) {
        $idModified = 'modifier.php?id='.$_POST['idAModifier'];
        echo "<script type='text/javascript'>document.location.replace('$idModified');</script>";exit();	
    }    
    
    
    


    
    
    foreach($post as $posts){
        // Simplification de la date
        $jour = substr($posts['date'],8,2);
        $mois = substr($posts['date'],5,2);
        $annee = substr($posts['date'],0,4);

	//Compte le nombre de commentaire pour chaque post
        $cmpt_coms = $bdd->prepare("SELECT COUNT(*) FROM comments c WHERE id_post = ?");
        $cmpt_coms->execute(Array($posts['id_post']));
        foreach($cmpt_coms as $cmpt_com){
            $nbr_com = $cmpt_com[0];
        }
    ?>
    
    
    
    <!-- DIV pour tous les posts -->
    <div class='menu_post' onclick='document.location.href="post.php?id=<?=$posts["id_post"]?>"'>
        <!-- Boutons de l'auteur -->
        <?php if($_SESSION['grade'] == 0 && $_SESSION['id']==$posts['id_user']){?>
            <form method='POST'>
                <button id='bouton_admin' type="submit" name="idADelete" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>
            </form>
        <?php }?>
        <!-- Bouton de l'admin -->
        <?php if($_SESSION['grade'] == 2){?>
            <form method='POST'>
                <button id='bouton_admin' type="submit" name="idADelete" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                <button id='bouton_admin' type="submit" name="idACacher" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/montrer.png" alt="montrer"></button>
                <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>   
            </form>
        <?php }?>
        <h2 id='menu_text'><?=$posts['titre']?> </h2>
        <!-- On verifie si il existe un résumé sinon on commence à afficher le contenu -->
        <?php if($posts['resumee']!=''){?>
            <p id='menu_text'><?=nl2br($posts['resumee'])?> </p>
        <?php }elseif(strlen($posts['contenu'])<=300){?>
            <p id='menu_text'><?=nl2br($posts['contenu'])?> </p>
        <?php }else{
            $str = nl2br(substr($posts['contenu'],0,300));     
        ?>
            <p id='menu_text'><?= nl2br(substr($str,0,strrpos($str,' '))).'...'?> </p> 
        <?php }?>

        <!-- On verifie si il y a une image et on l'affiche -->
        <?php if($posts["post_image"] != NULL){?>
            <img class='img_post' src="img/<?php echo $posts["post_image"] ?>" alt="image introuvable">
        <?php }?>
        <span class="clear"></span>

        <!-- Details du post -->
	<p id='menu_com'><i class="far fa-comment"></i> <?=$nbr_com?></p>
	<br><br>        
        <p id='menu_pseudo'>Ecrit par : <?=$posts['pseudo']?></p>
        <p id='menu_date'>Le : <?=$jour?>-<?=$mois?>-<?=$annee?></p>
    </div>
    <?php
    }
    ?>
    


