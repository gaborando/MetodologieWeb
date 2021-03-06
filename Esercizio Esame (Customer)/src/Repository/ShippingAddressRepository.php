<?php
    
    namespace Repository;
    
    use Entity\ShippingAddress;
    
    class ShippingAddressRepository extends AbstractRepository
    {
        /**
         * @param $onwer_id
         *
         * @return ShippingAddress[]
         */
        public function findByOwnerId($owner_id)
        {
            $query = "select id, street, city, region, country, zip_code, full_name, owner_id from shipping_addresses
            where owner_id = :owner_id and deleted_at is null";
            
            $stm = $this->connection->prepare($query);
            $stm->execute(['owner_id' => $owner_id]);
            $result = [];
            while ($sa = $stm->fetchObject(ShippingAddress::class)) {
                $result[] = $sa;
            }
            
            return $result;
        }
        
        /**
         * @param $id
         * @param $owner_id
         *
         * @return ShippingAddress|null
         */
        public function findByIdAndOwner($id, $owner_id)
        {
            $query = "select id, street, city, region, country, zip_code, full_name, owner_id from shipping_addresses
            where owner_id = :owner_id and id = :id ";
            
            $stm = $this->connection->prepare($query);
            $stm->execute(['owner_id' => $owner_id, 'id' => $id]);
            
            if ($r = $stm->fetchObject(ShippingAddress::class)) {
                return $r;
            }
            
            return null;
        }
        
        public function save(ShippingAddress $shippingAddress)
        {
            
            $query = "insert into shipping_addresses (street, city, region, country, zip_code, owner_id, full_name)
            VALUES (:street, :city, :region, :country, :zip_code, :owner_id, :full_name) ";
            $stm = $this->connection->prepare($query);
            $s = $stm->execute(
                [
                    'street' => $shippingAddress->getStreet(),
                    'city' => $shippingAddress->getCity(),
                    'region' => $shippingAddress->getRegion(),
                    'country' => $shippingAddress->getCountry(),
                    'zip_code' => $shippingAddress->getZipCode(),
                    'owner_id' => $shippingAddress->getOwnerId(),
                    'full_name' => $shippingAddress->getFullName(),
                ]
            );
            
            if ($s) {
                $shippingAddress->setId($this->connection->lastInsertId('shipping_addresses'));
            }
            
            return $shippingAddress;
        }
        
        public function delete($id)
        {
            $query = "update shipping_addresses set deleted_at = CURRENT_TIMESTAMP where id = :id";
            $stm = $this->connection->prepare($query);
            
            return $stm->execute(['id' => $id]);
        }
        
    }