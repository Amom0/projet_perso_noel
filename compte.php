<?php
session_start();
include("header.php"); 


if (isset($_POST['idADelete'])) {
    $idDelete = $_POST['idADelete'];
    $get_image = $bdd->prepare("SELECT post_image FROM posts WHERE id_post=$idDelete");
    $get_image->execute();
    $delete_post = $bdd->prepare("DELETE FROM posts WHERE id_post=$idDelete");
    $delete_post->execute();
    $lien = $get_image->fetchAll();
    $fichier = 'img/'.$lien[0]['post_image'];
    unlink($fichier);
    header('location:'.$_SERVER['REQUEST_URI']);
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
    header('location:'.$idModified);
}

?>

<body>
    

    <div class="box_compte">
        <h2 class='titre_compte'>Modifier mes données</h2>
        <form method='POST'>
            <input class="box_grande" type="text" name="pseudo" value="<?php echo $_SESSION['pseudo']?>" placeholder="Votre Pseudo / Identifiant" required>
            <p id='alerte_mdp' class='verif_mdp'></p>
            <input class="box_grande" type="password" name="password_O" id="password_O" placeholder="Mot de passe">
            <p id='message' class='verif_mdp'></p>
            <input class="box_grande" type="password" name="password_N" id="password_N" placeholder="Nouveau mot de passe"> 
            <input class="box_grande" onmouseout="check_pass('password_N','password_N2','modifier_compte');"  type="password" name="password_N2" id="password_N2" placeholder="Vérification du nouveau mot de passe"> 
            <button class="bouton_submit" id="modifier_compte" type="submit" name="action" value="Modifier_compte">Modifier !</button>

        </form>

        <?php
            
            if(isset($_POST['action']) && $_POST['action'] == "Modifier_compte"){
                if(isset($_POST['password_O']) && md5($_POST['password_O']) != $_SESSION['mdp']){
                    echo '<script>mdp_error();</script>'; 
                }
                else{
                    $nom = $_POST['nom'];
                    $prenom = $_POST['prenom'];
                    $pseudo = $_POST['pseudo'];
                    $email = $_POST['email'];
                    $date_n = $_POST['date_naissance'];
                    $id = $_SESSION['id'];
                    if($_POST['password_N']!='' && $_POST['password_N2']!=''){
                        $passwd = md5($_POST['password_N']);
                        $modification = $bdd->prepare('UPDATE user SET nom =?, prenom=?, pseudo=?, passwd=?, email=?, date_birth=? WHERE id_user=?');
                        $modification ->execute(Array($nom,$prenom,$pseudo,$passwd,$email,$date_n,$id));
                        $_SESSION['mdp'] = $passwd;
                    }
                    else{
                        $modificationwmdp = $bdd->prepare('UPDATE user SET nom =?, prenom=?, pseudo=?, email=?, date_birth=? WHERE id_user=?');
                        $modificationwmdp ->execute(Array($nom,$prenom,$pseudo,$email,$date_n,$id));
                        
                    }
                    $_SESSION['nom'] = $_POST['nom'];
                    $_SESSION['prenom'] = $_POST['prenom'];
                    $_SESSION['pseudo'] = $_POST['pseudo'];
                    $_SESSION['mail'] = $_POST['email'];
                    $_SESSION['date_n'] = $_POST['date_naissance'];
                }  
                header('location: compte.php');
            }
        ?>
    </div>

    <div class="box_compte_post" >
        <h2 class='titre_connec'>Vos posts</h2>
        <div id="all_post_user">
            <div id='child'>
                <?php
                    

                    
                    if($_SESSION['grade']==2){
                        $post_user = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE u.id_user=? ORDER BY p.date DESC");
                        //Compte le nombre de post dans la page menu
                        $conteur = $bdd->prepare("SELECT COUNT(*) FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE u.id_user=?");
                        $conteur->execute(Array($_SESSION['id']));
                        foreach($conteur as $test){
                            //Si aucun post n'est présent alors :
                            if($test[0]==0){
                                ?><h1 id='no_post'>Vous n'avez aucun post !</h1><?php
                            }
                        }
                    }else{
                        $post_user = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE visible=1 AND u.id_user=? ORDER BY p.date DESC");
                        //Compte le nombre de post dans la page menu
                        $conteur = $bdd->prepare("SELECT COUNT(*) FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE visible=1 AND u.id_user=?");
                        $conteur->execute(Array($_SESSION['id']));
                        foreach($conteur as $test){
                            //Si aucun post n'est présent alors :
                            if($test[0]==0){
                                ?><h1 id='no_post'>Vous n'avez aucun post !</h1><?php
                            }
                        }
                    }
                    $post_user->execute(Array($_SESSION['id'])); 



                    foreach($post_user as $posts){  
			//Compte le nombre de commentaire pour chaque post
                        $cmpt_coms = $bdd->prepare("SELECT COUNT(*) FROM comments c WHERE id_post = ?");
                        $cmpt_coms->execute(Array($posts['id_post']));
                        foreach($cmpt_coms as $cmpt_com){
                            $nbr_com = $cmpt_com[0];
                        }
                        ?>

                        <div class='menu_post' onclick='document.location.href="post.php?id=<?=$posts["id_post"]?>";'>
                            <?php if($_SESSION['grade'] == 0 && $_SESSION['id']==$posts['id_user']){?>
                                <form method='POST'>
                                    <button id='bouton_admin' type="submit" name="idADelete" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                                    <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>
                                </form>
                            <?php }?>
                            <?php if($_SESSION['grade'] == 2){?>
                                <form method='POST'>
                                    <button id='bouton_admin' type="submit" name="idADelete" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                                    <?php if($posts['visible']==1){?>
                                        <button id='bouton_admin' type="submit" name="idACacher" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/montrer.png" alt="montrer"></button>
                                    <?php }else{?>
                                        <button id='bouton_admin' type="submit" name="idAMontrer" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/cacher.png" alt="cacher"></button>
                                    <?php }?>
                                    <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$posts["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>   
                                </form>
                            <?php }?>
                            <h2 id='post_compte_text'><?=$posts['titre']?> </h2>
                            <?php if($posts["post_image"] != ''){?>
                                <img class='img_post' src="img/<?php echo $posts["post_image"] ?>" alt="image introuvable">
                            <?php }?>
                            <span class="clear"></span>
			    <p id='menu_com'><i class="far fa-comment"></i> <?=$nbr_com?></p>
                            <p>Le : <?=$posts['date']?></p>
                        </div>

                        <?php
                    }
                
                ?>
            </div>
        </div>
    </div>
