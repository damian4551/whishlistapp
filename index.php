<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/main.css">
    <link rel="icon" href="http://example.com/favicon.png">
    <script src="app.js" defer></script>
    <title>Panel główny</title>
</head>

<?php

    include_once 'classes/UserManager.php';
    include_once 'classes/Database.php';
    include_once 'classes/CatalogManager.php';
    include_once 'classes/ProductManager.php';
    include_once 'classes/LinkManager.php';

    $um = new UserManager();
    $db = new Database();
    $cm = new CatalogManager();
    $pm = new ProductManager();
    $lm = new LinkManager();

    $error_catalog = "";
    $error_product = "";

    if (filter_input(INPUT_GET, "action")) {
        $um->logoutUser($db);
    }

    //get current user_id
    session_start();
    $session_id = session_id();
    $user_id = $um->getLoggedInUser($db, $session_id);
    session_destroy();

    if ($user_id < 0) {
        header("location:login.php");
    }

    if (filter_input(INPUT_GET, "catalog")) {
        $parent_catalog = $_GET['catalog'];
    } else {
        $parent_catalog = "strona glowna";
    }

    if (filter_input(INPUT_POST, "add_catalog")) {
        $error_catalog = $cm->addCatalogToDB($db, $user_id);
    }

    if (filter_input(INPUT_POST, "delete_catalog")) {
        $cm->deleteCatalogFromDB($db, $_POST["delete_catalog"], $user_id);
    }

    if (filter_input(INPUT_POST, "add_product")) {
        $error_product = $pm->addProductToDB($db, $user_id);
    }

    if (filter_input(INPUT_POST, "delete_product")) {
        $pm->deleteProductFromDB($db, $_POST["delete_product"], $user_id);
    }

    if (filter_input(INPUT_POST, "delete_link")) {
        $lm->deletelinkFromDB($db, $_POST["delete_link"], $user_id);
    }

    $products = $pm->showAllProducts($db, $user_id, $parent_catalog);
    $catalogs = $cm->showAllCatalogs($db, $user_id, $parent_catalog);

    $catalog_names = $cm->catalogNames($db, $user_id);

?>


<body>

<div class="wrapper">
    
    <div class="inner-wrapper">
        
        <?php 

            if($error_catalog == "catalog-exists") {
                echo "<div class='error-box'>
                    <button class='close-btn'>+</button>
                    <p>Istnieje katalog o takiej nazwie</p>
                </div>";
            } 
            if($error_product == "product-exists") {
                echo "<div class='error-box'>
                    <button class='close-btn'>+</button>
                    <p>Istnieje produkt o takiej nazwie</p>
                </div>";
            }

            foreach($products as $product) {
                if (filter_input(INPUT_POST, "add_link")) {
                    $lm->addLinkToDB($db, $user_id, $product->product_id);
                }
                echo "
                <div class='modal' id='$product->product_id'>
                    <button class='close-btn'>+</button>
                    <p class='modal-title'>$product->product_name<p>
                    <div class='modal-description-block'>
                        <p class='modal-description-title'>Opis produktu<p>
                        <p>$product->product_description</p>
                    </div>
                    <div class='links'>
                        <p class='link-title'>Linki<p>
                        <ul>";
                        $links = $lm->showAllLinks($db, $user_id, $product->product_id);
                        foreach($links as $link ) {
                            echo "
                            <li class='link'>
                                <a href='$link->link_address'>$link->shop_name</a>
                                <form method='post'>
                                    <input type='hidden' value='";
                                    echo $link->link_id;
                                    echo "' name='delete_link'/>
                                    <input type='submit' value='Usuń link' class='delete-link'>
                                </form>
                            </li>
                            ";
                        }
                        echo "
                        </ul>
                    </div>
                    <form class='link-form' method='post'>
                        <input type='text' name='shop_name' placeholder='nazwa sklepu'/>
                        <input type='text' name='link_address' placeholder='adres strony'/>
                        <button type='submit' name='add_link' value='add_link'>+</button>
                    </form>
                    <form class='delete-product-form' method='post'>
                        <input type='hidden' value='";
                        echo $product->product_id;
                        echo "' name='delete_product'/>
                        <input type='submit' value='Usuń produkt' class='delete-product'>
                    </form>
                </div>
                ";
            }
        ?>
        

        <div class="catalog-form">
            <button class="close-btn">+</button>
            <form action="" method="post">
                <div class="input-row">
                    <label>nazwa katalogu</label>
                    <input name="catalog_name" type="text" required />
                </div>
                <div class="input-row">
                    <label>występowanie</label>
                    <select name="parent_catalog" required >
                        <option>strona glowna</option>
                        <?php 
                            foreach($catalog_names as $catalog) {
                                echo "
                                 <option>$catalog->catalog_name</option>
                                ";
                            }
                        ?>
                    </select>
                </div>
                <div class="btn-add-catalog">
                    <button type="submit" name="add_catalog" value="add_catalog">
                        dodaj
                    </button>
                </div>
            </form>
        </div>

        <div class="product-form">
            <button class="close-btn">+</button>
            <form action="" method="post">
                <div class="input-row">
                    <label>nazwa produktu</label>
                    <input name="product_name" type="text" required />
                </div>
                <div class="input-row">
                    <label>opis produktu</label>
                    <input name="product_description" type="text" required />
                </div>
                <div class="input-row">
                    <label>występowanie</label>
                    <select name="parent_catalog" required >
                        <option>strona glowna</option>
                        <?php 
                            foreach($catalog_names as $catalog) {
                                echo "
                                 <option>$catalog->catalog_name</option>
                                ";
                            }
                        ?>
                    </select>
                </div>
                <div class="btn-add-product">
                    <button type="submit" name="add_product" value="add_product">
                        dodaj
                    </button>
                </div>
            </form>
        </div>

        <div class="header">
            <div class="logo">
                <a href="index.php">wishlist<span>app</span></a>
            </div>
            <ul>
                <li><a href="index.php?action=logout">wyloguj</a></li>
            </ul>
        </div>


        <div class="main-container">

            <div class="catalogs-container">
                <div class="title-block">
                    katalogi
                </div>
                <div class="catalogs-list">
                    <?php 
                    if(count($catalogs) > 0) {
                        foreach($catalogs as $catalog) {
                            echo "
                            <div class='catalog'>
                                <form method='post'>
                                    <input type='hidden' value='";
                                    echo $catalog->catalog_id;
                                    echo "' name='delete_catalog'/>
                                    <input type='submit' value='+' class='delete-catalog'>
                                </form>
                                <a href='index.php?catalog=$catalog->catalog_name' class='catalog-link'><span>$catalog->catalog_name</span></a>
                            </div>
                            ";
                        }
                    } else {
                        echo "<p class='info'>Brak katalogów</p>";
                    }
                    ?>
                </div>
            </div>

            <div class="products-container">
                <div class="title-block">
                    produkty
                </div>
                <div class="products-list">
                    <?php 
                    if(count($products) > 0) {
                        foreach($products as $product) {
                            echo "
                            <div class='product' id=$product->product_id>
                                <span id=$product->product_id>$product->product_name</span>
                            </div>
                            ";
                        }
                    } else {
                        echo "<p class='info'>Brak produktów</p>";
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="bar">
            <div class="catalog-info">
                > <?php echo $parent_catalog ?>
            </div>
            <div class="options-block">
                <button class="show-options-btn">
                    +
                </button>
                <div class="options">
                    <span class="show-catalog-form">dodaj katalog</span>
                    <span class="show-product-form">dodaj produkt</span>
                </div>
            </div>
        </div>

    </div>
</div>
    
</body>
</html>