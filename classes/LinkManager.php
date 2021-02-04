<?php

class LinkManager {

    //validate
    public function linkValidation() {
        $args = [
            'shop_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'link_address' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ];

        $data = filter_input_array(INPUT_POST, $args);

        $link_array = array(
            'shop_name' => $data["shop_name"],
            'link_address' => $data["link_address"]
        );

        return $link_array;
    }

    //create
    public function addlinkToDB($db, $user_id, $product_id) {
        $link_array = $this->linkValidation();

        $shop_name = $link_array["shop_name"];
        $link_address = $link_array["link_address"];

        $db->insert("INSERT INTO links (link_id, user_id, product_id, shop_name, link_address) VALUES (NULL, '$user_id', '$product_id', '$shop_name', '$link_address')");
    }
    
    //delete
    public function deletelinkFromDB($db, $id, $user_id) {

        $db->delete("DELETE FROM links WHERE link_id = $id AND user_id = '$user_id'");

    }

    //show
    public function showAlllinks($db, $user_id, $product_id) {
        $all_links = $db->selectElements("SELECT link_id, shop_name, link_address FROM links WHERE user_id = '$user_id' AND product_id = '$product_id'");

        return $all_links;
    }
    
}