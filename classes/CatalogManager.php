<?php

class CatalogManager {

    //validate
    public function catalogValidation() {
        $args = [
            'catalog_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'parent_catalog' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ];

        $data = filter_input_array(INPUT_POST, $args);

        $catalog_array = array(
            'catalog_name' => $data["catalog_name"],
            'parent_catalog' => $data["parent_catalog"]
        );

        return $catalog_array;
    }

    //create
    public function addCatalogToDB($db, $user_id) {
        $catalog_array = $this->catalogValidation();

        $catalog_name = $catalog_array["catalog_name"];
        $parent_catalog = $catalog_array["parent_catalog"];
        $current_date =  date("Y-m-d 00:00:00");

        $isParentExist;
        $isCatalog = $db->selectElements("SELECT * FROM catalogs WHERE catalog_name = '$catalog_name' AND user_id = '$user_id'");

        if(count($isCatalog) > 0) {
            $parent_catalog_temp = $isCatalog[0]->parent_catalog;
            if($parent_catalog_temp != 'strona glowna') {
                $isParentExist = $db->selectElements("SELECT * FROM catalogs WHERE catalog_name = '$parent_catalog_temp' AND user_id = '$user_id'");
                if(count($isParentExist) == 0) {
                    $db->delete("DELETE FROM catalogs WHERE catalog_name = '$catalog_name' AND user_id = '$user_id'");
                }
            }
        }

        $isCatalog = $db->selectElements("SELECT * FROM catalogs WHERE catalog_name = '$catalog_name' AND user_id = '$user_id'");

        if(count($isCatalog) == 0) {
            $db->insert("INSERT INTO catalogs (catalog_id, catalog_name, created_at, parent_catalog, user_id) VALUES (NULL, '$catalog_name', '$current_date', '$parent_catalog', '$user_id')");
        } else {
            return "catalog-exists";
        }

    }
    
    //delete
    public function deleteCatalogFromDB($db, $id, $user_id) {

        $catalogs = $db->selectElements("SELECT catalog_name FROM catalogs WHERE catalog_id = '$id'");

        if(count($catalogs) > 0) {
            $catalog_name = $catalogs[0]->catalog_name;

            $products = $db->selectElements("SELECT product_id FROM products WHERE parent_catalog = '$catalog_name' AND user_id = '$user_id'");


            $db->delete("DELETE FROM catalogs WHERE catalog_id = $id AND user_id = '$user_id'");
            $db->delete("DELETE FROM catalogs WHERE parent_catalog = '$catalog_name' AND user_id = '$user_id'");
    
            if(count($catalogs) > 0) {
                $db->delete("DELETE FROM products WHERE parent_catalog = '$catalog_name' AND user_id = '$user_id'");
            }
    
            if(count($products) > 0) {
                $product_id = $products[0]->product_id;
                $db->delete("DELETE FROM links WHERE product_id = '$product_id' AND user_id = '$user_id'");
            }
        }
    }

    //show
    public function showAllCatalogs($db, $user_id, $parent_catalog) {
        $all_catalogs = $db->selectElements("SELECT catalog_id, catalog_name, parent_catalog FROM catalogs WHERE user_id = '$user_id' AND parent_catalog = '$parent_catalog' ORDER BY created_at ASC");

        return $all_catalogs;
    }

    //return catalogs name
    public function catalogNames($db, $user_id) {
        $all_names = $db->selectElements("SELECT catalog_name FROM catalogs WHERE user_id = '$user_id'");

        return $all_names;
    }
    
}