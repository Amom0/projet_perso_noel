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
?>

<body>
    <h1 id='titre_newpost'>Modifier votre post :</h1>
    <div id='box_newpost'>
        <form id="form_poster" method='POST' enctype='multipart/form-data'>
            <input id="box_titre" type="text" name="titre" value="<?php echo $lepost[0]["titre"] ?>" placeholder="*Titre" maxlength=100 required>
            <span class='clear'></span>
            <div class="parent-div">
                <?php if($lepost[0]["post_image"]!=NULL){?>
                    <button class="btn-upload">Modifier l'image</button>
                <?php }else{?>
                    <button class="btn-upload">Ajouter une image</button>
                <?php }?>
   	    <input id='btn_up' type="file" name="picture" onchange="choix_image();">
            </div>
            <p id='fichier'></p>
            <span class='clear'></span>
            <?php if($lepost[0]["post_image"]!=NULL){?>
                <h1>OU</h1>
                <span class='clear'></span>
                <div id='btn_del_parent'>
                    <button class='btn-upload'>Supprimer l'image</button> 
            	    <input id='btn_del' type='checkbox' name="delete_img" onclick='sup_image();' value="<?php echo $lepost[0]["post_image"] ?>">
                </div>
                <p id='check'></p>
            <?php }?>
            <textarea class="box_text" name="contenu" cols="80" rows="20" placeholder="*Contenu" form="form_poster" maxlength=30000 ><?php echo $lepost[0]["contenu"]?></textarea>
            <button class="bouton_submit" type="submit" name="action" value="Modifier">Modifier !</button>
        </form>
            

        <?php
            $upload_folder = "./img/";
                
            if(isset($_POST['action']) && $_POST['action'] == "Modifier"){
                if(isset($_FILES['picture']) && UPLOAD_ERR_NO_FILE != $_FILES['picture']['error']){
                    if($lepost[0]['post_image'] != NULL){
                        $fichier = 'img/'.$lepost[0]['post_image'];
                        unlink($fichier); 
                        $_randomID = uniqid();
                        move_uploaded_file($_FILES['picture']['tmp_name'],$upload_folder.$_randomID);
                    }else{
                        $_randomID = uniqid();
                        move_uploaded_file($_FILES['picture']['tmp_name'],$upload_folder.$_randomID);
                    }   
                }else{
                    if(isset($_POST['delete_img'])){
                        $_randomID = '';
                        $fichier = 'img/'.$lepost[0]['post_image'];
                        unlink($fichier); 
                    }else{
                        $_randomID = $lepost[0]['post_image'];
                    }
                }
                $titre = htmlspecialchars($_POST["titre"], ENT_QUOTES);
                $post_image = $_randomID;
                $contenu = htmlspecialchars($_POST["contenu"],ENT_QUOTES);
                $id_post = $lepost[0]["id_post"];

                $newpost = $bdd->prepare('UPDATE posts SET titre =?, post_image=?, contenu=? WHERE id_post=?');
                $newpost ->execute(Array($titre,$post_image,$contenu,$id_post));


                echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";exit();
            }
            
        ?>

            

    </div>
</body> 
