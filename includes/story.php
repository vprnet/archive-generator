<div class="row top_story">
    <?php
    if (isset($post['landscape'])) {
    if ($post['landscape'] == true) {
    echo "<div class=\"col-sm-12 landscape_img\">";
    echo "<img class=\"lazy\" data_original=\"" . $post['image'] . "\"></img>";

    echo "</div>";


    }
    }
    ?>
    <div style="margin-bottom:10px;" class="col-xs-12">

        <p class="dateline"><?php echo date("m/d/y", $post['time']); ?></p>


        <p class="dateline" style="margin-top:-20px;float:right;">
           <span class ="category" ><?php
            echo $post['category'];
            ?></span></p>
        <?php if(isset($post['id'])){
            ?>
        <h3 class="title"><a href="http://vpr.net/archive/<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></h3>

       <?php
        }else{
        ?>

        <h3 class="title"><a href="<?php echo $post['file']; ?>"><?php echo $post['title']; ?></a></h3>
                <?php
        }
                if (isset($post['byline'])) {

            echo "<span class=\"dateline\">By " . $post['byline'] . "</span>";

        }?>

    </div>
    <?php
    if (isset($post['landscape'])) {
        if ($post['landscape'] == true) {

            echo " <div class=\"col-sm-12\">";

        } else {
            if (isset($post['image'])) {
                echo "<div class=\"col-sm-4 vertical_img\">";
                echo "<img class=\"lazy\" data_original=\"" . $post['image'] . "\"></img>";

                echo "</div>";
                echo " <div class=\"col-sm-8\">";
            } else {
                echo " <div class=\"col-sm-12\">";
                if (isset($post['audio'])) {
                    //include('audio.php');
                }
            }
        }
    } else {
        echo " <div class=\"col-sm-12\">";

    }
    if (isset($post['audio'])) {
        include('audio.php');
    }
    ?>

    <div class="row story_text">
        <div class="col-sm-12">
            <?php echo $post['text']; ?>
        </div>
    </div>

</div>
</div>