<?php

class ProductManager {

    //validate
    public function productValidation() {
        $args = [
            'product_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'product_description' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'parent_catalog' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ];

        $data = filter_input_array(INPUT_POST, $args);

        $product_array = array(
            'product_name' => $data["product_name"],
            'product_description' => $data["product_description"],
            'parent_catalog' => $data["parent_catalog"],
        );

        return $product_array;
    }

    //create
    public function addProductToDB($db, $user_id) {
        $product_array = $this->productValidation();

        $product_name = $product_array["product_name"];
        $product_description = $product_array["product_description"];
        $parent_catalog = $product_array["parent_catalog"];
        $current_date =  date("Y-m-d 00:00:00");
        
        $isProduct = $db->selectElements("SELECT * FROM products WHERE product_name = '$product_name'");

        if(count($isProduct) == 0) {
            $db->insert("INSERT INTO products (product_id, product_name, product_description, parent_catalog, created_at, user_id) VALUES (NULL, '$product_name', '$product_description', '$parent_catalog', '$current_date', '$user_id')");
        } else {
            return "product-exists";
        }

    }
    
    //delete
    public function deleteProductFromDB($db, $id, $user_id) {

        $db->delete("DELETE FROM products WHERE product_id = $id AND user_id = '$user_id'");
        $db->delete("DELETE FROM links WHERE product_id = $id AND user_id = '$user_id'");

    }

    //show
    public function showAllProducts($db, $user_id, $parent_catalog) {
        $all_products = $db->selectElements("SELECT product_id, product_name, product_description FROM products WHERE user_id = '$user_id' AND parent_catalog = '$parent_catalog' ORDER BY created_at ASC");

        return $all_products;
    }
    
}