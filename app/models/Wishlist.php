<?php

namespace app\models;

use RedBeanPHP\R;

class Wishlist extends AppModel
{
    public function get_product($id) 
    {
        return R::getCell("SELECT id FROM product WHERE status = 1 AND id = ?", [$id]);
    }

    public function add_to_wishlist($id) 
    {
        $wishlist = self::get_wishlist_ids();
        if (!$wishlist) {
            setcookie('wishlist', $id, time() + 3600*24*7*30, '/');
        } else {
            if (!in_array($id, $wishlist)) {
                if (count($wishlist) > 5) {
                    array_shift($wishlist);
                }
                $wishlist[] = $id;
                $wishlist = implode(',', $wishlist);
                setcookie('wishlist', $wishlist, time() + 3600*24*7*30, '/');
            }
        }
    }

    public static function get_wishlist_ids(): array
    {
        $wishllist = $_COOKIE['wishlist'] ?? '';
        if ($wishllist) {
            $wishllist = explode(',', $wishllist);
        }
        if (is_array($wishllist)) {
            $wishllist = array_slice($wishllist, 0, 6);
            $wishllist = array_map('intval', $wishllist);
            return $wishllist;
        }
        return [];
    }

    public function get_wishlist_products($lang): array
    {
        $wishlist = self::get_wishlist_ids();
        if ($wishlist) {
            $wishlist = implode(',', $wishlist);
            return R::getAll("SELECT p.*, pd.* FROM product p JOIN product_description pd on p.id = pd.product_id WHERE p.status = 1 AND p.id IN ($wishlist) AND pd.language_id = ? LIMIT 6", [$lang['id']]);
        }
        return [];
    }

    public function delete_from_wishlist($id): bool
    {
        $wishlist = self::get_wishlist_ids();
        $key = array_search($id, $wishlist);
        if (false !== $key){
            unset($wishlist[$key]);
            if ($wishlist){
                $$wishlist = implode(',', $wishlist);
                setcookie('wishlist', $wishlist, time() + 3600*24*7*30, '/');
            } else {
                setcookie('wishlist', '', time()-3600, '/');
            }
            return true;
        }
        return false;
    }
}