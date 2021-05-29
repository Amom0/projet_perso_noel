<?php
session_start();
include("header.php"); 
if($_SESSION['grade']==2){
?>
    <body>
        <h1 id='titre_newpost'>Administration</h1>


        <form method="_GET">
            <input id="menu_reherche" type="search" id="recherche" name="search" value="<?php echo $_GET['search']?>" placeholder='Rechercher...'>
            <button id='bouton_search'>rechercher</i></button>
        </form>

        <?php 


        //Selectionne tout les posts cachés
        if($_GET['search']==''){     
            $hiddenpost = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE visible=0 ORDER BY p.date DESC");
            $hiddenpost->execute(); 
            //Compte le nombre de post dans la page menu
            $conteur = $bdd->prepare("SELECT COUNT(*) FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE visible=0");
            $conteur->execute();
            foreach($conteur as $test){
                //Si aucun post n'est présent alors :
                if($test[0]==0){
                    ?><h1 id='no_post'>Aucun post caché, pour le moment...</h1><?php
                }
            }
             
        //Selectionne tout les posts cachés qui contiennent X
        }else{                                                                                     
            $hiddenpost = $bdd->prepare("SELECT * FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE (titre LIKE CONCAT('%', ?, '%') OR contenu LIKE CONCAT('%', ?, '%') OR pseudo LIKE CONCAT('%', ?, '%'))AND visible=0 ORDER BY p.date DESC");
            $hiddenpost->execute(Array($_GET['search'],$_GET['search'],$_GET['search'])); 

            //Compte le nombre de post dans le menu avec la recherche
            $conteur = $bdd->prepare("SELECT COUNT(*) FROM posts p INNER JOIN user u ON u.id_user = p.id_user  WHERE (titre LIKE CONCAT('%', ?, '%') OR contenu LIKE CONCAT('%', ?, '%') OR pseudo LIKE CONCAT('%', ?, '%'))AND visible=0");
            $conteur->execute(Array($_GET['search'],$_GET['search'],$_GET['search']));
            foreach($conteur as $test){
                //Si aucun post n'est présent alors :
                if($test[0]==0){
                    ?><h1 id='no_post'>Aucun résultat !</h1><?php
                }
            }
        }



            

            if (isset($_POST['idADelete'])) {
                $idDelete = $_POST['idADelete'];
                $get_image = $bdd->prepare("SELECT post_image FROM posts WHERE id_post=$idDelete");
                $get_image->execute();
                $delete_post = $bdd->prepare("DELETE FROM posts WHERE id_post=$idDelete");
                $delete_post->execute();
                $lien = $get_image->fetchAll();
                $fichier = 'img/'.$lien[0]['post_image'];
                unlink($fichier); 
                header('location: administration.php');
            }
            if (isset($_POST['idAMontrer'])) {
                $idShowed = $_POST['idAMontrer'];
                $show_post = $bdd->prepare("UPDATE posts SET visible=1 WHERE id_post=$idShowed");
                $show_post->execute();
                header('location: administration.php');
            }
            if (isset($_POST['idAModifier'])) {
                $idModified = 'modifier.php?id='.$_POST['idAModifier'];
                header('location:'.$idModified);
            }

            foreach($hiddenpost as $hiddenposts){
        ?>
        <div class='menu_post' onclick='document.location.href="post.php?id=<?=$hiddenposts["id_post"]?>";'>
            <form method='POST'>
                <button id='bouton_admin' type="submit" name="idADelete" value="<?=$hiddenposts["id_post"]?>"><img id='croix' src="icones/x-button.png" alt="croix"></button>
                <button id='bouton_admin' type="submit" name="idAMontrer" value="<?=$hiddenposts["id_post"]?>"><img id='croix' src="icones/cacher.png" alt="cacher"></button>
                <button id='bouton_admin' type="submit" name="idAModifier" value="<?=$hiddenposts["id_post"]?>"><img id='croix' src="icones/modifier.png" alt="editer"></button>
            </form>
            <h2 id='menu_text'><?=$hiddenposts['titre']?> </h2>
            <!-- On verifie si il existe un résumé sinon on commence à afficher le contenu -->
            <?php if($hiddenposts['resumee']!=''){?>
                <p id='menu_text'><?=nl2br($hiddenposts['resumee'])?> </p>
            <?php }elseif(strlen($hiddenposts['contenu'])<=300){?>
                <p id='menu_text'><?=nl2br($hiddenposts['contenu'])?> </p>
            <?php }else{
                $str = nl2br(substr($hiddenposts['contenu'],0,300));     
            ?>
                <p id='menu_text'><?= nl2br(substr($str,0,strrpos($str,' '))).'...'?> </p> 
            <?php }?>
            <?php if($hiddenposts["post_image"] != ''){?>
                <img class='img_post' src="img/<?php echo $hiddenposts["post_image"] ?>" alt="image introuvable">
            <?php }?>
            <span class="clear"></span>
            <p id='menu_pseudo'>Ecrit par : <?=$hiddenposts['pseudo']?></p>
            <p id='menu_date'>Le : <?=$hiddenposts['date']?></p>
        </div>
                <?php
                }
                ?>
            
        

    </body>
  


<?php
    include("footer.php"); 
    }
    else{
        header('location: menu.php');
    }
?>