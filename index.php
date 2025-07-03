<?php
    ob_start();
    session_start();

    $pageTitle = 'Home';
    include 'init.php';
?>


    <div class="body">
        <div class="body-home">
            <div class="home container">
                <div class="text">
                    <h2>Welcome to <span>Joe Store</span></h2>
                    <p> 
                        Inhere, We offer a nice deal for both traders and customers
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. 
                        Hic reprehenderit aliquid qui aut impedit, blanditiis dolores sunt laborum.
                    </p>
                </div>
            </div>
        </div>
        <div class="home-cats container">
            <?php
                $i = 1;
                foreach(getCats(10) as $cat){
                    
            ?>
            <a class="home-cat" href="categories.php?id=<?= $cat['id'] ?>">
                    <h3><?= $cat['cat_name'] ?></h3>
                    <img src="layout\images\system\gallery<?= $i++; ?>.jpg" alt="">
                    <div class="see-more">
                        <span>See more</span>
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </div>
            </a>
            <?php } ?>
        </div>
        <div class="container slider">
            <i id="prev" class="fa-solid fa-circle-chevron-left left"></i>
            <i id="next" class="fa-solid fa-circle-chevron-right right"></i>
            <div class="slider-wrapper">
                <div class="image-list">
                    <?php
                        $stmt = $conn->prepare("SELECT * FROM ITEMS Limit 10;");
                        $stmt->execute();
                        $items = $stmt->fetchAll();

                        foreach($items as $item){
                            $img = file_get_contents($item['img_dir'] . '/' .$item['img_name']);
                    ?>
                            <a href="item.php?item_id=<?= $item['item_id'] ?>">
                                <span>See More</span>
                                <img src="data:image/jpg;base64,<?= base64_encode($img) ?>" alt="">
                            </a>
                    <?php } ?>
                </div>
            </div>
            <div class="scrollbar">
                <div class="scrollbar-track">
                    <div class="scrollbar-thumb"></div>
                </div>
            </div>
        </div>
    </div>


    <?php
        include $tpl . "footer.php";
        ob_end_flush();
    ?>