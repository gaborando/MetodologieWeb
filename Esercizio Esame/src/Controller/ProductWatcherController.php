<?php
    
    namespace Controller;
    
    use Entity\ProductWatcher;
    use Repository\ProductWatcherRepository;
    use Repository\ProductRepository;

    class ProductWatcherController extends AbstractController
    {
    
        public function doGET()
        {
            if(!$this->getUserSession()){
                $this->redirect('action-go-back');
            }

        }
    
        public function doPOST()
        {

            if(!$this->getUserSession()){
                $this->redirect('sign-in', ['redirect_to'=> '/product.php/?id='.$this->get('product_id', -1)]);
            }
            $productRepository = new ProductRepository();
            $product = $productRepository->findById($this->get('product_id', -1));
    
            if (!$product)
                $this->render('error-404');
    
            $productWatcherRepository = new ProductWatcherRepository();
    
            $pw = new ProductWatcher();
    
            $pw->setProductId($product->getId());
            $pw->setUserId($this->getUserSession()->getId());
    
            try {
                $productWatcherRepository->save($pw);
            }catch (\PDOException $e){
                if($e->getCode() == 23000)
                    $this->render('page-product-watcher-added', ['product'=> $product]);
            }
    
            $this->render('page-product-watcher-added', ['product'=> $product]);
        }
    }