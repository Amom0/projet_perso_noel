<?php
    session_start();
    include("header.php"); 
    if(!isset($_GET['id'])){
        header('Location: menu.php');
        exit;
    }
    $infopost = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE p.id_post=?"); 
    $infopost -> execute(Array($_GET['id'])); 
    $lepost = $infopost->fetchAll();
    $jour = substr($lepost[0]['date'],8,2);
    $mois = substr($lepost[0]['date'],5,2);
    $annee = substr($lepost[0]['date'],0,4);

    

        if (isset($_POST['idADelete'])) {
            $idDelete = $_POST['idADelete'];
            $get_image = $bdd->prepare("SELECT post_image FROM posts WHERE id_post=$idDelete");
            $get_image->execute();
            $delete_post = $bdd->prepare("DELETE FROM posts WHERE id_post=$idDelete");
            $delete_post->execute();
            $lien = $get_image->fetchAll();
            $fichier = 'img/'.$lien[0]['post_image'];
            unlink($fichier);
            echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";
            exit();
        }
        if (isset($_POST['idACacher'])) {
            $idHidden = $_POST['idACacher'];
            $hidde_post = $bdd->prepare("UPDATE posts SET visible=0 WHERE id_post=$idHidden");
            $hidde_post->execute();
            header('location:'.$_SERVER['REQUEST_URI']);
        }
        if (isset($_POST['idAMontrer'])) {
            $idShowed = $_POST['idAMontrer'];
            $show_post = $bdd->prepare("UPDATE posts SET visible=1 WHERE id_post=$idShowed");
            $show_post->execute();
            header('location:'.$_SERVER['REQUEST_URI']);
        }
        if (isset($_POST['idAModifier'])) {
            $idModified = 'modifier.php?id='.$_POST['idAModifier'];
	    echo "<script type='text/javascript'>document.location.replace('$idModified');</script>";
            exit();	
        }
        if (isset($_POST['idComADelete'])) {
            $idComDelete = $_POST['idComADelete'];
            $delete_com = $bdd->prepare("DELETE FROM comments WHERE id_comment= ?");
            $delete_com->execute([$idComDelete]);
	    header('location:'.$_SERVER['REQUEST_URI']);
        }
        
    

?>
<body>
    <div id='box_post'>
        <?php if($_SESSION['grade'] == 0 && $_SESSION['id']==$lepost[0]["id_user"]){?>
            <form method='POST'>
                <button id='bouton_admin' type="submit" name="idADelete" value="<?=$lepost[0]["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$lepost[0]["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>
            </form>
        <?php }?>
        <?php if($_SESSION['grade'] == 2){?>
            <form method='POST'>
                <button id='bouton_admin' type="submit" name="idADelete" value="<?=$lepost[0]["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                <?php if($lepost[0]["visible"]==1){ ?>
                    <button id='bouton_admin' type="submit" name="idACacher" value="<?=$lepost[0]["id_post"]?>"><img id='croix' src="icones/montrer.png" alt="montrer"></button>
                <?php }else{?>
                    <button id='bouton_admin' type="submit" name="idAMontrer" value="<?=$lepost[0]["id_post"]?>"><img id='croix' src="icones/cacher.png" alt="cacher"></button>
                <?php }?>
                <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$lepost[0]["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>   
            </form>
        <?php }?>

        <h1 id='post_titre'><?= $lepost[0]["titre"] ?></h1> 
        <?php if($lepost[0]["post_image"] != ''){?>
            <img id='img_plein_post' src="img/<?php echo $lepost[0]["post_image"] ?>" alt="image introuvable">
        <?php }?>
        <p id='post_contenu'><?= nl2br($lepost[0]["contenu"]) ?></p>  
        <h5 class='post_detail'>Ecrit par : <?= $lepost[0]["pseudo"] ?></h5>
        <h5 class='post_detail'>Publié le : <?=$jour?>-<?=$mois?>-<?=$annee?></h5>
    </div>

    <div id='box_comment'>
            <h1 id='titre_comment'>Commentaires</h1>
 	    <?php if(isset($_SESSION["pseudo"])){ 
                    $msg = 'Commenter... ';
            ?>
                    <div id='new_comment'>
                        <h2>New comment :</h2>
                        <form method='POST' >
                            <textarea id='text_new_comment' name="commentaire"  placeholder="Commenter..." maxlength=500 ></textarea>
                            <button id='btn_new_comment' type="submit" name="action" value="Commenter"><i class="fas fa-paper-plane fa-2x"></i></button>
                        </form>
                        
                        <?php
                            if(isset($_POST['action']) && $_POST['action'] == "Commenter"){
                                $newcomment = $bdd->prepare('INSERT INTO comments (contenu, id_user, id_post) VALUES ("'.htmlspecialchars($_POST["commentaire"], ENT_QUOTES).'",'.$_SESSION['id'].','.$_GET['id'].')');
                                $newcomment->execute();
                            } 
                        ?>

                    </div>
                 
            <?php }else{?>
                    <p id='alerter'>Veuillez-vous connecter pour commenter !</p>
            <?php }?>
            <?php
                $all_comment = $bdd->prepare("SELECT * FROM comments c INNER JOIN user u ON u.id_user = c.id_user WHERE c.id_post = ? ORDER BY date_com DESC");
                $all_comment->execute(Array($_GET['id']));
                //Compte le nombre de commentaire pour le post
                $conteur = $bdd->prepare("SELECT COUNT(*) FROM comments WHERE id_post=? ");
                $conteur->execute(Array($_GET['id']));
                foreach($conteur as $test){
                    
                    //Si aucun commentaire n'est présent alors :
                    if($test[0]==0){
                        ?><h1 id='no_post'>Aucun commentaire </h1><?php
                    }
                }

                foreach($all_comment as $comment){
                     // Simplification de la date
                    $jour = substr($comment['date_com'],8,2);
                    $mois = substr($comment['date_com'],5,2);
                    $annee = substr($comment['date_com'],0,4);
                    $heure = substr($comment['date_com'],11,5);
            ?>
                <div id='comment'>       
                    <p id='contenu_comment'><?=$comment['contenu']?></p>            
                    <div id='info_comment'>
			<p>                        </p>
                        <h2><?=$comment['pseudo']?></h2>
                        <?php if(($comment['id_user']==$_SESSION['id']) || $_SESSION['grade']==2){?>
                            <form method='POST'>
                                <button id='bouton_admin' type="submit" name="idComADelete" value="<?= $comment['id_comment']?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                            </form>
                        <?php }?>
                    </div>                  
                </div>
            <?php }?>
    </div>   
<?php
    
?>  

<?php
    include("footer.php"); 
?>
