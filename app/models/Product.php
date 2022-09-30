<?php

namespace app\models;

use R;
use RedBeanPHP\R as RedBeanPHPR;

class Product extends AppModel
{

    public function get_product($slug, $lang): array
    {
        return RedBeanPHPR::getRow("SELECT p.*, pd.* FROM product p JOIN product_description pd on p.id = pd.product_id WHERE p.status = 1 AND p.slug = ? AND pd.language_id = ?", [$slug, $lang['id']]);
    }

    public function get_gallery($product_id):array
    {
        return RedBeanPHPR::getAll("SELECT * FROM product_gallery WHERE product_id = ?", [$product_id]);
    }


}