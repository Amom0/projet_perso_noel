<?php
    session_start();
    include("header.php"); 
    if($_SESSION['grade']==2){
        // Boutons pour chaque utilisateur
        if (isset($_POST['iduserADelete'])) {
            $idDelete = $_POST['iduserADelete'];
            // Suppression des images de ses posts
            $get_image = $bdd->prepare("SELECT post_image FROM posts WHERE id_user=$idDelete");
            $get_image->execute();
            foreach($get_image as $delete_image){
                $fichier = 'img/'.$delete_image['post_image'];
                unlink($fichier); 
            }
            
            // Suppression de ses commentaires
            $delete_com = $bdd->prepare("DELETE FROM comments WHERE id_user=$idDelete");
            $delete_com->execute();

            // Suppression des commentaires de ses posts
            $delete_allcom = $bdd->prepare("DELETE c FROM comments c INNER JOIN posts p ON p.id_post = c.id_post WHERE p.id_user=$idDelete");
            $delete_allcom->execute();

            // Suppression de ses posts
            $delete_posts = $bdd->prepare("DELETE FROM posts WHERE id_user=$idDelete");
            $delete_posts->execute();

            // Suppression de l'utilisateur
            $delete_user = $bdd->prepare("DELETE FROM user WHERE id_user=$idDelete");
            $delete_user->execute();
            
            // Si l'utilisateur se supprime, on détruit la session
            if($_POST['iduserADelete']==$_SESSION['id']){
                session_destroy();
            }
            header('location: moderation.php');
        }

        // Promouvoir un utilisateur en admin
        if (isset($_POST['idAUpgrade'])) {
            $idUpgrade = $_POST['idAUpgrade'];
            $upgrade_user = $bdd->prepare("UPDATE user SET grade=2 WHERE id_user=$idUpgrade");
            $upgrade_user->execute();
            header('location:'.$_SERVER['REQUEST_URI']);
        }
        // Rétrogader un admin en utilisateur simple
        if (isset($_POST['idADowngrade'])) {
            $idDowngrade = $_POST['idADowngrade'];
            $downgrade_user = $bdd->prepare("UPDATE user SET grade=0 WHERE id_user=$idDowngrade");
            $downgrade_user->execute();
            header('location:'.$_SERVER['REQUEST_URI']);
        }


        // Boutons des posts des utilisateurs
        if (isset($_POST['idADelete'])) {
            $idDelete = $_POST['idADelete'];
            $get_image = $bdd->prepare("SELECT post_image FROM posts WHERE id_post=$idDelete");
            $get_image->execute();
            $delete_post = $bdd->prepare("DELETE FROM posts WHERE id_post=$idDelete");
            $delete_post->execute();
            $lien = $get_image->fetchAll();
            $fichier = 'img/'.$lien[0]['post_image'];
            unlink($fichier);
        }
        if (isset($_POST['idACacher'])) {
            $idHidden = $_POST['idACacher'];
            $hidde_post = $bdd->prepare("UPDATE posts SET visible=0 WHERE id_post=$idHidden");
            $hidde_post->execute();
        }
        if (isset($_POST['idAMontrer'])) {
            $idShowed = $_POST['idAMontrer'];
            $show_post = $bdd->prepare("UPDATE posts SET visible=1 WHERE id_post=$idShowed");
            $show_post->execute();
        }
        if (isset($_POST['idAModifier'])) {
            $idModified = 'modifier.php?id='.$_POST['idAModifier'];
            header('location:'.$idModified);
        }
        





        if($_GET['search']==''){     
            $users = $bdd->prepare("SELECT * FROM user u ORDER BY u.grade DESC");
            $users->execute();
            
             
        //Selectionne tout les posts cachés qui contiennent X
        }else{                                                                                     
            $users = $bdd->prepare("SELECT * FROM user u WHERE pseudo LIKE CONCAT('%', ?, '%') ORDER BY u.grade DESC");
            $users->execute(Array($_GET['search'])); 

            
        }

?>
<body>
    <h1 id='titre_newpost'>Modération</h1>
    
    
    <form method="_GET">
        <input id="menu_reherche" type="search" id="recherche" name="search" value="<?php echo $_GET['search']?>" placeholder='Rechercher...'>
        <button id='bouton_search'>rechercher</button>
    </form>

    
    
    <diV id='box_moderation'>
        <?php
            if($_GET['search']==''){
                //Compte le nombre d'utilisateur
                $conteur = $bdd->prepare("SELECT COUNT(*) FROM user");
                $conteur->execute();
                foreach($conteur as $test){
                    //Si aucun utilisateur n'est présent alors :
                    if($test[0]==0){
                        ?><h1 id='no_post'>Aucun utilisateur, comment avait vous fait pour être la ???</h1><?php
                    }
                }
            }else{
                //Compte le nombre d'utilisateur avec la recherche
                $conteur = $bdd->prepare("SELECT COUNT(*) FROM user u INNER JOIN posts p ON u.id_user=p.id_user WHERE pseudo LIKE CONCAT('%', ?, '%') ORDER BY u.grade DESC");
                $conteur->execute(Array($_GET['search']));
                foreach($conteur as $test){
                    //Si aucun utilisateur n'est présent alors :
                    if($test[0]==0){
                        ?><h1 id='no_post'>Aucun résultat !</h1><?php    
                    }
                }   
            }

            foreach($users as $user){
                
        ?>
                <!-- Box individuelles des posts -->

               
                <div id='box_mode_user'>
                
                    <!-- Bouton de l'admin -->
                    <?php if($_SESSION['grade'] == 2){?>
                        <form id='little_button' method='POST'>
                            <button id='bouton_admin' type="submit" name="iduserADelete" value="<?=$user["id_user"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                            <?php if($user['grade'] == 2){?>
                                <button id='bouton_admin' type="submit" name="idADowngrade" value="<?=$user["id_user"]?>"><img id='croix' src="icones/admin.png" alt="admin"></button>
                            <?php }else{?>
                                <button id='bouton_admin' type="submit" name="idAUpgrade" value="<?=$user["id_user"]?>"><img id='croix' src="icones/user.png" alt="user"></button>
                            <?php }?>
                        </form>
                    <?php }?>
                    <h1><?php echo ($user['pseudo']);?></h1>
                    
                    <i class="fas fa-angle-double-down"></i>

                     <!-- Div optionnelle qui contient les posts -->
                    <div id='additional_box'>
                        <div id='child'>
                        <?php
                            $post_user = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE u.id_user=? ORDER BY p.date DESC");
                            $post_user->execute(Array($user['id_user'])); 
                            //Compte le nombre de post dans la page menu
                            $conteur = $bdd->prepare("SELECT COUNT(*) FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE u.id_user=?");
                            $conteur->execute(Array($user['id_user']));
                            foreach($conteur as $test){
                                //Si aucun post n'est présent alors :
                                if($test[0]==0){
                                    ?><h1 id='no_post'>Aucun post !</h1><?php
                                }
                            }
                            foreach($post_user as $posts){  
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
                                <p>Le : <?=$posts['date']?></p>
                            </div>

                        <?php
                            }
                        ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        ?>
    </div>
   
    
<?php
    include("footer.php"); 
    }
    else{
        header('location: menu.php');
    }
?>  




