<?php


namespace Repository;


use Entity\Product;
use PDO;

class ProductRepository extends AbstractRepository implements Repository
{

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    public function update($entity)
    {
        // TODO: Implement update() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }
    
    /**
     * @param $id
     *
     * @return Product|null
     */
    public function findById($id)
    {
        $query = "
        select p.id,
            p.code,
            p.photo_url,
            p.description,
            p.name,
            p.unit_price,
            p.category,
            p.date_added,
            p.small_description,
            p.category_info,
            ifnull(count(r.id),0) as review_count,
            ifnull(avg(r.vote),0) as review_avg,
            (select count(*) from stock_units s where s.product_id = p.id and s.status = 0) as stock_count
        from products p
            left join reviews r on p.id = r.product_id
        where p.id = :id
        group by p.id;";


        $stm = $this->connection->prepare($query);
        $stm->execute(['id' => $id]);

        return $stm->fetchObject(Product::class);
    }

    public function findAll()
    {
        $query = "
        select p.id,
            p.code,
            p.photo_url,
            p.description,
            p.name,
            p.unit_price,
            p.category,
            p.date_added,
            p.small_description,
            p.category_info,
            ifnull(count(r.id),0) as review_count,
            ifnull(avg(r.vote),0) as review_avg,
            (select count(*) from stock_units s where s.product_id = p.id and s.status = 0) as stock_count
        from products p
            left join reviews r on p.id = r.product_id
        group by p.id;";

        $stm = $this->connection->prepare($query);
        $stm->execute();
        $products = [];
        while ($product = $stm->fetchObject(Product::class)) {
            $products[] = $product;
        }
        return $products;
    }


    public function findAllInWatcherList($user_id)
    {
        $query = "
        select p.id,
            p.code,
            p.photo_url,
            p.description,
            p.name,
            p.unit_price,
            p.category,
            p.date_added,
            p.small_description,
            p.category_info,
            ifnull(count(r.id),0) as review_count,
            ifnull(avg(r.vote),0) as review_avg,
            (select count(*) from stock_units s where s.product_id = p.id and s.status = 0) as stock_count
        from products p
            left join reviews r on p.id = r.product_id
        where p.id in (select product_id from product_watchers where user_id = :user_id)
        group by p.id;";

        $stm = $this->connection->prepare($query);
        $stm->execute(['user_id' => $user_id]);
        $products = [];
        while ($product = $stm->fetchObject(Product::class)) {
            $products[] = $product;
        }
        return $products;
    }

    public function search($category, $type, $search_text, $order_field, $order_direction, $page)
    {
        $query = "
        select p.id,
            p.code,
            p.photo_url,
            p.description,
            p.name,
            p.unit_price,
            p.category,
            p.date_added,
            p.small_description,
            p.category_info,
            ifnull(count(r.id),0) as review_count,
            ifnull(avg(r.vote),0) as review_avg,
            (select count(*) from stock_units s where s.product_id = p.id and s.status = 0) as stock_count
        from products p
            left join reviews r on p.id = r.product_id
        where (match(p.name, p.small_description, p.description, p.category_info) against (:search_text in boolean mode) || :search_text = '')
        and (p.date_added + interval 7 day > NOW() || 2 <> :type)
        and (p.category = :category || :category = 0)
        group by p.id
        having (review_avg >= 3 || 1 <> :type)
        order by match(name, small_description, description, category_info) against (:search_text in boolean mode) desc
        , $order_field $order_direction
        limit ".(intval($page)*18).", 18;";

        $stm = $this->connection->prepare($query);

        $search_text = str_replace(' ','* ',preg_replace('/[+\-><\(\)~*\"@]+/', ' ', trim($search_text)))."*";
        if(strlen($search_text)==1)
            $search_text = "";
        $stm->bindParam('search_text',$search_text , PDO::PARAM_STR);
        $stm->bindParam('category', $category, PDO::PARAM_INT);
        $stm->bindParam('type', $type, PDO::PARAM_INT);
        $stm->execute();
        $products = [];
        while ($product = $stm->fetchObject(Product::class)) {
            $products[] = $product;
        }
        return $products;
    }

    public function searchCount($category, $type, $search_text)
    {
        $query = "
        select count(*) from (select p.id
        from products p
            left join reviews r on p.id = r.product_id
        where (match(p.name, p.small_description, p.description, p.category_info) against (:search_text in boolean mode) || :search_text = '')
        and (p.date_added + interval 7 day > NOW() || 2 <> :type)
        and (p.category = :category || :category = 0)
        group by p.id
        having (ifnull(avg(r.vote),0) >= 3 || 1 <> :type)) as c;";

        $stm = $this->connection->prepare($query);

        $search_text = str_replace(' ','* ',preg_replace('/[+\-><\(\)~*\"@]+/', ' ', trim($search_text)))."*";
        if(strlen($search_text)==1)
            $search_text = "";
        $stm->bindParam('search_text',$search_text , PDO::PARAM_STR);
        $stm->bindParam('category', $category, PDO::PARAM_INT);
        $stm->bindParam('type', $type, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchColumn();
    }

}